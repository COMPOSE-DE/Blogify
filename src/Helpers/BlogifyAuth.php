<?php

namespace ComposeDe\Helpers;


use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;


class BlogifyAuth implements Guard
{
    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return Auth::check();
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return Auth::guest();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        return Auth::user();
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|null
     */
    public function id()
    {
        return Auth::id();
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return Auth::validate($credentials);
    }

    /**
     * Set the current user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return void
     */
    public function setUser(Authenticatable $user)
    {
        Auth::setUser($user);
    }
}