<?php

namespace App\Livewire\Frontend\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Profile extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    // Password change
    public string $currentPassword = '';

    public string $newPassword = '';

    public string $confirmPassword = '';

    public bool $showPasswordForm = false;

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
    }

    public function saveProfile(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.Auth::id(),
            'phone' => 'nullable|string|max:20',
        ]);

        Auth::user()->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        $this->dispatch('notify', message: 'Perfil actualizado');
    }

    public function changePassword(): void
    {
        $this->validate([
            'currentPassword' => 'required',
            'newPassword' => ['required', 'confirmed', Password::min(8)],
            'confirmPassword' => 'same:newPassword',
        ]);

        if (! Hash::check($this->currentPassword, Auth::user()->password)) {
            $this->addError('currentPassword', 'La contraseña actual no es correcta.');

            return;
        }

        Auth::user()->update(['password' => Hash::make($this->newPassword)]);

        $this->currentPassword = $this->newPassword = $this->confirmPassword = '';
        $this->showPasswordForm = false;

        $this->dispatch('notify', message: 'Contraseña actualizada');
    }

    public function render()
    {
        return view('livewire.frontend.account.profile')
            ->layout('layouts.store');
    }
}
