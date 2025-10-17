<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;

class EmailHelper
{
    public static function sendDynamicEmail($user)
    {
        $template = $user->email_template ?? 'default';

        if (view()->exists("emails.$template")) {
            Mail::send("emails.$template", ['user' => $user], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject("Welcome {$user->name}");
            });
        } else {
            // fallback template
            Mail::send("emails.default", ['user' => $user], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject("Welcome {$user->name}");
            });
        }
    }
}
