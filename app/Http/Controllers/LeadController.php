<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\Lead;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadController extends Controller
{
    public function __construct()
    {
        // Permissions
        $this->middleware('permission:leads.view')->only(['index','show']);
        $this->middleware('permission:leads.create')->only(['create','store']);
        $this->middleware('permission:leads.edit')->only(['edit','update']);
        $this->middleware('permission:leads.delete')->only(['destroy']);
        $this->middleware('permission:leads.export')->only(['export']);
        $this->middleware('permission:leads.bulk_delete')->only(['bulkDestroy']);
    }

    /** guard: lead must belong to current owner */
    protected function ensureOwnership(Lead $lead): void
    {
        if ($lead->owner_id !== auth()->id()) {
            abort(404);
        }
    }

    public function index(Request $request)
    {
        $ownerId = auth()->id();

        $q    = $request->string('q')->toString();
        $sort = $request->string('sort')->toString();
        $dir  = $request->string('dir')->toString();

        $leads = Lead::query()
            ->where('owner_id', $ownerId) // 🔑 scope
            ->search($q)
            ->when($request->filled('status'), fn($qq) => $qq->where('status',$request->status))
            ->sort($sort, $dir)
            ->paginate(15)
            ->withQueryString();

        return view('leads.index', [
            'leads' => $leads,
            'q'     => $q,
            'sort'  => $sort ?: 'created_at',
            'dir'   => $dir ?: 'desc',
            'status'=> $request->string('status')->toString(),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Lead::class);
        return view('leads.create');
    }

    public function store(StoreLeadRequest $request)
    {
        $lead = Lead::create(array_merge(
            $request->validated(),
            ['owner_id' => auth()->id()] // 🔑 stamp
        ));

        return redirect()->route('leads.edit', $lead)->with('status','Lead created successfully.');
    }

    public function show(Lead $lead)
    {
        $this->ensureOwnership($lead);
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $this->ensureOwnership($lead);
        return view('leads.edit', compact('lead'));
    }

    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        $this->ensureOwnership($lead);
        $lead->update($request->validated());
        return back()->with('status','Lead updated.');
    }

    public function destroy(Lead $lead)
    {
        $this->ensureOwnership($lead);
        $lead->delete();
        return redirect()->route('leads.index')->with('status','Lead deleted.');
    }

    public function bulkDestroy(Request $request)
    {
        $this->authorize('delete', Lead::class);

        $ids = $request->input('ids', []);
        if ($ids) {
            Lead::where('owner_id', auth()->id()) // 🔑 scope
                ->whereIn('id', $ids)
                ->delete();
        }

        return back()->with('status','Selected leads deleted.');
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('export', Lead::class);

        $rows = Lead::query()
            ->where('owner_id', auth()->id()) // 🔑 scope
            ->search($request->string('q')->toString())
            ->get(['id','name','email','phone','company','status','source','city','country','created_at']);

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=leads_export_'.now()->format('Ymd_His').'.csv',
        ];

        return response()->stream(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID','Name','Email','Phone','Company','Status','Source','City','Country','Created At']);
            foreach ($rows as $r) fputcsv($out, $r->toArray());
            fclose($out);
        }, 200, $headers);
    }
}
