<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    /**
     * نمایش فرم ورود
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * ورود کاربر
     */
    public function login(Request $request): RedirectResponse
    {
        // اعتبارسنجی ورودی‌ها
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.required' => 'ایمیل الزامی است',
            'email.email' => 'فرمت ایمیل نامعتبر است',
            'password.required' => 'رمز عبور الزامی است',
            'password.min' => 'رمز عبور باید حداقل 6 کاراکتر باشد',
        ]);

        // تلاش برای ورود
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // تجدید Session برای امنیت
            $request->session()->regenerate();
            
            return redirect()
                ->intended(route('expenses.index'))
                ->with('success', 'خوش آمدید!');
        }

        // بازگشت با خطا اگر ورود ناموفق بود
        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'اطلاعات ورود نامعتبر است',
            ]);
    }

    /**
     * خروج کاربر
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'با موفقیت خارج شدید');
    }
}
