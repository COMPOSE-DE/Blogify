<?php

namespace Donatix\Blogify\Controllers\Admin;

use Donatix\Blogify\Requests\LoginRequest;
use Illuminate\Contracts\Auth\Guard;

class AuthController extends BaseController
{

    /**
     * Show the login view
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('blogify::admin.auth.login');
    }

    /**
     * @param \Donatix\Blogify\Requests\LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $this->auth->attempt([
            'email' => $request->email,
            'password' => $request->password
        ], isset($request->rememberme) ? true : false);

        if ($credentials) {
            return redirect('/admin');
        }

        session()->flash('message', 'Wrong credentials');

        return redirect()->route('admin.login');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        $this->auth->logout();

        return redirect()->route('admin.login');
    }
}
