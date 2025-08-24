<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Artship Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-b from-cyan-900 via-orange-300 to-yellow-200 text-amber-900 font-sans">

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside class="w-64 bg-yellow-200 border-r border-orange-700 p-4">
            <h1 class="text-2xl text-center font-bold mb-6">Artship</h1>

            <!-- Profile Icon -->
            <div class="flex justify-center mb-4">
                <a href="{{ route('profile.show') }}">
                    <img src="{{ asset('images/profile.png') }}"
                        alt="Profile"
                        class="w-16 h-16 rounded-full border-2 border-amber-300 shadow">
                </a>
            </div>


            <nav class="flex flex-col space-y-4">
                <a href="{{ route('dashboard') }}" class="hover:bg-amber-200  p-2 rounded"> Dashboard</a>
                <a href="{{ route('courses.index') }}" class="hover:bg-amber-200 p-2 rounded"> Courses</a>  
                
                @if(auth()->check() && auth()->user()->role !== 'admin')
                    <a href="{{ route('my.courses') }}" class="hover:bg-amber-200 p-2 rounded"> My Courses</a>
                @endif
                @if(auth()->check() && auth()->user()->role !== 'admin')
                    <a href="{{ route('wishlist.index') }}" class="block px-4 py-2 hover:bg-amber-100 rounded">Wishlist</a>
                @endif
            </nav>
            <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button type="submit" class="w-full text-left text-red-600 hover:bg-red-100 p-2 rounded">
                Logout
            </button>
        </form>

        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

</body>
</html>
