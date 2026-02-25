<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    /**
     * Build the mail representation of the notification using the site's design.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);
        $locale = $notifiable->locale ?? app()->getLocale();
        $expireMinutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        return (new MailMessage)
            ->subject(__('ui.reset_email_subject', [], $locale))
            ->view('emails.reset-password', [
                'url' => $url,
                'user' => $notifiable,
                'expireMinutes' => $expireMinutes,
                'locale' => $locale,
            ]);
    }
}
