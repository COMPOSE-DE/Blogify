<?php

// All default package route will be defined here

///////////////////////////////////////////////////////////////////////////
// public routes
///////////////////////////////////////////////////////////////////////////
$use_default_routes = config('blogify.enable_default_routes');

if ($use_default_routes) {
    Route::group(['prefix' => config('blogify.blog_route_prefix'), 'namespace' => 'App\Http\Controllers', 'middleware' => 'web'], function() {
        Route::get('/', [
            'as' => 'blog.index',
            'uses' => 'BlogController@index'
        ]);
        Route::get('/{slug}', [
            'as' => 'blog.show',
            'uses' => 'BlogController@show'
        ]);
        Route::post('{slug}', [
            'as' => 'blog.confirmPass',
            'uses' => 'BlogController@show',
        ]);
        Route::get('archive/{year}/{month}', [
            'as' => 'blog.archive',
            'uses' => 'BlogController@archive'
        ]);
        Route::get('category/{category}', [
            'as' => 'blog.category',
            'uses' => 'BlogController@category',
        ]);
        Route::get('protected/verify/{hash}', [
            'as' => 'blog.askPassword',
            'uses' => 'BlogController@askPassword'
        ]);
        Route::post('comments', [
            'as' => 'comments.store',
            'uses' => 'CommentsController@store'
        ]);
    });
}

///////////////////////////////////////////////////////////////////////////
// Admin routes
///////////////////////////////////////////////////////////////////////////

$admin = [
    'prefix' => config('blogify.blog_admin_route_prefix'),
    'namespace' => 'ComposeDe\Blogify\Controllers\Admin',
    'middleware' => ['web', 'auth'],
];

Route::group($admin, function()
{
    Route::group(['middleware' => 'BlogifyAdminAuthenticate'], function()
    {
        // Dashboard
        Route::get('/', [
            'as'    => 'admin.dashboard',
            'uses'  => 'DashboardController@index'
        ]);

        /**
         * User routes
         *
         */
        Route::group(['middleware' => 'HasAdminRole'], function() {
            Route::resource('users', 'UserController', [
                'names' => 'admin.users'
            ]);
            Route::get('users/overview/{trashed?}', [
                'as' => 'admin.users.overview',
                'uses' => 'UserController@index',
            ]);
            Route::get('users/{hash}/restore', [
                'as' => 'admin.users.restore',
                'uses' => 'UserController@restore'
            ]);

            Route::resource('categories', 'CategoriesController', [
                'names' => 'admin.categories'
            ]);
            Route::get('categories/overview/{trashed?}', [
                'as' => 'admin.categories.overview',
                'uses' => 'CategoriesController@index',
            ]);
            Route::get('categories/{category}/restore', [
                'as' => 'admin.categories.restore',
                'uses' => 'CategoriesController@restore'
            ]);
        });


        /**
         *
         * Post routes
         */
        Route::resource('posts', 'PostsController', [
            'except' => 'update',
            'names' => 'admin.posts'
        ]);

        Route::post('posts/image/upload', [
            'as'    => 'admin.posts.uploadImage',
            'uses'  => 'PostsController@uploadImage',
        ]);
        Route::get('posts/overview/{trashed?}', [
            'as'    => 'admin.posts.overview',
            'uses'  => 'PostsController@index',
        ]);
        Route::get('posts/action/cancel/{hash?}', [
            'as'    => 'admin.posts.cancel',
            'uses'  => 'PostsController@cancel',
        ]);
        Route::get('posts/{hash}/restore', [
            'as' => 'admin.posts.restore',
            'uses' => 'PostsController@restore'
        ]);

        Route::group(['middleware' => 'HasAdminOrAuthorRole'], function() {
            Route::resource('tags', 'TagsController', [
                'except'    => 'store',
                'names'      => 'admin.tags'
            ]);
            Route::post('tags', [
                'as'    => 'admin.tags.store',
                'uses'  => 'TagsController@storeOrUpdate'
            ]);
            Route::get('tags/overview/{trashed?}', [
                'as'    => 'admin.tags.overview',
                'uses'  => 'TagsController@index',
            ]);
            Route::get('tags/{tag}/restore', [
                'as' => 'admin.tags.restore',
                'uses' => 'TagsController@restore'
            ]);

            Route::get('comments/{revised?}', [
                'as'    => 'admin.comments.index',
                'uses'  => 'CommentsController@index'
            ]);
            Route::get('comments/changestatus/{hash}/{revised}', [
                'as'    => 'admin.comments.changeStatus',
                'uses'  => 'CommentsController@changeStatus'
            ]);
        });

        Route::resource('profile', 'ProfileController', ['names' => 'admin.profile']);

        ///////////////////////////////////////////////////////////////////////////
        // API routes
        ///////////////////////////////////////////////////////////////////////////

        $api = [
            'prefix' => 'api',
        ];

        Route::group($api, function()
        {
            Route::get('sort/{table}/{column}/{order}/{trashed?}', [
                'as'    => 'admin.api.sort',
                'uses'  => 'ApiController@sort'
            ]);

            Route::get('slug/checkIfSlugIsUnique/{slug}', [
                'as'    => 'admin.api.slug.checkIfUnique',
                'uses'  => 'ApiController@checkIfSlugIsUnique',
            ]);

            Route::post('autosave', [
                'as'    => 'admin.api.autosave',
                'uses'  => 'ApiController@autoSave',
            ]);

            Route::get('tags/{tag}', [
                'as' => 'admin.api.tags',
                'uses' => 'ApiController@getTag'
            ]);
        });

    });

});
