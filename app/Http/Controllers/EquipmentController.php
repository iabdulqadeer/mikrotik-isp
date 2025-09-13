<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\User;
use App\Enums\EquipmentType;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEquipmentRequest;
use App\Http\Requests\UpdateEquipmentRequest;

class EquipmentController extends Controller
{
    public function __construct()
    {   
        // $this->authorizeResource(Equipment::class, 'equipment');

        $this->middleware('permission:equipment.view')->only(['index','show']);
        $this->middleware('permission:equipment.create')->only(['create','store']);
        $this->middleware('permission:equipment.edit')->only(['edit','update']);
        $this->middleware('permission:equipment.delete')->only(['destroy']);

    }

    public function index(Request $req)
    {
        $q     = trim((string)$req->get('q'));
        $type  = $req->get('type');
        $sort  = in_array($req->get('sort'), ['user','type','name','price','paid_amount']) ? $req->get('sort') : 'id';
        $dir   = $req->get('dir') === 'asc' ? 'asc' : 'desc';

        $items = Equipment::with('user')
            ->search($q)
            ->filterType($type)
            ->when($sort==='user', fn($qq)=>$qq->join('users','users.id','=','equipment.user_id')->select('equipment.*')->orderBy('users.name', $dir))
            ->when($sort!=='user', fn($qq)=>$qq->orderBy($sort,$dir))
            ->paginate(12)->withQueryString();

        return view('equipment.index', [
            'items'=>$items,
            'q'=>$q,
            'type'=>$type,
            'types'=>EquipmentType::options(),
            'sort'=>$sort,
            'dir'=>$dir,
        ]);
    }

    public function create()
    {
        return view('equipment.create', [
            'types' => EquipmentType::options(),
            'users' => User::orderBy('name')->get(['id','name','email']),
            'defaultCurrency' => config('app.currency','USD'),
        ]);
    }

    public function store(StoreEquipmentRequest $req)
    {
        $data = $req->validated();
        if (empty($data['paid_amount'])) $data['paid_amount'] = null;
        Equipment::create($data);

        return redirect()->route('equipment.index')->with('status','Equipment created successfully.');
    }

    public function show(Equipment $equipment)
    {
        return view('equipment.show', ['e'=>$equipment->load('user')]);
    }

    public function edit(Equipment $equipment)
    {
        return view('equipment.edit', [
            'e'=>$equipment,
            'types'=>EquipmentType::options(),
            'users'=>User::orderBy('name')->get(['id','name','email']),
        ]);
    }

    public function update(UpdateEquipmentRequest $req, Equipment $equipment)
    {
        $data = $req->validated();
        if (empty($data['paid_amount'])) $data['paid_amount'] = null;
        $equipment->update($data);

        return redirect()->route('equipment.index')->with('status','Equipment updated.');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();
        return redirect()->route('equipment.index')->with('status','Equipment deleted.');
    }
}
