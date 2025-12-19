<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Morilog\Jalali\Jalalian;

class Expense extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'amount',
        'type',
        'due_date',
        'is_paid',
        'description',
        'recurrence_count',
        'current_installment',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'is_paid' => 'boolean',
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Get the due date in Jalali format.
     */
    protected function dueDateJalali(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->due_date) {
                    return null;
                }
                
                try {
                    // تبدیل به Carbon در صورت لزوم
                    $carbonDate = $this->due_date instanceof \Carbon\Carbon 
                        ? $this->due_date 
                        : \Carbon\Carbon::parse($this->due_date);
                    
                    // تبدیل به تاریخ شمسی
                    return Jalalian::fromCarbon($carbonDate)->format('Y/m/d');
                } catch (\Exception $e) {
                    \Log::error('Error converting date to Jalali: ' . $e->getMessage());
                    return $this->due_date;
                }
            }
        );
    }

    /**
     * Get the created at in Jalali format.
     */
    protected function createdAtJalali(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->created_at) {
                    return null;
                }
                
                try {
                    return Jalalian::fromCarbon($this->created_at)->format('Y/m/d H:i:s');
                } catch (\Exception $e) {
                    \Log::error('Error converting created_at to Jalali: ' . $e->getMessage());
                    return $this->created_at->format('Y-m-d H:i:s');
                }
            }
        );
    }
}
