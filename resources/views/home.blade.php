<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DebtLog</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Vazirmatn', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <script>
        // ریدایرکت خودکار اگر کاربر وارد شده باشد
        @auth
            window.location.href = "{{ route('expenses.index') }}";
        @endauth
        
        // ریدایرکت به صفحه لاگین
        @guest
            window.location.href = "{{ route('login') }}";
        @endguest
    </script>
    
    <!-- Fallback message -->
    <div class="text-center">
        <p class="text-gray-600">درحال انتقال...</p>
    </div>
</body>
</html>
