<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function __invoke()
    {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.users.index'),
            'coordinator' => redirect()->route('indicators.index'),
            'specialist' => redirect()->route('inbox.index'),
            default => redirect()->route('consultations.index'),
        };
    }
}
