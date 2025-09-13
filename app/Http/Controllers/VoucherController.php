<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Models\Voucher;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class VoucherController extends Controller
{


    use AuthorizesRequests, ValidatesRequests;


    public function __construct()
{
    $this->middleware('permission:vouchers.list')->only(['index']);
    $this->middleware('permission:vouchers.view')->only(['show']);
    $this->middleware('permission:vouchers.create')->only(['create','store']);
    $this->middleware('permission:vouchers.update')->only(['edit','update']);
    $this->middleware('permission:vouchers.delete')->only(['destroy']);

    // bulk: allow if user can do ANY of export/revoke/delete
    $this->middleware('permission:vouchers.export|vouchers.revoke|vouchers.delete')->only(['bulk']);

    // print sheet
    $this->middleware('permission:vouchers.print')->only(['printSheet']);
}


    public function index(Request $r)
    {
        //$this->authorize('viewAny', Voucher::class);

        $status = $r->get('status');
        $device = $r->get('device_id');
        $search = $r->get('q');

        $q = Voucher::with(['device','creator'])
            ->when($status, fn($qq)=> $qq->where('status', $status))
            ->when($device, fn($qq)=> $qq->where('device_id', $device))
            ->search($search)
            ->latest();

        return view('vouchers.index', [
            'vouchers' => $q->paginate(20)->withQueryString(),
            'devices'  => Device::orderBy('name')->get(['id','name']),
            'filters'  => compact('status','device','search'),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Voucher::class);
        return view('vouchers.create', [
            'devices' => Device::orderBy('name')->get(['id','name']),
        ]);
    }

    public function store(Request $r)
    {
        $this->authorize('create', Voucher::class);

        $data = $r->validate([
            'device_id'         => ['nullable','exists:devices,id'],
            'plan'              => ['nullable','string','max:255'],
            'profile'           => ['nullable','string','max:255'],
            'duration_minutes'  => ['required','integer','min:5','max:525600'],
            'price'             => ['nullable','numeric','min:0','max:99999999.99'],
            'valid_from'        => ['nullable','date'],
            'valid_until'       => ['nullable','date','after_or_equal:valid_from'],
            'notes'             => ['nullable','string','max:2000'],

            // batch generation
            'count'             => ['nullable','integer','min:1','max:1000'],
            'code_prefix'       => ['nullable','string','max:10'],
            'code_length'       => ['nullable','integer','min:6','max:32'],
        ]);

        $count = max(1, (int)($data['count'] ?? 1));
        $codeLength = $data['code_length'] ?? 10;
        $prefix = strtoupper(trim($data['code_prefix'] ?? ''));

        unset($data['count'], $data['code_length'], $data['code_prefix']);

        $created = [];
        for ($i=0; $i<$count; $i++){
            $code = $this->uniqueCode($codeLength, $prefix);
            $created[] = Voucher::create(array_merge($data, [
                'code'       => $code,
                'created_by' => auth()->id(),
                'status'     => 'active',
            ]));
        }

        return redirect()->route('vouchers.index')
            ->with('success', $count > 1
                ? "{$count} vouchers created successfully."
                : "Voucher {$created[0]->code} created successfully.");
    }

    public function show(Voucher $voucher)
    {
        $this->authorize('view', $voucher);
        return view('vouchers.show', compact('voucher'));
    }

    public function edit(Voucher $voucher)
    {
        $this->authorize('update', $voucher);
        return view('vouchers.edit', [
            'voucher' => $voucher,
            'devices' => Device::orderBy('name')->get(['id','name']),
        ]);
    }

    public function update(Request $r, Voucher $voucher)
    {
        $this->authorize('update', $voucher);

        $data = $r->validate([
            'device_id'         => ['nullable','exists:devices,id'],
            'plan'              => ['nullable','string','max:255'],
            'profile'           => ['nullable','string','max:255'],
            'duration_minutes'  => ['required','integer','min:5','max:525600'],
            'price'             => ['nullable','numeric','min:0','max:99999999.99'],
            'status'            => ['required', Rule::in(['active','used','expired','revoked'])],
            'valid_from'        => ['nullable','date'],
            'valid_until'       => ['nullable','date','after_or_equal:valid_from'],
            'notes'             => ['nullable','string','max:2000'],
        ]);

        $voucher->update($data);
        return redirect()->route('vouchers.index')->with('success','Voucher updated.');
    }

    public function destroy(Voucher $voucher)
    {
        $this->authorize('delete', $voucher);
        $voucher->delete();
        return back()->with('success','Voucher deleted.');
    }

    public function bulk(Request $r)
    {
        $this->authorize('update', Voucher::class);

        $validated = $r->validate([
            'ids'   => ['required','array'],
            'ids.*' => ['integer','exists:vouchers,id'],
            'action'=> ['required', Rule::in(['revoke','delete','export'])],
        ]);

        $query = Voucher::whereIn('id', $validated['ids']);

        switch ($validated['action']) {
            case 'revoke':
                $count = $query->update(['status' => 'revoked']);
                return back()->with('success', "{$count} vouchers revoked.");
            case 'delete':
                $count = $query->delete();
                return back()->with('success', "{$count} vouchers deleted.");
            case 'export':
                $rows = $query->orderBy('id')->get(['code','plan','profile','duration_minutes','price','status']);
                $csv = $this->toCsv($rows);
                $filename = 'vouchers-'.now()->format('Ymd-His').'.csv';
                return response($csv)
                    ->header('Content-Type','text/csv')
                    ->header('Content-Disposition',"attachment; filename=\"{$filename}\"");
        }
        return back();
    }

    public function printSheet(Request $r)
    {
        $this->authorize('viewAny', Voucher::class);

        $ids = $r->validate([
            'ids' => ['required','array'],
            'ids.*' => ['integer','exists:vouchers,id'],
        ])['ids'];

        $vouchers = Voucher::whereIn('id',$ids)->get();
        return view('vouchers.print', compact('vouchers'));
    }

    private function uniqueCode(int $len, string $prefix=''): string
    {
        do {
            $core = strtoupper(Str::random($len));
            $code = $prefix ? ($prefix . '-' . $core) : $core;
        } while (Voucher::where('code',$code)->exists());
        return $code;
    }

    private function toCsv($rows): string
    {
        $out = fopen('php://temp','r+');
        fputcsv($out, ['code','plan','profile','duration_minutes','price','status']);
        foreach ($rows as $r) {
            fputcsv($out, [$r->code,$r->plan,$r->profile,$r->duration_minutes,$r->price,$r->status]);
        }
        rewind($out);
        return stream_get_contents($out);
    }
}