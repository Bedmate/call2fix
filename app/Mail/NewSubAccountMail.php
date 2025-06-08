<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewSubAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subAccount;
    public $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subAccount, $password)
    {
        $this->subAccount = $subAccount;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $accountType = $this->subAccount->sub_account_type === 'department' ? 'department' : 'sub account';

        $message = 
            "A new {$accountType} has been created for you.\n\n" .
            "Name: " . $this->subAccount->name . "\n" .
            "Email: " . $this->subAccount->email . "\n" .
            "Role: " . $this->subAccount->role . "\n" .
            "Password: " . $this->password . "\n\n" .
            "Please log in and change your password as soon as possible.";

        return $this->subject("Your New {$accountType} Has Been Created")
                    ->view('vendor.email', [
                        'content' => nl2br(e($message)),
                        'notifiable' => $this->subAccount,
                    ]);
    }

}
