# Blogify

Blogify is a package to add a blog to your Laravel 5 application. It comes with a full admin panel with different views for all user roles.
You can generate the public part through an artisan command but feel free to customize it, or just create your own using or models and their scopes.

This is a fork to make general improvements, some of which are:

- Allow custom Model classes
- Allow custom table names
- Remove the hardcoded Auth facade from views and other places and use a slim wrapper class to allow arbitrary authentication frameworks (like Sentinel)
- Allow configuring route prefixes for frontend and backend
- Fix some bugs
