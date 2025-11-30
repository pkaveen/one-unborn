<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Client Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-8">
                    <h1 class="text-2xl font-bold text-blue-600">Client Portal</h1>
                    <div class="hidden md:flex space-x-6">
                        <a href="{{ route('client.dashboard') }}" class="text-gray-700 hover:text-blue-600 font-medium">Dashboard</a>
                        <a href="{{ route('client.links') }}" class="text-gray-700 hover:text-blue-600 font-medium">My Links</a>
                        <a href="{{ route('client.sla.reports') }}" class="text-gray-700 hover:text-blue-600 font-medium">SLA Reports</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">{{ Auth::guard('client')->user()->username }}</span>
                    <form method="POST" action="{{ route('client.logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white shadow-lg mt-12">
        <div class="container mx-auto px-4 py-6">
            <p class="text-center text-gray-600">&copy; {{ date('Y') }} One Unborn. All rights reserved.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
