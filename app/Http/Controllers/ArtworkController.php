<?php

namespace App\Http\Controllers;


use App\Models\ArtworkSubmission;
use App\Models\ArtworkLike;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ArtworkController extends Controller
{
    // Show form for submitting artwork
    public function create(Course $course)
    {
        // Check if user is enrolled
        if (!auth()->user()->courses->contains($course->id)) {
            return redirect()->back()->with('error', 'You must be enrolled in the course to submit artwork.');
        }

        return view('artwork.create', compact('course'));
    }

    // Store artwork submission
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Upload image
        $imagePath = $request->file('image')->store('artwork_submissions', 'public');

        ArtworkSubmission::create([
            'user_id' => auth()->id(),
            'course_id' => $course->id,
            'title' => $request->title,
            'description' => $request->description,
            'image_path' => $imagePath
        ]);

        return redirect()->route('courses.show', $course->id)
            ->with('success', 'Artwork submitted successfully! It will be reviewed by admins.');
    }

    // Admin: View all submissions
    public function index()
    {
        $submissions = ArtworkSubmission::with(['user', 'course'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('artwork.index', compact('submissions'));
    }

    // Admin: Feature an artwork
    public function feature(ArtworkSubmission $artwork)
    {
        $artwork->update(['is_featured' => true]);

        return back()->with('success', 'Artwork featured successfully!');
    }

    // Admin: Unfeature an artwork
    public function unfeature(ArtworkSubmission $artwork)
    {
        $artwork->update(['is_featured' => false]);

        return back()->with('success', 'Artwork unfeatured successfully!');
    }

    public function toggleLike(ArtworkSubmission $artwork)
    {
        $like = ArtworkLike::where('user_id', auth()->id())
            ->where('artwork_id', $artwork->id)
            ->first();

        if ($like) {
            $like->delete();
            $artwork->decrement('likes_count');
            $isLiked = false;
        } else {
            ArtworkLike::create([
                'user_id' => auth()->id(),
                'artwork_id' => $artwork->id
            ]);
            $artwork->increment('likes_count');
            $isLiked = true;
        }

        return response()->json([
            'success' => true,
            'likes_count' => $artwork->fresh()->likes_count,
            'is_liked' => $isLiked
        ]);
    }
}
