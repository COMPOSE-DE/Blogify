<?php

namespace ComposeDe\Blogify\Requests;

use ComposeDe\Blogify\Models\Category;

class CategoryRequest extends Request
{

    /**
     * @var \ComposeDe\Blogify\Models\Category
     */
    protected $category;

    /**
     * @param \ComposeDe\Blogify\Models\Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
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
        $id = $this->segment(3) ?: 0;

        return [
            'name' => "required|unique:categories,name,$id|min:3|max:45",
        ];
    }
}
