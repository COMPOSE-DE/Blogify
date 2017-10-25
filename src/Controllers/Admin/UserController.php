<?php

namespace ComposeDe\Blogify\Controllers\Admin;

use ComposeDe\Blogify\Requests\UserRequest;
use ComposeDe\Blogify\Services\BlogifyMailer;
use BlogifyRoleModel;

class UserController extends BaseController
{
    protected $users;


    protected $roles;

    /**
     * @var \ComposeDe\Blogify\Services\BlogifyMailer
     */
    protected $mail;

    /**
     * UserController constructor.
     *
     * @param \BlogifyRoleModel                         $roles
     * @param \ComposeDe\Blogify\Services\BlogifyMailer $mail
     */
    public function __construct(BlogifyRoleModel $roles, BlogifyMailer $mail) {
        parent::__construct();

        $this->users = app(config('blogify.models.user'));
        $this->roles = $roles;
        $this->mail = $mail;
    }

    public function index($trashed = false)
    {
        $data = [
            'users' => (! $trashed) ?
                    $this->users
                        ->with('roles')
                        ->paginate($this->config->items_per_page)
                    :
                    $this->users
                        ->with('roles')
                        ->onlyTrashed()
                        ->paginate($this->config->items_per_page),
            'trashed' => $trashed,
        ];

        return view('blogify::admin.users.index', $data);
    }

    public function create()
    {
        $roles = $this->roles->all();

        return view('blogify::admin.users.form', compact('roles'));
    }

    public function edit($userId)
    {
        $user = $this->users->find($userId);

        $roles = $this->roles->all();

        return view('blogify::admin.users.form', compact('roles', 'user'));
    }

    public function store(UserRequest $request)
    {
        $data = $this->createUser($request);
        $this->mail->mailPassword($data['user']->email, 'Blogify temperary password', $data);

        $this->flashSuccess($data['user']->name, 'created');

        return redirect()->route('admin.users.index');
    }
    
    public function update(UserRequest $request, $userId)
    {
        $user = $this->users->find($userId);

        $user->roles()->sync($request->get('roles'));

        $this->flashSuccess($user->name, 'updated');

        return redirect()->route('admin.users.index');
    }

    public function destroy($userId)
    {
        $user = $this->users->find($userId);
        $username = $user->name;
        $user->delete();

        $this->flashSuccess($username, 'deleted');

        return redirect()->route('admin.users.index');
    }

    public function restore($id)
    {
        $user = $this->users->withTrashed()->find($id);
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
        $user = $this->roles->find($request->role)->createUser([
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
