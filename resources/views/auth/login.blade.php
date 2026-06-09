<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - QRASA</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="antialiased">
    <div class="relative flex min-h-screen">
        <!-- Left Panel -->
        <div class="hidden w-1/2 flex-col items-center justify-center bg-gradient-to-r from-orange-500 to-orange-600 text-white lg:flex">
            <div class="text-center">
                <a href="/" class="text-6xl font-bold tracking-tight">QRasa</a>
                <p class="mt-4 max-w-xs text-orange-100">
                    Selamat datang kembali! Masuk untuk mengelola menu dan pesanan Anda.
                </p>
            </div>
        </div>

        <!-- Right Panel (Form) -->
        <div class="flex w-full items-center justify-center bg-gray-50 lg:w-1/2">
            <div class="w-full max-w-md p-8 space-y-6">
                <div class="text-center lg:hidden">
                    <a href="/" class="text-3xl font-bold tracking-tight text-orange-600">QRASA</a>
                </div>

                <div class="text-left">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900">Login ke Akun Anda</h2>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" class="sr-only" />
                        <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Password')" class="sr-only" />
                        <x-text-input id="password" class="block w-full"
                                        type="password"
                                        name="password"
                                        required autocomplete="current-password"
                                        placeholder="Password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between">
                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-gray-400 text-orange-600 focus:ring-orange-600" name="remember">
                            <label for="remember_me" class="ms-2 block text-sm text-gray-900">{{ __('Remember me') }}</label>
                        </div>

                        @if (Route::has('password.request'))
                            <a class="text-sm font-medium text-orange-600 hover:text-orange-500" href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                    </div>

                    <div>
                        <x-primary-button class="w-full justify-center bg-orange-600 hover:bg-orange-500 focus:bg-orange-700 active:bg-orange-700">
                            {{ __('Log in') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
