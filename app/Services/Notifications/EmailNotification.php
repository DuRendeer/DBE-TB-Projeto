<?php

namespace App\Services\Notifications;

use App\Contracts\NotificationInterface;
use Illuminate\Support\Facades\Log;

/**
 * Email Notification Implementation (Factory Method Pattern)
 *
 * NOTA: Esta implementação utiliza logs simulados para fins de demonstração e testes.
 *
 * Para enviar emails reais em produção:
 * 1. Configure MAIL_* no .env (SMTP)
 * 2. Descomente as linhas com Mail::send()
 * 3. Opcionalmente, crie uma Mailable class para emails formatados
 *
 * Exemplo de configuração .env:
 * MAIL_MAILER=smtp
 * MAIL_HOST=smtp.gmail.com
 * MAIL_PORT=587
 * MAIL_USERNAME=seu-email@gmail.com
 * MAIL_PASSWORD=sua-senha-app
 */
class EmailNotification implements NotificationInterface
{
    public function send(string $recipient, string $subject, string $message): bool
    {
        // ===== IMPLEMENTAÇÃO ATUAL: LOG SIMULADO =====
        // Usado para demonstrar o padrão Factory sem dependências externas
        Log::info("Email sent to {$recipient}: {$subject} - {$message}");

        // ===== PARA PRODUÇÃO: DESCOMENTAR ABAIXO =====
        // use Illuminate\Support\Facades\Mail;
        //
        // Mail::raw($message, function ($mail) use ($recipient, $subject) {
        //     $mail->to($recipient)->subject($subject);
        // });

        return true;
    }

    public function getChannel(): string
    {
        return 'email';
    }
}
