<?php

namespace App\Http\Controllers;

use App\DataTables\TestimonialsDataTable;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index(TestimonialsDataTable $dataTable)
    {
        if (\Auth::user()->can('manage-testimonial')) {
            return $dataTable->render('testimonials.index');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-testimonial')) {
            return view('testimonials.create');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create-testimonial')) {
            request()->validate([
                'name'          => 'required|string|max:191',
                'title'         => 'required|string|max:191',
                'desc'          => 'required|string',
                'designation'   => 'required|string|max:191',
                'image'         => 'required|image|mimes:jpeg,jpg,png',
                'rating'        => 'required|numeric|min:1|max:5',
            ]);
            $fileName           = '';
            if ($request->file('image')) {
                $file           = $request->file('image');
                $fileName       = $file->store('testimonials');
            }
            Testimonial::create([
                'name'          => $request->name,
                'title'         => $request->title,
                'desc'          => $request->desc,
                'designation'   => $request->designation,
                'image'         => $fileName,
                'rating'        => $request->rating,
            ]);
            return redirect()->route('testimonials.index')->with('success', __('Testimonial created succesfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }
    public function edit($id)
    {
        if (\Auth::user()->can('edit-testimonial')) {
            $testimonial = Testimonial::find($id);
            return view('testimonials.edit', compact('testimonial'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-testimonial')) {
            request()->validate([
                'name'          => 'required|string|max:191',
                'title'         => 'required|string|max:191,' . $id,
                'desc'          => 'required|string',
                'designation'   => 'required|string|max:191',
                'image'         => 'required|image|mimes:jpeg,jpg,png',
                'rating'        => 'required|numeric|min:1|max:5',
            ]);
            $testimonial = Testimonial::find($id);
            if ($request->hasfile('image')) {
                $file               = $request->file('image');
                $fileName           =  $file->store('testimonials');
                $testimonial->image = $fileName;
            }
            $testimonial->name          = $request->name;
            $testimonial->title         = $request->title;
            $testimonial->desc          = $request->desc;
            $testimonial->rating        = $request->rating;
            $testimonial->designation   = $request->designation;
            $testimonial->save();
            return redirect()->route('testimonials.index')->with('success', __('Testimonial updated successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('delete-testimonial')) {
            $testimonial = Testimonial::find($id);
            $testimonial->delete();
            return back()->with('success', __('Testimonial deleted succesfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function status(Request $request, $id)
    {
        $testimonial    = Testimonial::find($id);
        $input          = ($request->value == "true") ? 1 : 0;
        if ($testimonial) {
            $testimonial->status = $input;
            $testimonial->save();
        }
        return response()->json(['is_success' => true, 'message' => __('Testimonial status changed successfully.')]);
    }
}
