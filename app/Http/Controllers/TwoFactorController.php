<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

// BaconQrCode v2.0.8 (PHP 8 compatible)
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
// use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;

class TwoFactorController extends Controller
{
    public function settings(Request $request)
    {
        $user = $request->user();
        $google2fa = new Google2FA();

        // pending secret only while enabling (not saved until confirmed)
        $pendingSecret = $user->two_factor_enabled
            ? null
            : ($request->session()->get('2fa_pending_secret')
                ?? $google2fa->generateSecretKey(32));

        if ($pendingSecret) {
            $request->session()->put('2fa_pending_secret', $pendingSecret);
            $otpauth = $google2fa->getQRCodeUrl(
                config('app.name', 'Laravel'),
                $user->email,
                $pendingSecret
            );
            $qrDataUri = $this->qrCodeDataUri($otpauth);
        } else {
            $qrDataUri = null;
        }

        return view('security.two-factor', [
            'user'     => $user,
            'qr'       => $qrDataUri,
            'secret'   => $pendingSecret,
            'recovery' => $user->two_factor_recovery_codes ?? [],
        ]);
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'code' => ['required','digits:6'],
        ]);

        $user = $request->user();
        $pendingSecret = $request->session()->get('2fa_pending_secret');
        if (! $pendingSecret) {
            return back()->withErrors(['code' => 'No pending secret. Please start enabling 2FA again.']);
        }

        $google2fa = new Google2FA();

        // Allow small time drift window (2 = +-2 time steps)
        $valid = $google2fa->verifyKey($pendingSecret, $request->input('code'), 2);

        if (! $valid) {
            return back()->withErrors(['code' => 'Invalid code. Please try again.'])->withInput();
        }

        $user->two_factor_secret = Crypt::encryptString($pendingSecret);
        $user->two_factor_confirmed_at = now();
        $user->two_factor_enabled = true;
        $user->two_factor_recovery_codes = $user->two_factor_recovery_codes ?: $this->generateRecoveryCodes();
        $user->save();

        $request->session()->forget('2fa_pending_secret');

        // return redirect()->route('2fa.settings')->with('status', 'Two-factor authentication is enabled.');

        return to_route('settings.index', ['tab'=>'security'])->with('status','Two-factor authentication is enabled.');
    }

    

    public function disable(Request $request)
    {
        $request->validate([
            'code' => ['required','string','max:255'], // TOTP or recovery
        ]);

        $user = $request->user();

        if (! $user->two_factor_enabled || ! $user->two_factor_secret) {
            return to_route('settings.index', ['tab' => 'security'])
                ->withErrors(['code' => '2FA is not enabled on this account.']);
        }

        $inputCode = preg_replace('/\s+/', '', $request->input('code'));
        $secret    = Crypt::decryptString($user->two_factor_secret);

        $google2fa = new Google2FA();
        $validTotp = $google2fa->verifyKey($secret, $inputCode, 2);

        $validRecovery = false;
        if (! $validTotp && ! empty($user->two_factor_recovery_codes)) {
            $codes = $user->two_factor_recovery_codes;
            $idx   = array_search($inputCode, $codes, true);
            if ($idx !== false) {
                $validRecovery = true;
                // Invalidate the used recovery code
                unset($codes[$idx]);
                $user->two_factor_recovery_codes = array_values($codes);
            }
        }

        if (! $validTotp && ! $validRecovery) {
            return to_route('settings.index', ['tab' => 'security'])
                ->withErrors(['code' => 'Invalid code. Enter a current 6-digit code or a recovery code.'])
                ->withInput();
        }

        // Passed verification → disable 2FA
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->two_factor_enabled = false;
        $user->save();

        $request->session()->forget('two_factor_passed');

        return to_route('settings.index', ['tab' => 'security'])
            ->with('status', 'Two-factor authentication has been disabled.');
    }

    public function downloadRecovery(Request $request)
    {
        $user  = $request->user();
        $codes = $user->two_factor_recovery_codes ?? [];

        if (empty($codes)) {
            return to_route('settings.index', ['tab' => 'security'])
                ->withErrors(['recovery' => 'No recovery codes to download.']);
        }

        $content = implode(PHP_EOL, $codes) . PHP_EOL;
        return response($content, 200, [
            'Content-Type'        => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="recovery-codes.txt"',
        ]);
    }


    public function regenerateRecovery(Request $request)
    {
        $user = $request->user();

        if (! $user->two_factor_enabled) {
            return back()->withErrors(['recovery' => 'Enable 2FA first.']);
        }

        $user->two_factor_recovery_codes = $this->generateRecoveryCodes();
        $user->save();

        return back()->with('status', 'New recovery codes generated.');
    }

    public function challengeView()
    {
        return view('security.two-factor-challenge');
    }

    public function challengeVerify(Request $request)
    {
        $request->validate([
            'code' => ['required','string','max:255'],
        ]);

        $user = $request->user();

        if (! $user->two_factor_enabled || ! $user->two_factor_secret) {
            session(['two_factor_passed' => true]);
            return redirect()->intended(route('dashboard'));
        }

        $secret = Crypt::decryptString($user->two_factor_secret);
        $code   = preg_replace('/\s+/', '', $request->input('code'));

        $google2fa = new Google2FA();
        $validTotp = $google2fa->verifyKey($secret, $code, 2);

        $validRecovery = false;
        if (! $validTotp && ! empty($user->two_factor_recovery_codes)) {
            $codes = $user->two_factor_recovery_codes;
            $idx = array_search($code, $codes, true);
            if ($idx !== false) {
                $validRecovery = true;
                unset($codes[$idx]); // invalidate used code
                $user->two_factor_recovery_codes = array_values($codes);
                $user->save();
            }
        }

        if (! $validTotp && ! $validRecovery) {
            return back()->withErrors(['code' => 'Invalid 2FA or recovery code.']);
        }

        session(['two_factor_passed' => true]);
        return redirect()->intended(route('dashboard'));
    }

    private function generateRecoveryCodes(): array
    {
        return collect(range(1, 8))
            ->map(fn () => Str::upper(Str::random(10)))
            ->values()
            ->toArray();
    }

    /**
     * Generate a PNG QR Code data URI using BaconQrCode.
     * Uses Imagick if available, otherwise falls back to GD.
     */
   private function qrCodeDataUri(string $otpauth): string
{
    $renderer = new ImageRenderer(
        new RendererStyle(300),
        new SvgImageBackEnd()
    );

    $writer = new Writer($renderer);
    $svg = $writer->writeString($otpauth);

    // Inline SVG as data URI
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}
}