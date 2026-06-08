<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password - QRASA</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="antialiased">
    <div class="relative flex min-h-screen">
        <!-- Left Panel -->
        <div class="hidden w-1/2 flex-col items-center justify-center bg-gradient-to-r from-orange-500 to-orange-600 text-white lg:flex">
            <div class="text-center">
                <a href="/" class="text-6xl font-bold tracking-tight">QRasa</a>
                <p class="mt-4 max-w-md text-orange-100 px-8">
                    Lupa password Anda? Tidak masalah. Beri tahu kami alamat email Anda dan kami akan mengirimkan tautan reset password.
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
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900">Reset Password</h2>
                    <p class="mt-2 text-sm text-gray-600 block lg:hidden">
                        Lupa password Anda? Jangan khawatir, masukkan email Anda di bawah ini dan kami akan mengirimkan tautan untuk membuat password baru.
                    </p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" class="sr-only" />
                        <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="Alamat Email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="pt-2">
                        <x-primary-button class="w-full justify-center bg-orange-600 hover:bg-orange-500 focus:bg-orange-700 active:bg-orange-700">
                            {{ __('Kirim Link Reset Password') }}
                        </x-primary-button>
                    </div>

                    <div class="mt-6 text-center">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-orange-600 hover:text-orange-500">
                            Kembali ke Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
