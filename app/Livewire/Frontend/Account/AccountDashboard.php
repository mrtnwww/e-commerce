<?php

namespace App\Livewire\Frontend\Account;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AccountDashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        return view('livewire.frontend.account.account-dashboard', [
            'recentOrders' => Order::where('user_id', $user->id)->latest()->limit(5)->get(),
            'totalOrders' => Order::where('user_id', $user->id)->count(),
            'totalSpent' => Order::where('user_id', $user->id)
                ->whereNotIn('status', ['cancelled', 'refunded'])
                ->sum('total'),
        ])->layout('layouts.store');
    }
}
