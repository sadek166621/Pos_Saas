<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
        return view('landing.index', compact('plans'));
    }

    public function pricing(): View
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
        return view('landing.pricing', compact('plans'));
    }

    public function features(): View
    {
        return view('landing.features');
    }

    public function solutions(): View
    {
        return view('landing.solutions');
    }

    public function contact(): View
    {
        return view('landing.contact');
    }

    public function terms(): View
    {
        return view('landing.terms');
    }

    public function privacy(): View
    {
        return view('landing.privacy');
    }
}
