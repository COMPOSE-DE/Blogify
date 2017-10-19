<?php

namespace ComposeDe\Blogify\Requests;

use ComposeDe\Blogify\Models\Post;
use ComposeDe\Blogify\Models\Visibility;

class PostRequest extends Request
{

    /**
     * @var \ComposeDe\Blogify\Models\Post
     */
    protected $posts;

    /**
     * @var \ComposeDe\Blogify\Models\Visibility
     */
    protected $visibilities;

    /**
     * @param \ComposeDe\Blogify\Models\Post       $posts
     * @param \ComposeDe\Blogify\Models\Visibility $visibilities
     */
    public function __construct(Post $posts, Visibility $visibilities)
    {
        $this->posts = $posts;
        $this->visibilities = $visibilities;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $hash = $this->input('hash');
        $id = (! empty($hash)) ? $this->posts->byHash($hash)->id : 0;
        $protected_visibility = $this->visibilities->whereName('Protected')->first()->hash;

        return [
            'title'             => 'required|min:2|max:100',
            'slug'              => "required|unique:posts,slug,$id|min:2|max:120",
            'reviewer'          => 'exists:users,id',
            'post'              => 'required',
            'category'          => 'required|exists:categories,hash',
            'publishdate'       => 'required|date: d-M-Y H:i',
            'password'          => "required_if:visibility,$protected_visibility",
        ];
    }

}
