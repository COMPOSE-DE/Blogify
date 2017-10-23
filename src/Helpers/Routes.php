<?php

namespace ComposeDe\Helpers;


use Illuminate\Routing\Router;

class Routes
{
    public static function getSimpleRoutes()
    {
        $router = app(Router::class);

        $routes = collect($router->getRoutes())->keyBy(function ($route) {
            return $route->getName();
        })->map(function($route) {
            return $route->uri();
        })->all();

        return $routes;
    }


    public static function getAdminRoutePrefix()
    {
        return config('blogify.blog_admin_route_prefix', 'admin');
    }
}