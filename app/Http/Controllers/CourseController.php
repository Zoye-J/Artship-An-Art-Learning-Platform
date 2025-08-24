<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\Enrollment;
use App\Models\ViewedMaterial;



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

    public function show(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $tab = $request->get('tab', 'videos');

        $materials = CourseMaterial::where('course_id', $id)
            ->where('type', $tab === 'videos' ? 'video' : 'pdf')
            ->get();

    // Check if user is enrolled and get progress
        $isEnrolled = auth()->check() && auth()->user()->courses->contains($course->id);
        $progress = 0;

        if ($isEnrolled) {
            $enrollment = Enrollment::where('user_id', auth()->id())
                ->where('course_id', $course->id)
                ->first();
        
            $progress = $enrollment ? $enrollment->progress : 0;
        }

        return view('course_materials.show', compact('course', 'materials', 'tab', 'isEnrolled', 'progress'));
    }

    public function markAsViewed($courseId, $materialId)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        if (!auth()->user()->courses->contains($courseId)) {
            return response()->json(['error' => 'Not enrolled'], 403);
        }

    // Mark material as viewed
        ViewedMaterial::firstOrCreate([
            'user_id' => auth()->id(),
            'course_material_id' => $materialId
        ]);

    // Update progress directly (no trait needed)
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $courseId)
            ->first();

        if ($enrollment) {
            $totalMaterials = $enrollment->course->materials()->count();
            if ($totalMaterials > 0) {
                $viewedMaterials = ViewedMaterial::where('user_id', auth()->id())
                    ->whereHas('material', function($query) use ($courseId) {
                        $query->where('course_id', $courseId);
                    })
                    ->count();
            
                $progress = round(($viewedMaterials / $totalMaterials) * 100);
                $enrollment->update(['progress' => $progress]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return back()->with('success', 'Course deleted.');
    }

    public function enroll($id)
    {
        $course = Course::findOrFail($id);

        if (!auth()->user()->courses()->where('course_id', $id)->exists()) {
            auth()->user()->courses()->attach($course->id);
            
            $enrollment = Enrollment::firstOrCreate([
                'user_id' => auth()->id(),
                'course_id' => $course->id
            ], [
                'progress' => 0
            ]);
        }

        return redirect()->route('my.courses')->with('success', 'Enrolled successfully!');
    }

    public function unenroll(Course $course)
    {
        $user = auth()->user();
        
        if ($user->courses->contains($course->id)) {
            $user->courses()->detach($course->id);
            
            $materialIds = $course->materials()->pluck('id');
            ViewedMaterial::where('user_id', $user->id)
                ->whereIn('course_material_id', $materialIds)
                ->delete();
        
            return redirect()->route('my.courses')
                ->with('success', 'Successfully unenrolled from ' . $course->title);
        }

        return redirect()->route('my.courses')
            ->with('error', 'You are not enrolled in this course');
    }
}


