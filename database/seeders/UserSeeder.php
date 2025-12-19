<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ایجاد کاربر تست
        User::firstOrCreate(
            ['email' => 'test@debtlog.local'],
            [
                'name' => 'کاربر تست',
                'password' => Hash::make('password123'),
            ]
        );

        // ایجاد کاربرهای اضافی
        User::factory(5)->create();
    }
}
