<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Support\Impersonation;
use App\Services\ActiveUserService;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:users.view')->only(['index','show']);
        $this->middleware('permission:users.create')->only(['create','store']);
        $this->middleware('permission:users.update')->only(['edit','update','toggleActive']);
        $this->middleware('permission:users.delete')->only(['destroy']);
        $this->middleware('permission:users.active')->only(['activeIndex']);
        $this->middleware('permission:users.impersonate')->only(['impersonate','leaveImpersonation']);
    }

    /**
     * Extra safety: ensure the given user belongs to the current owner.
     * Returns 404 to hide existence if not owned by the caller.
     */
    protected function ensureOwnership(User $user): void
    {
        if ($user->owner_id !== auth()->id()) {
            abort(404);
        }
    }

    /**
     * List ONLY users owned by the current authenticated user.
     */
    public function index(Request $r)
    {
        $ownerId = auth()->id();

        $q = User::query()
            ->with('roles')
            ->where('owner_id', $ownerId); // ← hard owner scope

        // search
        if ($s = (string) $r->get('q')) {
            $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('username', 'like', "%{$s}%");
            });
        }

        // role filter (keeps owner scope intact)
        if ($role = (string) $r->get('role')) {
            $q->whereHas('roles', fn($qr) => $qr->where('name', $role));
        }

        $users = $q->latest('id')->paginate(15);
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->pluck('name');

        return view('users.index', compact('users','roles'));
    }

    /**
     * Active users page (leave global unless your ActiveUserService supports owner scoping).
     * If you need to scope by owner, plumb ownerId into the service here.
     */
    public function activeIndex(Request $request, ActiveUserService $svc)
    {
        $type = $request->get('type'); // null|hotspot|pppoe
        $q    = trim((string)$request->get('q'));
        if (!in_array($type, [null,'hotspot','pppoe'], true)) $type = null;

        // Example if service supports owner scoping:
        // ['items'=>$items,'counts'=>$counts] = $svc->getForOwner(auth()->id(), $type, $q);
        ['items'=>$items,'counts'=>$counts] = $svc->get($type, $q);

        $perPage = (int)($request->integer('per_page') ?: 20);
        $page    = max((int)$request->integer('page', 1), 1);
        $total   = $items->count();
        $slice   = $items->slice(($page-1)*$perPage, $perPage)->values();

        return view('users.active', [
            'rows'   => $slice,
            'counts' => $counts,
            'type'   => $type,
            'q'      => $q,
            'pager'  => [
                'data'=>$slice,'total'=>$total,'per_page'=>$perPage,
                'current'=>$page,'has_more'=>($page*$perPage)<$total
            ],
        ]);
    }

    public function create()
    {
        return view('users.create', [
            'roles' => \Spatie\Permission\Models\Role::orderBy('name')->pluck('name'),
        ]);
    }

    public function store(Request $r)
    {
        $ownerId = auth()->id();

        $data = $r->validate([
            'first_name'       => ['required','string','max:255'],
            'last_name'        => ['required','string','max:255'],
            'email'            => [
                'nullable','email','max:255',
                Rule::unique('users','email')->where(fn($q)=>$q->where('owner_id', $ownerId)),
            ],
            'phone'            => ['required','string','max:50'],
            'whatsapp'         => ['nullable','string','max:50'],
            'customer_care'    => ['nullable','string','max:100'],
            'business_address' => ['nullable','string','max:1000'],
            'country'          => ['required','string','max:2'],
            'terms'            => ['nullable','boolean'],
            'password'         => ['nullable','string','min:6'],
            'roles'            => ['nullable','array'],
            'roles.*'          => ['string','exists:roles,name'],
            'username'         => [
                'nullable','string','max:255',
                Rule::unique('users','username')->where(fn($q)=>$q->where('owner_id', $ownerId)),
            ],
        ]);

        $fullName = trim(($data['first_name'] ?? '').' '.($data['last_name'] ?? ''));
        $password = $data['password'] ?? 'password';
        unset($data['password']);

        /** @var \App\Models\User $user */
        $user = User::create(array_merge($data, [
            'owner_id' => $ownerId,
            'name'     => $fullName,
            'password' => bcrypt($password),
            'is_active'=> true,
        ]));

        if (!empty($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return redirect()->route('users.index')->with('ok', 'User created.');
    }

    public function show(User $user)
    {
        $this->ensureOwnership($user);
        $user->load('roles');
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->ensureOwnership($user);
        $user->load('roles');

        return view('users.edit', [
            'user'  => $user,
            'roles' => \Spatie\Permission\Models\Role::orderBy('name')->pluck('name'),
        ]);
    }

    public function update(Request $r, User $user)
    {
        $this->ensureOwnership($user);
        $ownerId = $user->owner_id ?? auth()->id(); // keep scope stable

        $data = $r->validate([
            'first_name'       => ['required','string','max:255'],
            'last_name'        => ['required','string','max:255'],
            'email'            => [
                'nullable','email','max:255',
                Rule::unique('users','email')
                    ->where(fn($q)=>$q->where('owner_id', $ownerId))
                    ->ignore($user->id),
            ],
            'phone'            => ['required','string','max:50'],
            'whatsapp'         => ['nullable','string','max:50'],
            'customer_care'    => ['nullable','string','max:100'],
            'business_address' => ['nullable','string','max:1000'],
            'country'          => ['required','string','max:2'],
            'terms'            => ['nullable','boolean'],
            'password'         => ['nullable','string','min:6'],
            'roles'            => ['nullable','array'],
            'roles.*'          => ['string','exists:roles,name'],
            'username'         => [
                'nullable','string','max:255',
                Rule::unique('users','username')
                    ->where(fn($q)=>$q->where('owner_id', $ownerId))
                    ->ignore($user->id),
            ],
        ]);

        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }
        unset($data['password']);

        // keep legacy `name` in sync
        $data['name'] = trim(($data['first_name'] ?? $user->first_name).' '.($data['last_name'] ?? $user->last_name));

        $user->fill($data)->save();

        if ($r->has('roles')) {
            $user->syncRoles($data['roles'] ?? []);
        }

        return redirect()->route('users.show', $user)->with('ok', 'User updated.');
    }

    public function destroy(User $user)
    {
        $this->ensureOwnership($user);

        if (auth()->id() === $user->id) {
            return back()->with('err', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('ok', 'User deleted.');
    }

    public function toggleActive(User $user)
    {
        $this->ensureOwnership($user);

        if ($user->id === auth()->id()) {
            return back()->with('err', 'You cannot deactivate your own account.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        return back()->with('ok', 'Account status updated.');
    }

    public function impersonate(Request $request, User $user)
    {
        $this->ensureOwnership($user);

        if ($user->id === auth()->id()) {
            return back()->with('err', 'You cannot impersonate yourself.');
        }
        if (!$user->is_active) {
            return back()->with('err', 'Cannot impersonate an inactive account.');
        }
        // Optional: protect admin accounts
        if ($user->hasRole('admin') && !auth()->user()->hasRole('admin')) {
            abort(403, 'You cannot impersonate an admin.');
        }

        Impersonation::start(auth()->id());

        Auth::loginUsingId($user->id);
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('ok', 'You are now impersonating '.$user->name.'.');
    }

    public function leaveImpersonation(Request $request)
    {
        $impersonatorId = Impersonation::impersonatorId();
        if (!$impersonatorId) {
            return back()->with('err', 'Not currently impersonating.');
        }

        Auth::loginUsingId($impersonatorId);
        Impersonation::stop();
        $request->session()->regenerate();

        return redirect()
            ->route('users.index')
            ->with('ok', 'Returned to your account.');
    }
}
