<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSystemUserRequest;
use App\Http\Requests\Admin\UpdateSystemUserRequest;
use App\Http\Requests\Admin\ChangePasswordRequest;
use App\Models\User;
use App\Models\InternetProfile; // optional if you use it
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class SystemUserController extends Controller
{


public function __construct()
    {
        // Read/list/show users
        $this->middleware('permission:users.view')->only(['index','show']);

        // Create users
        $this->middleware('permission:users.create')->only(['create','store']);

        // Edit/update users (profile fields, role assignment, etc.)
        $this->middleware('permission:users.edit')->only(['edit','update']);

        // Change password (separate permission, aligns with the modal button)
        // If your method is named differently (e.g. updatePassword), add it here.
        $this->middleware('permission:users.password')->only(['password']);

        // Generate API token (separate permission, aligns with the modal button)
        // If your method is named differently (e.g. generateToken), add it here.
        $this->middleware('permission:users.token')->only(['token']);

        // Delete user (if you expose this)
        $this->middleware('permission:users.delete')->only(['destroy']);
    }


public function index(Request $r)
{
    $this->authorize('viewAny', User::class);

    $active = $r->string('tab')->toString() ?: 'all';
    $q   = trim($r->get('q',''));

    // define tab icons
    $iconAll  = 'M4 6h16M4 12h16M4 18h16';
    $iconRole = 'M12 3v18m9-9H3'; // you can adjust per role

    $tabs = [
        'all' => ['label' => 'All Users', 'icon' => $iconAll],
    ];

    foreach (Role::orderBy('name')->get() as $role) {
        $tabs[$role->name] = [
            'label' => ucfirst($role->name),
            'icon'  => $iconRole,
        ];
    }

    $users = User::query()
      ->when($q, fn($x)=>$x->where(fn($y)=>$y
          ->where('name','like',"%$q%")
          ->orWhere('email','like',"%$q%")
          ->orWhere('phone','like',"%$q%")
          ->orWhere('username','like',"%$q%")))
      ->when($active !== 'all', fn($x)=>$x->whereHas('roles', fn($q2)=>$q2->where('name',$active)))
      ->with('roles:id,name')
      ->orderBy('name')
      ->paginate(10)
      ->withQueryString();

    return view('system-users.index', [
      'users'  => $users,
      'tabs'   => $tabs,
      'active' => $active,
      'q'      => $q,
    ]);
}


 public function create()
{
    $this->authorize('create', User::class);
    $profiles = InternetProfile::query()->orderBy('name')->get(['id','name']);
    $roles = Role::query()->orderBy('name')->get(['id','name']);   // ← NEW
    return view('system-users.create', compact('profiles','roles'));
}

  public function store(StoreSystemUserRequest $req)
{
    $data = $req->validated();

    DB::transaction(function () use ($data, $req) {
        $user = new User();
        $user->name  = trim($data['first_name'].' '.$data['last_name']);
        $user->first_name = $data['first_name'];
        $user->last_name  = $data['last_name'];
        $user->username   = $data['username'];
        $user->email      = $data['email'];
        $user->phone      = $data['phone'];
        $user->internet_profile_id = $data['internet_profile_id'] ?? null;
        $user->password   = Hash::make($data['password']);
        $user->email_verified_at = now(); // optional
        $user->save();

        // Assign role from DB by id
        $role = Role::findOrFail($data['role_id']);
        $user->syncRoles([$role->name]);

        // optionally SMS creds (stubbed)
        if ($req->boolean('send_sms')) {
            // $user->notify(new SendLoginCredentialsNotification($data['username'], $data['password']));
            session()->flash('status', 'User created and SMS queued.');
        }
    });

    return redirect()->route('systemusers.index')->with('success','User created successfully.');
}


  public function show(User $user)
  {
    $this->authorize('view', $user);
    return view('system-users.show', compact('user'));
  }

  public function edit(User $user)
{
    $this->authorize('update', $user);
    $profiles = InternetProfile::query()->orderBy('name')->get(['id','name']);
    $roles = Role::query()->orderBy('name')->get(['id','name']);   // ← NEW
    return view('system-users.edit', compact('user','profiles','roles'));
}

  public function update(UpdateSystemUserRequest $req, User $user)
{
    $data = $req->validated();

    DB::transaction(function () use ($data, $user) {
        $user->name  = trim($data['first_name'].' '.$data['last_name']);
        $user->first_name = $data['first_name'];
        $user->last_name  = $data['last_name'];
        $user->username   = $data['username'];
        $user->email      = $data['email'];
        $user->phone      = $data['phone'];
        $user->internet_profile_id = $data['internet_profile_id'] ?? null;
        $user->save();

        // Sync role from DB by id
        $role = Role::findOrFail($data['role_id']);
        $user->syncRoles([$role->name]);
    });

    return redirect()->route('systemusers.index')->with('success','User updated.');
}

  public function changePassword(Request $req, User $user)
{
    $this->authorize('update', $user);

    $data = $req->validate([
        'password' => ['required','string','min:8','confirmed'],
    ]);

    $user->update(['password' => Hash::make($data['password'])]);

    return back()->with('success','Password changed successfully.');
}

  public function generateToken(Request $req, User $user)
    {
        $this->authorize('update', $user);

        $plain = $user->createToken('System Access - '.now()->format('Y-m-d H:i'))->plainTextToken;

        return back()->with('token_plain', $plain)->with('success', 'API token generated.');
    }

}