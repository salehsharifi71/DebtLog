<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * نمایش داشبورد
     */
    public function index(): View
    {
        return view('dashboard');
    }
}
