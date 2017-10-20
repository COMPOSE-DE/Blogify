<?php

namespace ComposeDe\Blogify\Requests;

use App\User;
use ComposeDe\Blogify\Facades\BlogifyAuth;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends Request
{
    protected $auth;

    /**
     * Holds the hash of the user
     * that we are trying to edit
     *
     * @var string
     */
    protected $hash;

    protected $users;


    /**
     * Holds the id of the user
     * that we are trying to edit
     *
     * @var int|bool
     */
    protected $userId;

    /**
     * Construct the class
     */
    public function __construct()
    {
        $this->auth = BlogifyAuth::getFacadeRoot();
        $this->users = app(config('blogify.models.auth'));
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->auth->user()->id == $this->route('profile');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:3|max:30',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->route('profile'))],
            'password' => 'nullable|required_with:new_password|AuthUserPass',
            'new_password' => 'nullable|confirmed',
            'profilepicture' => 'image|max:1000',
        ];
    }

    /**
     * Override default messages
     *
     * @return array
     */
    public function messages()
    {
        return [
            'auth_user_pass' => 'The given password does not match your current password',
        ];
    }
}
