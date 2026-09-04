<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function orders(): View
    {
        $orders = auth()->user()
            ->orders()
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('account.orders', compact('orders'));
    }

    public function profile(): View
    {
        return view('account.orders', [
            'orders' => auth()->user()->orders()->with('items')->latest()->paginate(10),
        ]);
    }
}
