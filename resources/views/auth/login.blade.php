<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DebtLog - ورود</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Vazirmatn', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <!-- Card Container -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-10">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 10a9 9 0 018.354-8.969A7.5 7.5 0 1117.5 15H12m-2-2v2m0-6v2m0-6v2m4 0v2"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">DebtLog</h1>
                <p class="text-gray-600 text-sm mt-2">سیستم مدیریت بدهی‌ها و اقساط</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        ایمیل
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email"
                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:outline-none focus:border-indigo-500 transition-colors @error('email') border-red-500 @enderror"
                        placeholder="example@email.com"
                        value="{{ old('email') }}"
                        required
                        autofocus
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        رمز عبور
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:outline-none focus:border-indigo-500 transition-colors @error('password') border-red-500 @enderror"
                        placeholder="••••••••"
                        required
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        id="remember"
                        class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <label for="remember" class="mr-2 text-sm text-gray-600">
                        یادآوری من بمان
                    </label>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg transition-colors duration-200 mt-6"
                >
                    ورود به سیستم
                </button>
            </form>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mt-6 bg-red-50 border-r-4 border-red-500 p-4 rounded">
                    <p class="text-red-800 text-sm font-medium">خطا در ورود:</p>
                    <ul class="mt-2 text-red-700 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Info Message -->
            <div class="mt-6 bg-blue-50 border-r-4 border-blue-500 p-4 rounded">
                <p class="text-blue-800 text-xs font-medium">🔒 ورود امن</p>
                <p class="text-blue-700 text-sm mt-1">درخواست ورود خود را با ایمیل و رمز عبور خود تکمیل کنید.</p>
            </div>

            <!-- Footer -->
            <div class="text-center text-gray-600 text-xs mt-8 pt-6 border-t border-gray-200">
                <p>DebtLog © {{ date('Y') }} - تمام حقوق محفوظ است</p>
            </div>
        </div>

        <!-- Background Decoration -->
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>🌟 سیستم مدیریت هوشمند امور مالی شخصی</p>
        </div>
    </div>
</body>
</html>
