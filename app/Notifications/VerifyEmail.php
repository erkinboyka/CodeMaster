<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmail extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );

        return (new MailMessage)
            ->subject(__('Confirm your email — CodeMaster'))
            ->greeting(__('Hello, ') . $notifiable->name . '!')
            ->line(__('Thanks for registering on CodeMaster. Please confirm your email address by clicking the button below.'))
            ->action(__('Confirm Email'), $verificationUrl)
            ->line(__('This link will expire in 60 minutes.'))
            ->line(__('If you did not create an account, no action is required.'));
    }
}
