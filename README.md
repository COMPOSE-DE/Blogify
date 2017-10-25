# Blogify

Blogify is a package to add a blog to your Laravel 5 application. It comes with a full admin panel with different views for all user roles.  
You can generate the public part through an artisan command, but feel free to customize it.

**This is a fork to make general improvements, some of which are:**

- Allow custom model classes
- Allow custom table names
- Remove the hardcoded Auth facade from views and other places and use a slim wrapper class to allow arbitrary authentication frameworks (like Sentinel)
- Allow configuring route prefixes for frontend and backend
- Fix some bugs

## Installation
### Pull the code
You can download it directly or use composer (recommended), if you register this repository with it.

### Check your tables and models
Blogify will create several tables for its own use, they are prefixed with "blogify_". It will also use existing tables `users`, `roles` and `role_users`. This is one part of enabling compatibility with Sentinel. If you want different table names, you can specify them with the config keys `tables.*`.  
You should also check, if you want to override some models. These will usually be the models for users and roles. You can specify them with the config keys `models.*`.  
You can use `BlogifyRoleTrait` to help you with creating your own role model.  
If you want to use some existing roles for Blogify, adjust the return values of the methods `getAdminRoleName()` etc.  
If you are using Sentinel, your roles table has a colum `slug`, which is not set by the Blogify seeder. You could use the `saving` or `creating` event handlers to derive the slug from the name just in time.

### Set your admin account email address and password
Add to your environment file: `BLOGIFY_ADMIN_EMAIL` and `BLOGIFY_ADMIN_PASSWORD` with appropriate values.

### Run installation commands
Run:
1. `php artisan vendor:publish --provider=ComposeDe\\Blogify\\BlogifyServiceProvider` (you can choose, what you want to publish. Check the `BlogifyServiceProvider`)
2. `php artisan blogify:migrate`
3. `php artisan blogify:seed`

Check your users table and your roles table. Blogify has created a new admin user, if no user with an email of `BLOGIFY_ADMIN_EMAIL` was found. Your roles table should contain some new roles.

4. `php artisan blogify:create-dirs`
5. `php artisan blogify:generate` (see the command help for options)

## Known issues
- Original code is not fully migrated to handling multiple roles per user.
- Workflows for login and registration are very likely not working. This functionality should be removed, because Blogify is a package and should use the users and roles of the main application.

## You found an issue?
Let's hope it's not too bad.
