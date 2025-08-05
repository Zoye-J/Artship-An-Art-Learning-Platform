<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Course;

class WishlistController extends Controller
{
    public function store(Request $request, $courseId)
    {
        if (auth()->user()->role === 'admin') {
            abort(403); // Admins cannot add to wishlist
        }

        Wishlist::firstOrCreate([
            'user_id' => auth()->id(),
            'course_id' => $courseId,
        ]);

        return back()->with('success', 'Course added to wishlist!');
    }

    public function destroy($courseId)
    {
        Wishlist::where('user_id', auth()->id())
                ->where('course_id', $courseId)
                ->delete();

        return back()->with('success', 'Course removed from wishlist.');
    }

    public function index()
    {
        $wishlist = Wishlist::with('course')->where('user_id', auth()->id())->get();
       
        if (auth()->user()->role === 'admin') {
            abort(403); // Forbidden
        }

        $wishlistCourses = auth()->user()
            ->wishlist()
            ->with('course')
            ->get()
            ->pluck('course');

        return view('wishlist.index', compact('wishlistCourses'));
    }

}

