<?php

return [

    /**
     * Specify your own models, if you wish
     */
    
    'models' => [
        //The model used for authentication and/or your users.
        'auth' => \App\User::class,
        'category' => \ComposeDe\Blogify\Models\Category::class,
        'comment' => \ComposeDe\Blogify\Models\Comment::class,
        'media' => \ComposeDe\Blogify\Models\Media::class,
        'post' => \ComposeDe\Blogify\Models\Post::class,
        'role' => \ComposeDe\Blogify\Models\Role::class,
        'status' => \ComposeDe\Blogify\Models\Status::class,
        'tag' => \ComposeDe\Blogify\Models\Tag::class,
        'visibility' => \ComposeDe\Blogify\Models\Visibility::class,
    ],

    'tables' => [
        'users' => 'blogify_user',
        'categories' => 'blogify_category',
        'comments' => 'blogify_comment',
        'media' => 'blogify_media',
        'posts' => 'blogify_post',
        'post_tag' => 'blogify_post_tag',
        'roles' => 'blogify_role',
        'statuses' => 'blogify_status',
        'tags' => 'blogify_tag',
        'visibility' => 'blogify_visibility',
    ],

    /**
     * Other models
     */

    /**
     * Array with all available character sets
     *
     */
    'char_sets' => [
        'hash'      => 'ABCDEFGHIJKLMNOPQRSTUVWabcdefghijklmnopqrstuvw0123456789',
        'password'  => 'ABCDEFGHIJKLMNOPQRSTUVWabcdefghijklmnopqrstuvw0123456789@$?!',
    ],

    /**
     * Information about the admin user
     * This will be used to seed the users table
     */
    'admin_user'     => [
        'name'          => 'Admin',
        'email'         => env('BLOGIFY_ADMIN_EMAIL', 'admin@example.com'),
        'password'      => env('BLOGIFY_ADMIN_PASSWORD', 'secret'),
    ],

    /**
     * Defines how many items per
     * page you want to show
     *
     */
    'items_per_page' => 20,

    /**
     * Defines how much time the config items
     * retrived from the database to be cached.
     * The value is in minutes.
     * Such tables are visibilities, statuses ...
     */
    'config_items_cache_time' => 60 * 24,

    /**
     * Paths where uploaded images
     * wil be placed
     *
     */
    'upload_paths'  => [
        'posts' => [
            'images'    => 'uploads/posts/',
        ],
        'profiles' => [
            'profilepictures' => 'uploads/profilepictures/',
        ],
    ],

    /**
     * The size where an uploaded
     * image will be resized to
     *
     */
    'image_sizes'   => [
        'posts' => [500, null],
        'profilepictures' => [100, 100],
    ],

    /**
     * A class implementing Illuminate\Contracts\Auth\Guard
     * It will be used by blogify for simple authentication functionality.
     */
    'auth_wrapper' => \ComposeDe\Helpers\BlogifyAuth::class,
    
    /**
     * Define if new comments
     * first needs approval
     *
     */
    'approve_comments_first' => true,

    /**
     * Set this to true when you have ran the
     * blogify:generate command to enable the
     * routes of the public part
     *
     */
    'enable_default_routes' => true,

    /**
     * The route prefixes for the blog and the blog administration.
     */
    'blog_route_prefix' => 'blog',
    'blog_admin_route_prefix' => 'blog_admin',

    /**
     * If should send emails to reviewers of
     * posts in the blog
     */
    'notify_reviewers' => env('BLOGIFY_NOTIFY_REVIEWERS', false),
];
