<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
            $resetLink = $frontendUrl . "/admin/reset-password?token={$this->token}";

            return $this->subject('Admin Password Reset')
                 ->view('emails.admin_reset_password')
                 ->with(['resetLink' => $resetLink]);
    }
}
