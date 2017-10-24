<?php

namespace ComposeDe\Blogify\Controllers\Admin;

use ComposeDe\Blogify\Requests\CategoryRequest;
use BlogifyCategoryModel;

class CategoriesController extends BaseController
{
    public function index($trashed = null, BlogifyCategoryModel $categoriesModel)
    {
        $query = $categoriesModel->orderBy('created_at', 'DESC');

        if ($trashed) {
            $query->onlyTrashed();
        }

        $categories = $query->paginate($this->config->items_per_page);

        return view('blogify::admin.categories.index', compact('categories', 'trashed'));
    }

    public function create()
    {
        return view('blogify::admin.categories.form');
    }

    public function edit(BlogifyCategoryModel $category)
    {
        return view('blogify::admin.categories.form', compact('category'));
    }

    public function store(CategoryRequest $request, BlogifyCategoryModel $categories)
    {
        $category = $categories->create([
            'name' => $request->get('name'),
            'hash' => str_random(),
        ]);

        if ($request->wantsJson()) {
            return $category;
        }

        $this->flashSuccess($category->name, 'created');

        return redirect()->route('admin.categories.index');
    }

    public function update(BlogifyCategoryModel $category, CategoryRequest $request)
    {
        $category->name = $request->name;
        $category->save();

        $this->flashSuccess($category->name, 'updated');

        return redirect()->route('admin.categories.index');
    }

    public function destroy(BlogifyCategoryModel $category)
    {
        $categoryName = $category->name;
        $category->delete();

        $this->flashSuccess($categoryName, 'deleted');

        return redirect()->route('admin.categories.index');
    }

    public function restore($id, BlogifyCategoryModel $categories)
    {
        $category = $categories->withTrashed()->findOrFail($id);
        $category->restore();

        $this->flashSuccess($category->name, 'deleted');

        return redirect()->route('admin.categories.index');
    }

    protected function flashSuccess($name, $action, $model = '')
    {
        parent::flashSuccess($name, $action, 'Category');
    }
}
