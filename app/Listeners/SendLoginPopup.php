<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class SendLoginPopup
{
    public function handle(Login $event)
    {
        session()->flash('login_popup', '🎉 Welcome back, ' . $event->user->name . '! You have successfully logged in.');
    }
}
