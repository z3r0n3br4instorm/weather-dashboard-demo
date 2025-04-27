<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="transition-colors">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Air Quality Dashboard') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Dark Mode JS -->
    <script>
        // Dark mode functionality
        document.addEventListener("DOMContentLoaded", () => {
            const darkModeToggle = document.getElementById("dark-mode-toggle");
            const htmlElement = document.documentElement;

            // Check for saved theme preference or use system preference
            const savedTheme = localStorage.getItem("theme");
            const systemPrefersDark = window.matchMedia(
                "(prefers-color-scheme: dark)"
            ).matches;

            // Set initial theme
            if (savedTheme === "dark" || (!savedTheme && systemPrefersDark)) {
                htmlElement.classList.add("dark");
                if (darkModeToggle) {
                    darkModeToggle.checked = true;
                }
            }

            // Toggle dark mode
            if (darkModeToggle) {
                darkModeToggle.addEventListener("change", () => {
                    if (darkModeToggle.checked) {
                        htmlElement.classList.add("dark");
                        localStorage.setItem("theme", "dark");
                    } else {
                        htmlElement.classList.remove("dark");
                        localStorage.setItem("theme", "light");
                    }
                });
            }
        });
    </script>
    
    <!-- Additional styles -->
    <style>
        .map-container {
            height: 500px;
            width: 100%;
        }
        .sensor-info {
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: background-color 0.3s ease;
        }
        .aqi-good { background-color: #00E400; color: black; }
        .aqi-moderate { background-color: #FFFF00; color: black; }
        .aqi-sensitive { background-color: #FF7E00; color: black; }
        .aqi-unhealthy { background-color: #FF0000; color: white; }
        .aqi-very-unhealthy { background-color: #99004C; color: white; }
        .aqi-hazardous { background-color: #7E0023; color: white; }

        /* Dark mode styles */
        .dark {
            color-scheme: dark;
        }
        
        .dark body {
            background-color: #1a1a1a;
            color: #e5e5e5;
        }
        
        .dark .bg-white {
            background-color: #2a2a2a !important;
        }
        
        .dark .bg-gray-100 {
            background-color: #333333 !important;
        }
        
        .dark .text-gray-500 {
            color: #a3a3a3 !important;
        }
        
        .dark .text-gray-700 {
            color: #d1d1d1 !important;
        }
        
        .dark .border-gray-100,
        .dark .border-gray-200 {
            border-color: #3a3a3a !important;
        }
        
        .dark .shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3) !important;
        }

        /* Toggle switch styles */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: #3b82f6;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }
    </style>
    
    @yield('head')
</head>
<body class="font-sans antialiased transition-colors">
    <div class="min-h-screen bg-gray-100 transition-colors">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow transition-colors">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
        
        <!-- Footer -->
        <footer class="bg-white py-4 shadow mt-6 transition-colors">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <div class="text-gray-500 text-sm">
                        &copy; {{ date('Y') }} Air Quality Dashboard for Colombo Metropolitan Area. All rights reserved.
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    @yield('scripts')
</body>
</html> 