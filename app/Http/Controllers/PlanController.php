<?php
namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use App\Http\Requests\PlanStoreRequest;
use App\Http\Requests\PlanUpdateRequest;

class PlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:plans.view')->only(['index','show']);
        $this->middleware('permission:plans.create')->only(['create','store']);
        $this->middleware('permission:plans.update')->only(['edit','update']);
        $this->middleware('permission:plans.delete')->only(['destroy']);
    }

    /** Ensure the plan belongs to the current owner (404 if not) */
    protected function ensureOwnership(Plan $plan): void
    {
        if ($plan->owner_id !== auth()->id()) {
            abort(404);
        }
    }

    public function index(Request $r)
    {
        $ownerId = auth()->id();

        $q      = $r->string('q')->toString();
        $cycle  = $r->string('billing_cycle')->toString();
        $active = $r->string('active')->toString(); // '1','0', or ''
        $sort   = $r->string('sort','created_at')->toString(); // name|price|created_at
        $dir    = $r->string('dir','desc')->toString();        // asc|desc

        $plans = Plan::query()
            ->where('owner_id', $ownerId)          // 🔑 owner scope
            ->search($q)
            ->cycle($cycle)
            ->activeState($active)
            ->when(in_array($sort,['name','price','created_at'], true),
                   fn($qq)=>$qq->orderBy($sort, $dir==='asc'?'asc':'desc'))
            ->paginate(15)
            ->withQueryString();

        return view('plans.index', [
            'plans'   => $plans,
            'filters' => compact('q','cycle','active','sort','dir'),
            'cycles'  => ['daily','weekly','monthly'],
        ]);
    }

    public function create()
    {
        return view('plans.create');
    }

    public function store(PlanStoreRequest $r)
    {
        $data = $r->validated();
        $data['owner_id'] = auth()->id();          // 🔑 stamp owner
        $data['active']   = (bool)($data['active'] ?? true);

        Plan::create($data);

        return redirect()->route('plans.index')->with('ok', 'Plan created.');
    }

    public function show(Plan $plan)
    {
        $this->ensureOwnership($plan);
        return view('plans.show', compact('plan'));
    }

    public function edit(Plan $plan)
    {
        $this->ensureOwnership($plan);
        return view('plans.edit', compact('plan'));
    }

    public function update(PlanUpdateRequest $r, Plan $plan)
    {
        $this->ensureOwnership($plan);

        $data = $r->validated();
        $data['active'] = (bool)($data['active'] ?? false);

        $plan->update($data);

        return redirect()->route('plans.index')->with('ok', 'Plan updated.');
    }

    public function destroy(Plan $plan)
    {
        $this->ensureOwnership($plan);
        $plan->delete();

        return redirect()->route('plans.index')->with('ok', 'Plan deleted.');
    }
}
