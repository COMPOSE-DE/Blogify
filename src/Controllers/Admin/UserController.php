<?php

namespace ComposeDe\Blogify\Controllers\Admin;

use ComposeDe\Blogify\Blogify;
use ComposeDe\Blogify\Models\Role;
use ComposeDe\Blogify\Requests\UserRequest;
use App\User;
use Illuminate\Contracts\Hashing\Hasher as Hash;
use ComposeDe\Blogify\Services\BlogifyMailer;

class UserController extends BaseController
{

    /**
     * @var \App\User
     */
    protected $user;

    /**
     * @var \ComposeDe\Blogify\Models\Role
     */
    protected $role;

    /**
     * @var \ComposeDe\Blogify\Services\BlogifyMailer
     */
    protected $mail;

    public function __construct(User $user, Role $role, BlogifyMailer $mail) {
        parent::__construct();

        $this->user = $user;
        $this->role = $role;
        $this->mail = $mail;
    }

    public function index($trashed = false)
    {
        $data = [
            'users' => (! $trashed) ?
                    $this->user
                        ->with('role')
                        ->paginate($this->config->items_per_page)
                    :
                    $this->user
                        ->with('role')
                        ->onlyTrashed()
                        ->paginate($this->config->items_per_page),
            'trashed' => $trashed,
        ];

        return view('blogify::admin.users.index', $data);
    }

    public function create()
    {
        $roles = $this->role->all();

        return view('blogify::admin.users.form', compact('roles'));
    }

    public function edit(User $user)
    {
        $roles = $this->role->all();

        return view('blogify::admin.users.form', compact('roles', 'user'));
    }

    public function store(UserRequest $request)
    {
        $data = $this->createUser($request);
        $this->mail->mailPassword($data['user']->email, 'Blogify temperary password', $data);

        $this->flashSuccess($data['user']->name, 'created');

        return redirect()->route('admin.users.index');
    }

    public function update(UserRequest $request, User $user)
    {
        $user->role_id = $request->get('role');
        $user->save();

        $this->flashSuccess($user->name, 'updated');

        return redirect()->route('admin.users.index');
    }

    public function destroy(User $user)
    {
        $username = $user->name;
        $user->delete();

        $this->flashSuccess($username, 'deleted');

        return redirect()->route('admin.users.index');
    }

    public function restore($id)
    {
        $user = $this->user->withTrashed()->find($id);
        $user->restore();

        $this->flashSuccess($user->name, 'restored');

        return redirect()->route('admin.users.index');
    }

    ///////////////////////////////////////////////////////////////////////////
    // Helper methods
    ///////////////////////////////////////////////////////////////////////////

    /**
     * @param \ComposeDe\Blogify\Requests\UserRequest $data
     * @param string $id
     * @return array
     */
    private function createUser($request)
    {
        $password = str_random(8);
        $user = Role::find($request->role)->createUser([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($password),
        ]);

        return compact('user', 'password');
    }

    protected function flashSuccess($name, $action, $model = '')
    {
        parent::flashSuccess($name, $action, 'User');
    }
}
