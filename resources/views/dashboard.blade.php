<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DebtLog - داشبورد</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Vazirmatn', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="inline-flex items-center justify-center w-10 h-10 bg-indigo-100 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 10a9 9 0 018.354-8.969A7.5 7.5 0 1117.5 15H12m-2-2v2m0-6v2m0-6v2m4 0v2"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">DebtLog</h1>
            </div>
            
            <div class="flex items-center gap-4">
                <span class="text-gray-700">خوش آمدید، <strong>{{ Auth::user()->name }}</strong></span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">
                        خروج
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">داشبورد</h2>
            <p class="text-gray-600">خوش آمدید به سیستم مدیریت بدهی‌ها و اقساط</p>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <a href="{{ route('expenses.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white p-6 rounded-lg shadow-md transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold">افزودن هزینه جدید</h3>
                        <p class="text-indigo-100 text-sm">ثبت یک هزینه یا قسط جدید</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('expenses.index') }}" class="bg-green-600 hover:bg-green-700 text-white p-6 rounded-lg shadow-md transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold">مشاهده لیست هزینه‌ها</h3>
                        <p class="text-green-100 text-sm">تمام هزینه‌های ثبت شده</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Welcome Card -->
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <svg class="w-16 h-16 text-indigo-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">شما با موفقیت وارد شدید</h3>
            <p class="text-gray-600 mb-4">می‌توانید اکنون شروع به مدیریت هزینه‌ها و اقساط خود کنید</p>
            <a href="{{ route('expenses.index') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition-colors">
                شروع کنید
            </a>
        </div>
    </div>
</body>
</html>
