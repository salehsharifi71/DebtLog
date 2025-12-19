<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * نمایش صفحه خانه
     */
    public function index(): View
    {
        return view('home');
    }
}
