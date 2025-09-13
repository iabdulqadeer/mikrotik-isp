<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProvisioningController extends Controller
{
    public function rsc(Request $r): Response
    {
        $token = (string) $r->query('token');
        abort_unless($token, 404, 'missing token');

        $device = Device::where('provision_token', $token)->firstOrFail();

        // Decide ports based on your device record
        $apiPort    = $device->ssl ? 8729 : 8728;
        $apiService = $device->ssl ? 'api-ssl' : 'api';

        // Avoid quotes issues in identity
        $identity = str_replace('"', '\"', $device->name ?: 'MikroTik');

        $script = <<<RSC
# --- MikroTik ISP Manager bootstrap ---
/ip service set {$apiService} disabled=no port={$apiPort}
/system identity set name="{$identity}"

# (Optional) Create API user (change permissions!)
# /user add name="{$device->username}" password="CHANGE_ME" group=full disabled=no

# (Optional) Schedule heartbeat to your app (adjust URL)
# /system scheduler add name="hb" interval=5m on-event="/tool fetch mode=https http-method=post http-header-field=\"Content-Type: application/json\" url=\"{$r->getSchemeAndHttpHost()}/api/device/heartbeat?token={$token}\" http-data=\"{\\\"identity\\\":\\\"[/system identity get name]\\\",\\\"time\\\":\\\"[/system clock get time]\\\"}\" keep-result=no"

# Done.
RSC;

        // Return as plain text; no BOM, no leading whitespace
        return response($script, 200)
            ->header('Content-Type', 'text/plain; charset=utf-8')
            ->header('Content-Disposition', 'inline; filename="provision.rsc"');
    }
}
