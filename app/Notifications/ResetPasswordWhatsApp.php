<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Services\WhatsAppService;

class ResetPasswordWhatsApp extends Notification
{
    use Queueable;

    public string $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $phone = $notifiable->phone;
        
        if ($phone) {
            $name = $notifiable->name;
            $email = $notifiable->getEmailForPasswordReset();
            $resetUrl = url(route('password.reset', [
                'token' => $this->token,
                'email' => $email
            ], false));

            $message = "Halo {$name}, klik link berikut untuk mereset password akun PPOB Anda: " . $resetUrl;

            WhatsAppService::send($phone, $message);
        }

        return [];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
