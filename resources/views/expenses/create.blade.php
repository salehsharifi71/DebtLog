<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ثبت هزینه جدید - سیستم مدیریت اقساط</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Persian Date Picker CSS -->
    <link rel="stylesheet" href="https://unpkg.com/persian-datepicker@latest/dist/css/persian-datepicker.min.css">
    
    <style>
        @import url('https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css');
        
        body {
            font-family: 'Vazirmatn', 'Segoe UI', Tahoma, sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen py-8 px-4">
    
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">
                💰 ثبت هزینه جدید
            </h1>
            <p class="text-gray-600">
                هزینه‌های یکبار مصرف یا قسطی خود را مدیریت کنید
            </p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-l from-blue-600 to-indigo-600 p-6">
                <h2 class="text-white text-2xl font-bold">
                    📝 اطلاعات هزینه
                </h2>
            </div>

            <form id="expenseForm" class="p-8 space-y-6">
                @csrf

                <!-- Alert Messages -->
                <div id="alertContainer" class="hidden">
                    <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 rounded-lg" role="alert">
                        <p class="font-bold">✅ موفقیت!</p>
                        <p id="successMessage"></p>
                    </div>
                </div>

                <div id="errorContainer" class="hidden">
                    <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
                        <p class="font-bold">❌ خطا!</p>
                        <p id="errorMessage"></p>
                    </div>
                </div>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-gray-700 font-bold mb-2">
                        عنوان هزینه <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors"
                        placeholder="مثال: وام مسکن، خرید لپتاپ، قسط ماشین"
                    >
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-gray-700 font-bold mb-2">
                        مبلغ (تومان) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="amount" 
                        name="amount" 
                        required
                        min="0"
                        step="1000"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors"
                        placeholder="مثال: 1000000"
                    >
                    <p class="text-sm text-gray-500 mt-1" id="amountFormatted"></p>
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        نوع هزینه <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative cursor-pointer">
                            <input 
                                type="radio" 
                                name="type" 
                                value="one_time" 
                                checked
                                class="peer sr-only"
                            >
                            <div class="w-full p-4 border-2 border-gray-300 rounded-lg text-center transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-600 peer-checked:font-bold hover:border-blue-300">
                                <div class="text-3xl mb-2">📄</div>
                                <div>یکبار مصرف</div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input 
                                type="radio" 
                                name="type" 
                                value="recurring"
                                class="peer sr-only"
                            >
                            <div class="w-full p-4 border-2 border-gray-300 rounded-lg text-center transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-600 peer-checked:font-bold hover:border-blue-300">
                                <div class="text-3xl mb-2">📅</div>
                                <div>قسطی / تکراری</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Due Date -->
                <div>
                    <label for="due_date" class="block text-gray-700 font-bold mb-2">
                        تاریخ سررسید <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="due_date_display" 
                        required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors cursor-pointer"
                        placeholder="تاریخ را انتخاب کنید"
                        readonly
                    >
                    <input type="hidden" id="due_date" name="due_date">
                </div>

                <!-- Recurrence Count (Only for recurring) -->
                <div id="recurrenceField" class="hidden">
                    <label for="recurrence_count" class="block text-gray-700 font-bold mb-2">
                        تعداد اقساط <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="recurrence_count" 
                        name="recurrence_count" 
                        min="1"
                        max="120"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors"
                        placeholder="مثال: 12"
                    >
                    <p class="text-sm text-gray-500 mt-1">
                        تعداد اقساط را وارد کنید (حداکثر 120 قسط)
                    </p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-gray-700 font-bold mb-2">
                        توضیحات (اختیاری)
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="4"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition-colors resize-none"
                        placeholder="توضیحات تکمیلی در مورد این هزینه..."
                    ></textarea>
                </div>

                <!-- Is Paid (Only for one_time) -->
                <div id="isPaidField">
                    <label class="flex items-center cursor-pointer">
                        <input 
                            type="checkbox" 
                            id="is_paid" 
                            name="is_paid"
                            value="1"
                            class="w-5 h-5 text-blue-600 border-2 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                        >
                        <span class="mr-3 text-gray-700 font-bold">
                            ✅ این هزینه پرداخت شده است
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="w-full bg-gradient-to-l from-blue-600 to-indigo-600 text-white font-bold py-4 px-6 rounded-lg hover:from-blue-700 hover:to-indigo-700 transform hover:scale-[1.02] transition-all duration-200 shadow-lg hover:shadow-xl"
                    >
                        💾 ذخیره هزینه
                    </button>
                </div>
            </form>
        </div>

        <!-- Back Link -->
        <div class="text-center mt-8">
            <a href="/" class="text-blue-600 hover:text-blue-800 font-bold underline">
                🏠 بازگشت به صفحه اصلی
            </a>
        </div>
    </div>

    <!-- jQuery (Required for Persian Date Picker) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Persian Date Picker JS -->
    <script src="https://unpkg.com/persian-date@latest/dist/persian-date.min.js"></script>
    <script src="https://unpkg.com/persian-datepicker@latest/dist/js/persian-datepicker.min.js"></script>

    <script>
        // Initialize Persian Date Picker
        $(document).ready(function() {
            const picker = $('#due_date_display').persianDatepicker({
                format: 'YYYY/MM/DD',
                initialValue: false,
                autoClose: true,
                altField: '#due_date',
                altFormat: 'YYYY-MM-DD',
                altFieldFormatter: function(unixDate) {
                    const pDate = new persianDate(unixDate);
                    return pDate.toLocale('en').format('YYYY-MM-DD');
                },
                calendar: {
                    persian: {
                        locale: 'fa'
                    }
                },
                onSelect: function(unix) {
                    // تبدیل تاریخ شمسی به میلادی
                    const pDate = new persianDate(unix);
                    const gregorianDate = pDate.toLocale('en').format('YYYY-MM-DD');
                    
                    // ذخیره تاریخ میلادی در فیلد مخفی
                    $('#due_date').val(gregorianDate);
                    console.log('تاریخ انتخاب شده:', gregorianDate);
                }
            });

            // Toggle recurrence field based on type
            $('input[name="type"]').on('change', function() {
                const type = $(this).val();
                const recurrenceField = $('#recurrenceField');
                const isPaidField = $('#isPaidField');
                
                if (type === 'recurring') {
                    recurrenceField.removeClass('hidden');
                    isPaidField.addClass('hidden');
                    $('#recurrence_count').attr('required', true);
                    $('#is_paid').prop('checked', false);
                } else {
                    recurrenceField.addClass('hidden');
                    isPaidField.removeClass('hidden');
                    $('#recurrence_count').attr('required', false);
                }
            });

            // Format amount input
            $('#amount').on('input', function() {
                const value = $(this).val();
                if (value) {
                    const formatted = new Intl.NumberFormat('fa-IR').format(value);
                    $('#amountFormatted').text(formatted + ' تومان');
                } else {
                    $('#amountFormatted').text('');
                }
            });

            // Form submission
            $('#expenseForm').on('submit', function(e) {
                e.preventDefault();
                
                // Hide previous alerts
                $('#alertContainer, #errorContainer').addClass('hidden');
                
                // Disable submit button
                const submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true).html('⏳ در حال ذخیره...');
                
                // Get form data
                const formData = {
                    title: $('#title').val(),
                    amount: $('#amount').val(),
                    type: $('input[name="type"]:checked').val(),
                    due_date: $('#due_date').val(),
                    description: $('#description').val(),
                    is_paid: $('#is_paid').is(':checked') ? 1 : 0,
                    recurrence_count: $('#recurrence_count').val() || null,
                    _token: $('input[name="_token"]').val()
                };

                // Validation
                if (!formData.due_date) {
                    showError('لطفاً تاریخ سررسید را انتخاب کنید.');
                    submitBtn.prop('disabled', false).html('💾 ذخیره هزینه');
                    return;
                }

                if (formData.type === 'recurring' && !formData.recurrence_count) {
                    showError('لطفاً تعداد اقساط را وارد کنید.');
                    submitBtn.prop('disabled', false).html('💾 ذخیره هزینه');
                    return;
                }

                // Send AJAX request
                fetch('{{route('expenses.store')}}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': formData._token
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccess(data.message);
                        
                        // Reset form after 2 seconds
                        setTimeout(() => {
                            $('#expenseForm')[0].reset();
                            $('#amountFormatted').text('');
                            $('#due_date').val('');
                            $('#due_date_display').val('');
                            $('#recurrenceField').addClass('hidden');
                            $('#alertContainer').addClass('hidden');
                        }, 2000);
                    } else {
                        showError(data.message || 'خطایی در ثبت هزینه رخ داد.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('خطایی در ارتباط با سرور رخ داد. لطفاً دوباره تلاش کنید.');
                })
                .finally(() => {
                    submitBtn.prop('disabled', false).html('💾 ذخیره هزینه');
                });
            });

            function showSuccess(message) {
                $('#successMessage').text(message);
                $('#alertContainer').removeClass('hidden');
                
                // Scroll to top
                $('html, body').animate({ scrollTop: 0 }, 'smooth');
            }

            function showError(message) {
                $('#errorMessage').text(message);
                $('#errorContainer').removeClass('hidden');
                
                // Scroll to top
                $('html, body').animate({ scrollTop: 0 }, 'smooth');
            }
        });
    </script>
</body>
</html>
