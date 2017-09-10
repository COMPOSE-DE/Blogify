<?php

namespace Donatix\Blogify\Controllers\Admin;

use Donatix\Blogify\Controllers\Admin;

class BaseController extends Controller
{
    protected $user;
    protected $auth_user;

    protected $config;

    public function __construct()
    {
        $this->config = objectify(config('blogify'));

        $this->middleware(function ($request, $next) {
            $this->user = $request->user();
            view()->share('_user', $this->user);

            // should be removed
            $this->auth_user = $this->user;

            return $next($request);
        });
    }
}
