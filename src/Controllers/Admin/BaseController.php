<?php

namespace ComposeDe\Blogify\Controllers\Admin;

use ComposeDe\Blogify\Controllers\Admin;

class BaseController extends Controller
{
    protected $user;
    protected $config;

    public function __construct()
    {
        $this->config = objectify(config('blogify'));

        $this->middleware(function ($request, $next) {
            $this->user = $request->user();
            view()->share('_user', $this->user);

            return $next($request);
        });
    }

    protected function flashSuccess($name, $action, $model = '')
    {
        $message = trans('blogify::notify.success', [
            'model' => $model, 'name' => $name, 'action' => $action
        ]);
        session()->flash('notify', ['success', $message]);
    }
}
