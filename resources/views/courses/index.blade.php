@extends('layouts.dashboard')
    @section('header')
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Courses
        </h2>
    @endsection


@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-3xl font-bold text-brown-800 mb-6">Courses</h1>
    
    {{-- Category Filter --}}
    <div class="mb-4">
    <form method="GET" action="{{ route('courses.index') }}" class="mb-4">
        <label for="category" class="font-semibold text-brown-800">Filter by Category:</label>
        <select name="category" id="category" onchange="this.form.submit()" class="ml-2 px-3 py-1 border rounded">
            <option value="">All</option>
            <option value="Digital" {{ request('category') == 'Digital' ? 'selected' : '' }}>Digital</option>
            <option value="Traditional" {{ request('category') == 'Traditional' ? 'selected' : '' }}>Traditional</option>
            <option value="Basics" {{ request('category') == 'Basics' ? 'selected' : '' }}>Basics</option>
        </select>
    </form>


    </div>


    @auth
        @if(auth()->user()->role === 'admin')
            <div class="mb-4">
                <a href="{{ route('courses.create') }}" class="bg-amber-700 text-white px-4 py-2 rounded shadow hover:bg-amber-500">
                    ➕ Add New Course
                </a>
            </div>
        @endif
    @endauth
    <p class="text-red-600">Current filter: {{ request('category') }}</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($courses as $course)
            <a href="{{ route('courses.show', $course->id) }}" class="block hover:shadow-lg transition duration-300 rounded overflow-hidden">
                <div class="bg-white p-4 border rounded shadow h-full flex flex-col justify-between">
                    @if($course->thumbnail)
                        <img src="{{ asset($course->thumbnail) }}"
                            alt="Course Thumbnail"
                            class="w-full h-48 object-cover rounded mb-3 border border-gray-300">
                    @endif


                    <div>
                        <h3 class="text-xl font-semibold text-brown-800">{{ $course->title }}</h3>
                        <p class="text-sm text-amber-700 mb-1">{{ ucfirst($course->category) }}</p>
                        <p class="text-gray-600 mb-3">{{ $course->description }}</p>
                    </div>

                    @auth
                        {{-- Wishlist buttonss --}}
                        @if(auth()->user()->role !== 'admin')
                            @php
                                $isWishlisted = auth()->user()->wishlist->contains('course_id', $course->id);
                            @endphp

                            @if($isWishlisted)
                                <form method="POST" action="{{ route('wishlist.destroy', $course->id) }}" class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-pink-600 hover:underline">
                                        📜 Remove from Wishlist
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('wishlist.store', $course->id) }}" class="mt-2">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:underline">
                                        📝 Add to Wishlist
                                    </button>
                                </form>
                            @endif
                        @endif

                        {{-- Admin delete option --}}
                        @if(auth()->user()->role === 'admin')
                            <form method="POST" action="{{ route('courses.destroy', $course->id) }}" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:underline"
                                        onclick="return confirm('Delete this course?')">
                                    🗑️ Delete
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            </a>
        @empty
            <p>No courses found for this category.</p>
        @endforelse
    </div>
</div>
@endsection
