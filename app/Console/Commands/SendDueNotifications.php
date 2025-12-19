<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendDueNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-due-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ارسال اعلان تلگرام برای اقساط سررسید فردا';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 در حال بررسی اقساط سررسید فردا...');

        // محاسبه تاریخ فردا
        $tomorrow = Carbon::tomorrow()->toDateString();

        // یافتن تمام اقساط پرداخت نشده که سررسید آنها فردا است
        $dueExpenses = Expense::where('is_paid', false)
            ->whereDate('due_date', $tomorrow)
            ->get();

        if ($dueExpenses->isEmpty()) {
            $this->info('✅ هیچ قسطی برای فردا یافت نشد.');
            return 0;
        }

        $this->info("📋 تعداد {$dueExpenses->count()} قسط سررسید فردا یافت شد.");

        $sentCount = 0;
        $failedCount = 0;

        foreach ($dueExpenses as $expense) {
            try {
                $message = $this->prepareMessage($expense);
                $this->sendTelegramNotification($message);
                
                $this->line("✅ پیام برای «{$expense->title}» ارسال شد.");
                $sentCount++;
                
                // جلوگیری از Rate Limit
                sleep(1);
                
            } catch (\Exception $e) {
                $this->error("❌ خطا در ارسال پیام برای «{$expense->title}»: " . $e->getMessage());
                Log::error('Telegram notification error', [
                    'expense_id' => $expense->id,
                    'error' => $e->getMessage()
                ]);
                $failedCount++;
            }
        }

        $this->info("✅ ارسال پیام‌ها تکمیل شد: {$sentCount} موفق، {$failedCount} ناموفق");
        
        return 0;
    }

    /**
     * آماده‌سازی متن پیام
     */
    private function prepareMessage(Expense $expense): string
    {
        $emoji = $expense->type === 'recurring' ? '📅' : '📄';
        
        $message = "⏰ *یادآوری سررسید قسط*\n\n";
        $message .= "{$emoji} *عنوان:* {$expense->title}\n";
        $message .= "💰 *مبلغ:* " . number_format($expense->amount) . " ریال\n";
        $message .= "📆 *تاریخ سررسید:* {$expense->due_date_jalali}\n";
        
        if ($expense->type === 'recurring' && $expense->current_installment) {
            $message .= "🔢 *شماره قسط:* {$expense->current_installment}/{$expense->recurrence_count}\n";
        }
        
        if ($expense->description) {
            $message .= "\n📝 *توضیحات:* {$expense->description}\n";
        }
        
        $message .= "\n⚠️ *این قسط فردا سررسید دارد!*";
        
        return $message;
    }

    /**
     * ارسال پیام به تلگرام
     */
    private function sendTelegramNotification(string $message): void
    {
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (empty($botToken) || empty($chatId)) {
            throw new \Exception('توکن ربات یا Chat ID تلگرام تنظیم نشده است. لطفاً فایل .env را بررسی کنید.');
        }

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        $response = Http::post($url, [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ]);

        if (!$response->successful()) {
            throw new \Exception('خطا در ارسال پیام به API تلگرام: ' . $response->body());
        }

        $result = $response->json();
        
        if (!isset($result['ok']) || !$result['ok']) {
            throw new \Exception('پاسخ نامعتبر از API تلگرام: ' . json_encode($result));
        }
    }
}
