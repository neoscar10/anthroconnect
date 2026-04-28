<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

class ProfilePage extends Component
{
    use WithFileUploads;

    public $name;
    public $whatsapp_phone;
    public $avatar;
    public $new_avatar;
    
    // Password change
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->whatsapp_phone = $user->whatsapp_phone;
        $this->avatar = $user->avatar;
    }

    public function updateProfile()
    {
        $user = \App\Models\User::find(Auth::id());
        
        if ($this->new_avatar) {
            $this->validate([
                'new_avatar' => ['image', 'max:2048'], // 2MB Max
            ]);

            // Delete old avatar if exists and is not a default/url
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $this->new_avatar->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();
            
            $this->avatar = $path;
            $this->new_avatar = null;

            session()->flash('status', 'profile-updated');
            $this->dispatch('profile-updated');
        }
    }

    public function updatePassword()
    {
        $user = Auth::user();

        $validated = $this->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        session()->flash('status', 'password-updated');
    }

    public function render()
    {
        return view('livewire.profile.profile-page')
            ->layout('layouts.public');
    }
}
