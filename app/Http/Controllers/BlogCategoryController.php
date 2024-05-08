<?php

namespace App\Http\Controllers;

use App\DataTables\BlogCategoryDataTable;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    public function index(BlogCategoryDataTable $dataTable)
    {
        if (\Auth::user()->can('manage-category')) {
            return $dataTable->render('blogs-category.index');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-category')) {
            return view('blogs-category.create');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create-category')) {
            request()->validate([
                'name'      => 'required|string|max:191|unique:blog_categories,name',
                'status'    => 'required',
            ]);
            BlogCategory::create([
                'name'      => $request->name,
                'status'    => $request->status
            ]);
            return redirect()->route('blogs-category.index')->with('success', __('Category created successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function edit(BlogCategory $blogCategory, $id)
    {
        if (\Auth::user()->can('edit-category')) {
            $category = BlogCategory::find($id);
            return view('blogs-category.edit', compact('category'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-category')) {
            request()->validate([
                'name'          => 'required|string|max:191|unique:blog_categories,name,' . $id,
                'status'        => 'required',
            ]);
            $category           = BlogCategory::find($id);
            $category->name     = $request->name;
            $category->status   = $request->status;
            $category->update();
            return redirect()->route('blogs-category.index')->with('success', __('Category updated successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('delete-category')) {
            $category = BlogCategory::find($id);
            $category->delete();
            return redirect()->route('blogs-category.index')->with('success', __('Category deleted successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function blogCategoryStatus(Request $request, $id)
    {
        $category             = BlogCategory::find($id);
        $input                = ($request->value == "true") ? 1 : 0;
        if ($category) {
            $category->status = $input;
            $category->save();
        }
        return response()->json(['is_success' => true, 'message' => __('Category status changed successfully.')]);
    }
}
