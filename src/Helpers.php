<?php

if (! function_exists('blogify'))
{
    /**
     * Get the Blogify binding
     *
     * @return \Illuminate\Foundation\Application|mixed
     */
    function blogify()
    {
        return app('ComposeDe.blogify');
    }
}

if (! function_exists('objectify') )
{
    /**
     * Make an object of the given var
     *
     * @param $var
     * @return mixed
     */
    function objectify($var)
    {
        return json_decode(json_encode($var));
    }
}

if (! function_exists('optionSelected')) {
    function optionSelected($condition)
    {
        return $condition ? 'selected' : '';
    }
}
