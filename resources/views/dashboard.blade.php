@extends('layouts.dashboard')

@section('content')
    <div class="relative rounded overflow-hidden shadow-lg">
        <img src="{{ asset('images/dashboard-banner.jpg') }}" alt="Welcome Banner" class="w-full h-64 object-cover">
        <div class="absolute top-0 left-0 w-full h-full flex items-start justify-start p-7">
            <div class="text-white font-bold text-6xl leading-tight drop-shadow-[2px_2px_5px_rgba(0,0,0,0.6)]">
                <p>Welcome</p>
                <p>To</p>
                <p>Artship</p>
            </div>
        </div>


    </div>

    <div class="mt-6">
        <p class="text-lg">Explore your courses and saved content using the side menu.</p>
    </div>
@endsection

