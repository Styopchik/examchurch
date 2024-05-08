<?php

namespace App\Http\Controllers;

use App\DataTables\BlogDataTable;
use App\Facades\UtilityFacades;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index(BlogDataTable $dataTable)
    {
        if (\Auth::user()->can('manage-blog')) {
            return $dataTable->render('blog.index');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-blog')) {
            $categories = BlogCategory::where('status', 1)->pluck('name', 'id');
            return view('blog.create', compact('categories'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create-blog')) {
            request()->validate([
                'title'                 => 'required|string|max:191',
                'images'                => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'short_description'     => 'required|string',
                'description'           => 'required|string',
                'category'              => 'required',
            ]);
            if ($request->hasFile('images')) {
                $path                   = $request->file('images')->store('blogs');
            }
            Blog::create([
                'title'                 => $request->title,
                'description'           => $request->description,
                'category_id'           => $request->category,
                'images'                => $path,
                'short_description'     => $request->short_description,
                'created_by'            => \Auth::user()->id,
            ]);
            return redirect()->route('blogs.index')->with('success', __('Blog created successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function edit(Blog $blog)
    {
        if (\Auth::user()->can('edit-blog')) {
            $categories                 = BlogCategory::where('status', 1)->pluck('name', 'id');
            return view('blog.edit', compact('blog', 'categories'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-blog')) {
            request()->validate([
                'title'                 => 'required|string|max:191',
                'images'                => 'image|mimes:jpeg,png,jpg|max:2048',
                'short_description'     => 'required|string',
                'description'           => 'required|string',
                'category'              => 'required',
            ]);
            $blog                       = Blog::find($id);
            if ($request->hasFile('images')) {
                $path                   = $request->file('images')->store('blogs');
                $blog->images           = $path;
            }
            $blog->title                = $request->title;
            $blog->description          = $request->description;
            $blog->category_id          = $request->category;
            $blog->short_description    = $request->short_description;
            $blog->created_by           = \Auth::user()->id;
            $blog->save();
            return redirect()->route('blogs.index')->with('success', __('blogs updated successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('delete-blog')) {
            $post = Blog::find($id);
            $post->delete();
            return redirect()->route('blogs.index')->with('success', __('Posts deleted successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function viewBlog($slug)
    {
        $lang       = UtilityFacades::getActiveLanguage();
        \App::setLocale($lang);
        $blog       =  Blog::where('slug', $slug)->first();
        if (!$blog) {
            abort(404);
        }
        $allBlogs   =  Blog::all();
        return view('blog.view-blog', compact('blog', 'allBlogs', 'lang'));
    }

    public function seeAllBlogs(Request $request)
    {
        $lang                       = UtilityFacades::getActiveLanguage();
        \App::setLocale($lang);
        if ($request->category_id != '') {
            $blogs                  = Blog::where('category_id', $request->category_id)->paginate(3);
            $blogHtml               = '';
            foreach ($blogs as $blog) {
                $imageUrl           = $blog->images ? Storage::url($blog->images) : asset('vendor/landing-page2/image/blog-card-img-2.png');
                $redirectUrl        = route('view.blog', $blog->slug);
                $createdAt          = UtilityFacades::dateTimeFormat($blog->created_at);
                $shortDescription   = $blog->short_description ? $blog->short_description : 'A step-by-step guide on how to configure and implement multi-tenancy in a Laravel application, including tenant isolation and database separation.';
                $blogHtml .= '<div class="article-card">
                    <div class="article-card-inner">
                        <div class="article-card-image">
                            <a href="#">
                                <img src="' . $imageUrl . '" alt="blog-card-image">
                            </a>
                        </div>
                        <div class="article-card-content">
                            <div class="author-info d-flex align-items-center justify-content-between">
                                <div class="date d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23" fill="none">
                                        <!-- SVG path for date icon -->
                                    </svg>
                                    <span>' . $createdAt . '</span>
                                </div>
                            </div>
                            <h3>
                                <a href="' . $redirectUrl . '">' . $blog->title . '</a>
                            </h3>
                            <p>' . $shortDescription . '</p>
                        </div>
                    </div>
                </div>';
            }
            return response()->json(['appendedContent' => $blogHtml]);
        } else {
            $blogs      = Blog::paginate(3);
        }
        $recentBlogs    = Blog::latest()->take(3)->get();
        $lastBlog       = Blog::latest()->first();
        $categories     = BlogCategory::all();
        return view('blog.view-all-blogs', compact('blogs', 'recentBlogs', 'lastBlog', 'categories', 'lang'));
    }
}
