<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>داشبورد - سیستم مدیریت اقساط</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @import url('https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css');
        
        body {
            font-family: 'Vazirmatn', 'Segoe UI', Tahoma, sans-serif;
        }

        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .8;
            }
        }
        
        /* استایل اسکرول بار برای لیست‌ها */
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-blue-50 min-h-screen">
    
    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="text-4xl">💰</div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">سیستم مدیریت اقساط</h1>
                        <p class="text-sm text-gray-600">داشبورد مدیریت هزینه‌ها</p>
                    </div>
                </div>
                <a href="/expenses/create" class="bg-gradient-to-l from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-lg font-bold hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                    ➕ افزودن هزینه جدید
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- بخش آمار کلی (کارت‌های بالا) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- کارت ماه جاری -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 text-white card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-5xl">📅</div>
                    <div class="bg-white/20 rounded-full px-4 py-1 text-sm font-bold">
                        {{ $currentMonthCount }} قسط
                    </div>
                </div>
                <h2 class="text-xl font-bold mb-2">اقساط این ماه</h2>
                <div class="text-3xl font-bold mb-1">
                    {{ number_format($currentMonthTotal) }}
                </div>
                <p class="text-blue-100 text-sm">تومان</p>
            </div>

            <!-- کارت معوقه -->
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-xl p-6 text-white card-hover {{ $overdueCount > 0 ? 'pulse-animation' : '' }}">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-5xl">⚠️</div>
                    <div class="bg-white/20 rounded-full px-4 py-1 text-sm font-bold">
                        {{ $overdueCount }} قسط
                    </div>
                </div>
                <h2 class="text-xl font-bold mb-2">اقساط عقب افتاده</h2>
                <div class="text-3xl font-bold mb-1">
                    {{ number_format($overdueTotal) }}
                </div>
                <p class="text-red-100 text-sm">تومان</p>
            </div>

            <!-- کارت آینده کلی -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl p-6 text-white card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-5xl">📊</div>
                    <div class="bg-white/20 rounded-full px-4 py-1 text-sm font-bold">
                        {{ $futureCount }} قسط
                    </div>
                </div>
                <h2 class="text-xl font-bold mb-2">کل اقساط آینده</h2>
                <div class="text-3xl font-bold mb-1">
                    {{ number_format($futureTotal) }}
                </div>
                <p class="text-green-100 text-sm">تومان</p>
            </div>
        </div>

        <!-- خلاصه وضعیت ۳ ماه آینده (کارت‌های رنگی) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- ماه ۱ -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-xl p-6 text-white card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-5xl">📆</div>
                    <div class="bg-white/20 rounded-full px-4 py-1 text-sm font-bold">
                        {{ $nextMonth1Count }} قسط
                    </div>
                </div>
                <h2 class="text-xl font-bold mb-2">{{ $nextMonth1NameJalali }}</h2>
                <div class="text-3xl font-bold mb-1">
                    {{ number_format($nextMonth1Total) }}
                </div>
                <p class="text-purple-100 text-sm">تومان</p>
            </div>

            <!-- ماه ۲ -->
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl shadow-xl p-6 text-white card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-5xl">📅</div>
                    <div class="bg-white/20 rounded-full px-4 py-1 text-sm font-bold">
                        {{ $nextMonth2Count }} قسط
                    </div>
                </div>
                <h2 class="text-xl font-bold mb-2">{{ $nextMonth2NameJalali }}</h2>
                <div class="text-3xl font-bold mb-1">
                    {{ number_format($nextMonth2Total) }}
                </div>
                <p class="text-indigo-100 text-sm">تومان</p>
            </div>

            <!-- ماه ۳ -->
            <div class="bg-gradient-to-br from-orange-400 to-orange-500 rounded-2xl shadow-xl p-6 text-white card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-5xl">🗓️</div>
                    <div class="bg-white/20 rounded-full px-4 py-1 text-sm font-bold">
                        {{ $nextMonth3Count }} قسط
                    </div>
                </div>
                <h2 class="text-xl font-bold mb-2">{{ $nextMonth3NameJalali }}</h2>
                <div class="text-3xl font-bold mb-1">
                    {{ number_format($nextMonth3Total) }}
                </div>
                <p class="text-orange-100 text-sm">تومان</p>
            </div>
        </div>

        <!-- جدول اقساط نزدیک (Upcoming Expenses Table) -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="bg-gradient-to-l from-gray-700 to-gray-800 px-6 py-4">
                <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                    <span>🎯</span>
                    اقساط نزدیک (10 قسط اول)
                </h2>
            </div>

            @if($upcomingExpenses->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">#</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">عنوان</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">مبلغ (تومان)</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">تاریخ سررسید</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">نوع</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">قسط</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">وضعیت</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($upcomingExpenses as $index => $expense)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-bold">{{ $expense->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">{{ number_format($expense->amount) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">📆</span>
                                    <span class="font-bold">{{ $expense->due_date_jalali }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($expense->type === 'one_time')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">یکبار مصرف</span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">قسطی</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($expense->current_installment)
                                    <span class="font-bold">{{ $expense->current_installment }}/{{ $expense->recurrence_count }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($expense->is_paid)
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">پرداخت شده</span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">در انتظار پرداخت</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="markAsPaid({{ $expense->id }})" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg transition-colors text-xs font-bold" {{ $expense->is_paid ? 'disabled' : '' }}>✓ پرداخت</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📭</div>
                <p class="text-gray-500 text-lg">هیچ قسط نزدیکی وجود ندارد!</p>
            </div>
            @endif
        </div>

        <!-- لیست‌های تفصیلی (ماه جاری، معوقه، آینده دور) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
            
            <!-- اقساط این ماه -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col h-full">
                <div class="bg-blue-500 px-4 py-3">
                    <h3 class="text-lg font-bold text-white">📅 اقساط این ماه</h3>
                </div>
                <div class="p-4 overflow-y-auto custom-scroll flex-grow" style="max-height: 400px;">
                    @if($currentMonthExpenses->count() > 0)
                        <ul class="space-y-2">
                            @foreach($currentMonthExpenses as $expense)
                            <li class="border-r-4 border-blue-500 bg-blue-50 p-3 rounded hover:bg-blue-100 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="font-bold text-sm text-gray-800">{{ $expense->title }}</div>
                                    <button onclick="markAsPaid({{ $expense->id }})" 
                                            class="{{ $expense->is_paid ? 'bg-gray-300 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600' }} text-white text-xs px-2 py-1 rounded transition-colors" 
                                            {{ $expense->is_paid ? 'disabled' : '' }}
                                            title="{{ $expense->is_paid ? 'پرداخت شده' : 'ثبت پرداخت' }}">
                                        {{ $expense->is_paid ? '✓' : '✓ پرداخت' }}
                                    </button>
                                </div>
                                <div class="flex justify-between items-center mt-1">
                                    <div class="text-xs text-gray-600">سررسید: {{ $expense->due_date_jalali }}</div>
                                    <div class="text-sm font-bold text-blue-600">{{ number_format($expense->amount) }} تومان</div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="flex flex-col items-center justify-center h-full text-gray-400 py-8">
                            <span>✅</span>
                            <p>قسطی برای این ماه وجود ندارد</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- اقساط عقب افتاده -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col h-full">
                <div class="bg-red-500 px-4 py-3 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">⚠️ اقساط عقب افتاده</h3>
                    @if($overdueCount > 0)
                        <span class="bg-white/20 text-white text-xs px-2 py-1 rounded-full">{{ $overdueCount }} مورد</span>
                    @endif
                </div>
                <div class="p-4 overflow-y-auto custom-scroll flex-grow" style="max-height: 400px;">
                    @if($overdueExpenses->count() > 0)
                        <ul class="space-y-2">
                            @foreach($overdueExpenses as $expense)
                            <li class="border-r-4 border-red-500 bg-red-50 p-3 rounded hover:bg-red-100 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="font-bold text-sm text-gray-800">{{ $expense->title }}</div>
                                    <button onclick="markAsPaid({{ $expense->id }})" 
                                            class="{{ $expense->is_paid ? 'bg-gray-300 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600' }} text-white text-xs px-2 py-1 rounded transition-colors" 
                                            {{ $expense->is_paid ? 'disabled' : '' }}
                                            title="{{ $expense->is_paid ? 'پرداخت شده' : 'ثبت پرداخت' }}">
                                        {{ $expense->is_paid ? '✓' : '✓ پرداخت' }}
                                    </button>
                                </div>
                                <div class="flex justify-between items-center mt-1">
                                    <div class="text-xs text-red-600 font-bold">سررسید: {{ $expense->due_date_jalali }}</div>
                                    <div class="text-sm font-bold text-red-600">{{ number_format($expense->amount) }} تومان</div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="flex flex-col items-center justify-center h-full text-gray-400 py-8">
                            <span>🎉</span>
                            <p>قسط عقب افتاده‌ای ندارید!</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- اقساط آینده (دورتر) -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col h-full">
                <div class="bg-green-500 px-4 py-3">
                    <h3 class="text-lg font-bold text-white">⏳ اقساط دورتر</h3>
                </div>
                <div class="p-4 overflow-y-auto custom-scroll flex-grow" style="max-height: 400px;">
                    @if($futureExpenses->count() > 0)
                        <ul class="space-y-2">
                            @foreach($futureExpenses->take(10) as $expense)
                            <li class="border-r-4 border-green-500 bg-green-50 p-3 rounded hover:bg-green-100 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="font-bold text-sm text-gray-800">{{ $expense->title }}</div>
                                    <button onclick="markAsPaid({{ $expense->id }})" 
                                            class="{{ $expense->is_paid ? 'bg-gray-300 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600' }} text-white text-xs px-2 py-1 rounded transition-colors" 
                                            {{ $expense->is_paid ? 'disabled' : '' }}
                                            title="{{ $expense->is_paid ? 'پرداخت شده' : 'ثبت پرداخت' }}">
                                        {{ $expense->is_paid ? '✓' : '✓ پرداخت' }}
                                    </button>
                                </div>
                                <div class="flex justify-between items-center mt-1">
                                    <div class="text-xs text-gray-600">{{ $expense->due_date_jalali }}</div>
                                    <div class="text-sm font-bold text-green-600">{{ number_format($expense->amount) }} تومان</div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @if($futureExpenses->count() > 10)
                            <p class="text-center text-xs text-gray-500 mt-3 pt-2 border-t">و {{ $futureExpenses->count() - 10 }} قسط دیگر...</p>
                        @endif
                    @else
                        <div class="flex flex-col items-center justify-center h-full text-gray-400 py-8">
                            <p>قسط آینده‌ای ثبت نشده است</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ============================================= -->
        <!-- بخش جدید: جزئیات اقساط ۳ ماه آینده به تفکیک -->
        <!-- ============================================= -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-700 mb-6 pr-2 border-r-4 border-indigo-500">جزئیات اقساط ۳ ماه آینده</h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- لیست ماه اول آینده -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col h-full border border-purple-100">
                    <div class="bg-purple-600 px-4 py-3 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white">📅 {{ $nextMonth1NameJalali }}</h3>
                        <span class="bg-white/20 text-white text-xs px-2 py-1 rounded">ماه آینده</span>
                    </div>
                    <div class="p-4 overflow-y-auto custom-scroll flex-grow" style="max-height: 400px;">
                        @if($nextMonth1Expenses->count() > 0)
                            <ul class="space-y-2">
                                @foreach($nextMonth1Expenses as $expense)
                                <li class="border-r-4 border-purple-500 bg-purple-50 p-3 rounded hover:bg-purple-100 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-grow">
                                            <div class="font-bold text-sm text-gray-800">{{ $expense->title }}</div>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            @if($expense->current_installment)
                                                <span class="text-xs bg-purple-200 text-purple-800 px-1.5 py-0.5 rounded">{{ $expense->current_installment }}/{{ $expense->recurrence_count }}</span>
                                            @endif
                                            <button onclick="markAsPaid({{ $expense->id }})" 
                                                    class="{{ $expense->is_paid ? 'bg-gray-300 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600' }} text-white text-xs px-2 py-1 rounded transition-colors" 
                                                    {{ $expense->is_paid ? 'disabled' : '' }}
                                                    title="{{ $expense->is_paid ? 'پرداخت شده' : 'ثبت پرداخت' }}">
                                                {{ $expense->is_paid ? '✓' : '✓ پرداخت' }}
                                            </button>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center mt-2">
                                        <div class="text-xs text-gray-600 flex items-center gap-1">
                                            <span>📅</span> {{ $expense->due_date_jalali }}
                                        </div>
                                        <div class="text-sm font-bold text-purple-700">{{ number_format($expense->amount) }} تومان</div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                            <div class="mt-3 pt-3 border-t border-purple-100 flex justify-between items-center text-sm text-purple-800 font-bold">
                                <span>جمع کل:</span>
                                <span>{{ number_format($nextMonth1Total) }} تومان</span>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center h-full text-gray-400 py-8">
                                <span class="text-4xl mb-2">🏖️</span>
                                <p>در {{ $nextMonth1NameJalali }} قسطی ندارید</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- لیست ماه دوم آینده -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col h-full border border-indigo-100">
                    <div class="bg-indigo-600 px-4 py-3 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white">📅 {{ $nextMonth2NameJalali }}</h3>
                        <span class="bg-white/20 text-white text-xs px-2 py-1 rounded">2 ماه دیگر</span>
                    </div>
                    <div class="p-4 overflow-y-auto custom-scroll flex-grow" style="max-height: 400px;">
                        @if($nextMonth2Expenses->count() > 0)
                            <ul class="space-y-2">
                                @foreach($nextMonth2Expenses as $expense)
                                <li class="border-r-4 border-indigo-500 bg-indigo-50 p-3 rounded hover:bg-indigo-100 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-grow">
                                            <div class="font-bold text-sm text-gray-800">{{ $expense->title }}</div>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            @if($expense->current_installment)
                                                <span class="text-xs bg-indigo-200 text-indigo-800 px-1.5 py-0.5 rounded">{{ $expense->current_installment }}/{{ $expense->recurrence_count }}</span>
                                            @endif
                                            <button onclick="markAsPaid({{ $expense->id }})" 
                                                    class="{{ $expense->is_paid ? 'bg-gray-300 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600' }} text-white text-xs px-2 py-1 rounded transition-colors" 
                                                    {{ $expense->is_paid ? 'disabled' : '' }}
                                                    title="{{ $expense->is_paid ? 'پرداخت شده' : 'ثبت پرداخت' }}">
                                                {{ $expense->is_paid ? '✓' : '✓ پرداخت' }}
                                            </button>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center mt-2">
                                        <div class="text-xs text-gray-600 flex items-center gap-1">
                                            <span>📅</span> {{ $expense->due_date_jalali }}
                                        </div>
                                        <div class="text-sm font-bold text-indigo-700">{{ number_format($expense->amount) }} تومان</div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                            <div class="mt-3 pt-3 border-t border-indigo-100 flex justify-between items-center text-sm text-indigo-800 font-bold">
                                <span>جمع کل:</span>
                                <span>{{ number_format($nextMonth2Total) }} تومان</span>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center h-full text-gray-400 py-8">
                                <span class="text-4xl mb-2">🏖️</span>
                                <p>در {{ $nextMonth2NameJalali }} قسطی ندارید</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- لیست ماه سوم آینده -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col h-full border border-orange-100">
                    <div class="bg-orange-500 px-4 py-3 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white">📅 {{ $nextMonth3NameJalali }}</h3>
                        <span class="bg-white/20 text-white text-xs px-2 py-1 rounded">3 ماه دیگر</span>
                    </div>
                    <div class="p-4 overflow-y-auto custom-scroll flex-grow" style="max-height: 400px;">
                        @if($nextMonth3Expenses->count() > 0)
                            <ul class="space-y-2">
                                @foreach($nextMonth3Expenses as $expense)
                                <li class="border-r-4 border-orange-500 bg-orange-50 p-3 rounded hover:bg-orange-100 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-grow">
                                            <div class="font-bold text-sm text-gray-800">{{ $expense->title }}</div>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            @if($expense->current_installment)
                                                <span class="text-xs bg-orange-200 text-orange-800 px-1.5 py-0.5 rounded">{{ $expense->current_installment }}/{{ $expense->recurrence_count }}</span>
                                            @endif
                                            <button onclick="markAsPaid({{ $expense->id }})" 
                                                    class="{{ $expense->is_paid ? 'bg-gray-300 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600' }} text-white text-xs px-2 py-1 rounded transition-colors" 
                                                    {{ $expense->is_paid ? 'disabled' : '' }}
                                                    title="{{ $expense->is_paid ? 'پرداخت شده' : 'ثبت پرداخت' }}">
                                                {{ $expense->is_paid ? '✓' : '✓ پرداخت' }}
                                            </button>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center mt-2">
                                        <div class="text-xs text-gray-600 flex items-center gap-1">
                                            <span>📅</span> {{ $expense->due_date_jalali }}
                                        </div>
                                        <div class="text-sm font-bold text-orange-700">{{ number_format($expense->amount) }} تومان</div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                            <div class="mt-3 pt-3 border-t border-orange-100 flex justify-between items-center text-sm text-orange-800 font-bold">
                                <span>جمع کل:</span>
                                <span>{{ number_format($nextMonth3Total) }} تومان</span>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center h-full text-gray-400 py-8">
                                <span class="text-4xl mb-2">🏖️</span>
                                <p>در {{ $nextMonth3NameJalali }} قسطی ندارید</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white shadow-md mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center">
            <p class="text-gray-600">
                💰 سیستم مدیریت اقساط - ساخته شده با Laravel و Tailwind CSS
            </p>
        </div>
    </footer>

    <script>
        function markAsPaid(expenseId) {
            if (!confirm('آیا از پرداخت این قسط اطمینان دارید؟')) {
                return;
            }

            fetch(`/api/expenses/${expenseId}/mark-paid`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ خطا: ' + (data.message || 'خطایی رخ داد'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ خطا در ارتباط با سرور');
            });
        }
    </script>
</body>
</html>