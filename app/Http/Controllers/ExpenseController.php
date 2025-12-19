<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class ExpenseController extends Controller
{
    /**
     * تبدیل تاریخ جلالی به میلادی
     */
    private function convertJalaliToGregorian($jalaliDate)
    {
        try {
            // اگر تاریخ از قبل میلادی است، همان را برگردان
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $jalaliDate)) {
                $year = (int) substr($jalaliDate, 0, 4);
                // اگر سال بزرگتر از 1500 است، میلادی است
                if ($year > 1500) {
                    return $jalaliDate;
                }
            }
            
            // تبدیل جلالی به میلادی
            // فرمت ورودی: 1403/09/22 یا 1403-09-22
            $jalaliDate = str_replace('/', '-', $jalaliDate);
            $parts = explode('-', $jalaliDate);
            
            if (count($parts) === 3) {
                $jalalian = Jalalian::fromFormat('Y-m-d', $jalaliDate);
                return $jalalian->toCarbon()->format('Y-m-d');
            }
            
            return $jalaliDate;
        } catch (\Exception $e) {
            // در صورت خطا، تاریخ را همان‌طور برگردان
            return $jalaliDate;
        }
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        // اعتبارسنجی ورودی‌ها
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:one_time,recurring',
            'due_date' => 'required|date',
            'is_paid' => 'boolean',
            'description' => 'nullable|string',
            'recurrence_count' => 'required_if:type,recurring|nullable|integer|min:1|max:120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // تبدیل تاریخ جلالی به میلادی (اگر نیاز باشد)
        $gregorianDate = $this->convertJalaliToGregorian($data['due_date']);

        // اگر نوع هزینه یک‌بار باشد
        if ($data['type'] === 'one_time') {
            $expense = Expense::create([
                'title' => $data['title'],
                'amount' => $data['amount'],
                'type' => $data['type'],
                'due_date' => $gregorianDate,
                'is_paid' => $data['is_paid'] ?? false,
                'description' => $data['description'] ?? null,
                'recurrence_count' => null,
                'current_installment' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'هزینه با موفقیت ثبت شد',
                'data' => $expense
            ], 201);
        }

        // اگر نوع هزینه قسطی/تکراری باشد
        if ($data['type'] === 'recurring') {
            $expenses = [];
            // استفاده از تاریخ میلادی تبدیل شده
            $baseDate = Carbon::parse($gregorianDate);

            // ایجاد اقساط به تعداد recurrence_count
            for ($i = 1; $i <= $data['recurrence_count']; $i++) {
                // محاسبه تاریخ سررسید هر قسط (به صورت میلادی)
                $installmentDate = $i === 1 
                    ? $baseDate->copy() 
                    : $baseDate->copy()->addMonths($i - 1);

                $expense = Expense::create([
                    'title' => $data['title'] . ' - قسط ' . $i,
                    'amount' => $data['amount'],
                    'type' => $data['type'],
                    'due_date' => $installmentDate->format('Y-m-d'), // تاریخ میلادی
                    'is_paid' => false,
                    'description' => $data['description'] ?? null,
                    'recurrence_count' => $data['recurrence_count'],
                    'current_installment' => $i,
                ]);

                $expenses[] = $expense;
            }

            return response()->json([
                'success' => true,
                'message' => "تعداد {$data['recurrence_count']} قسط با موفقیت ثبت شد",
                'data' => $expenses,
                'total_installments' => count($expenses)
            ], 201);
        }
    }
 /**
     * متد کمکی برای دریافت بازه تاریخ میلادی یک ماه شمسی
     * این متد برای محاسبه دقیق شروع و پایان ماه‌های شمسی ضروری است
     */
    private function getJalaliMonthRange(Jalalian $date)
    {
        $year = $date->getYear();
        $month = $date->getMonth();

        // ساخت اولین روز ماه شمسی
        $firstDay = new Jalalian($year, $month, 1);
        
        // محاسبه تعداد روزهای همان ماه
        $daysInMonth = $firstDay->getMonthDays();
        
        // ساخت آخرین روز ماه شمسی
        $lastDay = new Jalalian($year, $month, $daysInMonth);

        return [
            'start' => $firstDay->toCarbon()->format('Y-m-d'), // تبدیل به میلادی برای دیتابیس
            'end'   => $lastDay->toCarbon()->format('Y-m-d'),
            'name'  => $firstDay->format('F'), // نام ماه (مثلاً: آذر)
        ];
    }

    /**
     * Display a listing of the expenses.
     */
    public function index()
    {
        // تاریخ امروز میلادی
        $today = Carbon::today()->format('Y-m-d');
        
        // زمان حال شمسی برای محاسبات
        $jalaliNow = Jalalian::now();

        // 1. محاسبه شروع و پایان ماه جاری شمسی
        $currentMonthRange = $this->getJalaliMonthRange($jalaliNow);
        
        // 2. اقساط عقب افتاده (سررسید قبل از امروز و پرداخت نشده)
        $overdueExpenses = Expense::where('due_date', '<', $today)
            ->where('is_paid', false)
            ->orderBy('due_date', 'asc')
            ->get();

        // 3. اقساط این ماه شمسی (از اول تا آخر ماه شمسی جاری)
        $currentMonthExpenses = Expense::whereBetween('due_date', [$currentMonthRange['start'], $currentMonthRange['end']])
            ->where('is_paid', false)
            ->orderBy('due_date', 'asc')
            ->get();

        // 4. کل اقساط آینده (هر چیزی بعد از پایان ماه جاری)
        $futureExpenses = Expense::where('due_date', '>', $currentMonthRange['end'])
            ->where('is_paid', false)
            ->orderBy('due_date', 'asc')
            ->get();

        // 5. اقساط نزدیک (10 مورد اول از امروز به بعد)
        $upcomingExpenses = Expense::where('due_date', '>=', $today)
            ->where('is_paid', false)
            ->orderBy('due_date', 'asc')
            ->take(10)
            ->get();

        // --- محاسبه ۳ ماه آینده به تفکیک ---
        
        // ماه اول آینده
        $nextMonth1Range = $this->getJalaliMonthRange($jalaliNow->addMonths(1));
        $nextMonth1Expenses = Expense::whereBetween('due_date', [$nextMonth1Range['start'], $nextMonth1Range['end']])
            ->where('is_paid', false)
            ->orderBy('due_date', 'asc') // مرتب‌سازی بر اساس تاریخ
            ->get();

        // ماه دوم آینده
        $nextMonth2Range = $this->getJalaliMonthRange($jalaliNow->addMonths(2));
        $nextMonth2Expenses = Expense::whereBetween('due_date', [$nextMonth2Range['start'], $nextMonth2Range['end']])
            ->where('is_paid', false)
            ->orderBy('due_date', 'asc')
            ->get();

        // ماه سوم آینده
        $nextMonth3Range = $this->getJalaliMonthRange($jalaliNow->addMonths(3));
        $nextMonth3Expenses = Expense::whereBetween('due_date', [$nextMonth3Range['start'], $nextMonth3Range['end']])
            ->where('is_paid', false)
            ->orderBy('due_date', 'asc')
            ->get();

        // ارسال نام ماه‌ها به ویو
        $nextMonth1NameJalali = $nextMonth1Range['name'];
        $nextMonth2NameJalali = $nextMonth2Range['name'];
        $nextMonth3NameJalali = $nextMonth3Range['name'];

        // محاسبات آماری (جمع مبالغ)
        $currentMonthTotal = $currentMonthExpenses->sum('amount');
        $futureTotal       = $futureExpenses->sum('amount');
        $overdueTotal      = $overdueExpenses->sum('amount');
        
        $nextMonth1Total = $nextMonth1Expenses->sum('amount');
        $nextMonth2Total = $nextMonth2Expenses->sum('amount');
        $nextMonth3Total = $nextMonth3Expenses->sum('amount');

        // محاسبات تعداد
        $currentMonthCount = $currentMonthExpenses->count();
        $futureCount       = $futureExpenses->count();
        $overdueCount      = $overdueExpenses->count();
        
        $nextMonth1Count = $nextMonth1Expenses->count();
        $nextMonth2Count = $nextMonth2Expenses->count();
        $nextMonth3Count = $nextMonth3Expenses->count();

        return view('expenses.index', compact(
            'currentMonthExpenses',
            'futureExpenses',
            'overdueExpenses',
            'upcomingExpenses',
            'currentMonthTotal',
            'futureTotal',
            'overdueTotal',
            'currentMonthCount',
            'futureCount',
            'overdueCount',
            'nextMonth1Expenses',
            'nextMonth2Expenses',
            'nextMonth3Expenses',
            'nextMonth1Total',
            'nextMonth2Total',
            'nextMonth3Total',
            'nextMonth1Count',
            'nextMonth2Count',
            'nextMonth3Count',
            'nextMonth1NameJalali',
            'nextMonth2NameJalali',
            'nextMonth3NameJalali'
        ));
    }
    

    /**
     * Display the specified expense.
     */
    public function show($id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'هزینه مورد نظر یافت نشد'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $expense
        ]);
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, $id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'هزینه مورد نظر یافت نشد'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric|min:0',
            'due_date' => 'sometimes|date',
            'is_paid' => 'sometimes|boolean',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $expense->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'هزینه با موفقیت بروزرسانی شد',
            'data' => $expense
        ]);
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy($id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'هزینه مورد نظر یافت نشد'
            ], 404);
        }

        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'هزینه با موفقیت حذف شد'
        ]);
    }

    /**
     * Mark expense as paid.
     */
    public function markAsPaid($id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'هزینه مورد نظر یافت نشد'
            ], 404);
        }

        $expense->update(['is_paid' => true]);

        return response()->json([
            'success' => true,
            'message' => 'هزینه به عنوان پرداخت شده علامت‌گذاری شد',
            'data' => $expense
        ]);
    }
}
