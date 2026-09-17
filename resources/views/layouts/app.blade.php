<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'StayFinder - Student Accommodation Marketplace' }}</title>
    <meta name="description" content="Discover affordable student PGs, hostels, flats, and rooms near your college or coaching institute. Free for students.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="flex min-h-full flex-col text-slate-800 antialiased bg-slate-50 selection:bg-indigo-500 selection:text-white">
    
    {{-- Top Navbar --}}
    @include('components.navbar')

    {{-- Main Content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('components.footer')

    <script>
        function togglePropertyFavorite(btn, propertyId) {
            if (! {{ Auth::check() ? 'true' : 'false' }}) {
                window.location.href = '{{ route('login') }}';
                return;
            }
            const icon = btn.querySelector('svg');
            btn.disabled = true;

            fetch('/stays/' + propertyId + '/favorite', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                btn.disabled = false;
                if (data.is_saved) {
                    btn.classList.remove('bg-white/90', 'text-slate-700');
                    btn.classList.add('bg-rose-600', 'text-white');
                    if (icon) icon.setAttribute('fill', 'currentColor');
                } else {
                    btn.classList.remove('bg-rose-600', 'text-white');
                    btn.classList.add('bg-white/90', 'text-slate-700');
                    if (icon) icon.setAttribute('fill', 'none');
                }
            })
            .catch(function() {
                btn.disabled = false;
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
