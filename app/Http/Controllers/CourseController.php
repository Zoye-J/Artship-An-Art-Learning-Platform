<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $selectedCategory = $request->input('category');
        
        $query = Course::query();

        if (!empty($selectedCategory) && $selectedCategory !== 'All') {
            $query->where('category', $selectedCategory);
        }


        $courses = $query->latest()->get();

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

    
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('thumbnails'), $filename);

        
            $validated['thumbnail'] = 'thumbnails/' . $filename;
        }

        Course::create($validated);

        return redirect()->route('courses.index')->with('success', 'Course added successfully.');
    }

    public function show(Course $course)
    {
        return view('courses.show', compact('course'));
    }




    public function destroy(Course $course)
    {
        $course->delete();

        return back()->with('success', 'Course deleted.');
    }
}


