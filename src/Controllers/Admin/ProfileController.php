<?php

namespace ComposeDe\Blogify\Controllers\Admin;

use ComposeDe\Blogify\Config;
use ComposeDe\Blogify\Middleware\IsOwner;
use ComposeDe\Blogify\Requests\ProfileUpdateRequest;
use Intervention\Image\Facades\Image;

class ProfileController extends BaseController
{
    protected $users;

    public function __construct() {
        parent::__construct();

        $this->middleware(IsOwner::class, ['only', 'edit']);

        $this->users = app(config('blogify.models.user'));
    }

    public function edit($id)
    {
        $user = $this->users->findOrFail($id);

        return view('blogify::admin.profiles.form', compact('user'));
    }

    public function update(ProfileUpdateRequest $request, $id)
    {
        $user = $this->users->findOrFail($id);

        $user->name = $request->get('name');
        $user->email = $request->get('email');

        if ($request->has('new_password')) {
            $user->password = bcrypt($request->new_password);
        }

        if ($request->hasFile('profilepicture')) {
            $this->handleImage($request->file('profilepicture'), $user);
        }

        $user->save();

        $this->flashSuccess($user->name, 'updated');

        return redirect()->route('admin.dashboard');
    }

    ///////////////////////////////////////////////////////////////////////////
    // Helper methods
    ///////////////////////////////////////////////////////////////////////////

    /**
     * @param $image
     * @param $user
     */
    private function handleImage($image, $user)
    {
        $filename = $this->generateFilename();
        $path = $this->resizeAndSaveProfilePicture($image, $filename);

        if (isset($user->profilepicture)) {
            $this->removeOldPicture($user->profilepicture);
        }

        $user->profilepicture = $path;
    }

    /**
     * @return string
     */
    private function generateFilename()
    {
        return time().'-'.$this->users->name.'-profilepicture';
    }

    /**
     * @param $image
     * @param string $filename
     * @return string
     */
    private function resizeAndSaveProfilePicture($image, $filename)
    {
        $extention = $image->getClientOriginalExtension();
        $fullpath = $this->config->upload_paths->profiles->profilepictures.$filename.'.'.$extention;

        Image::make($image->getRealPath())
            ->resize($this->config->image_sizes->profilepictures[0], $this->config->image_sizes->profilepictures[1], function($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->save($fullpath);

        return $fullpath;
    }

    /**
     * @param $oldPicture
     */
    private function removeOldPicture($oldPicture)
    {
        if (file_exists($oldPicture)) {
            unlink($oldPicture);
        }
    }

    protected function flashSuccess($name, $action, $model = '')
    {
        parent::flashSuccess($name, $action, 'User');
    }
}
