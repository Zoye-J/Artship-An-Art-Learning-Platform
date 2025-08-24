<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CourseMaterialController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MyCourseController;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
require __DIR__.'/auth.php';



// Authenticated routes (all users)
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Course routes - accessible to all authenticated users
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

    // Materials routes - accessible to all authenticated users
    Route::get('/courses/{course}/materials', [CourseMaterialController::class, 'index'])->name('courses.materials.index');

    // Wishlist routes
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{courseId}', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{courseId}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');


    // Course enrollment
    Route::post('/courses/{id}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
    Route::delete('/courses/{course}/unenroll', [CourseController::class, 'unenroll'])
    ->name('courses.unenroll')
    ->middleware('auth');

    // My Courses
    Route::get('/my-courses', [MyCourseController::class, 'index'])->name('my.courses');

    Route::post('/courses/{course}/materials/{material}/view', [CourseController::class, 'markAsViewed'])
    ->name('materials.view')
    ->middleware('auth');

});

// ADMIN-ONLY ROUTES (require admin role)
Route::middleware(['auth', 'admin'])->group(function () {
    // Admin course management
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

    // Admin material management
    Route::get('/courses/{course}/materials/create', [CourseMaterialController::class, 'create'])->name('courses.materials.create');
    Route::post('/courses/{course}/materials', [CourseMaterialController::class, 'store'])->name('courses.materials.store');
    Route::delete('/materials/{material}', [CourseMaterialController::class, 'destroy'])->name('materials.destroy');
});
