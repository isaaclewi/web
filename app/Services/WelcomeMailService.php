<?php

namespace App\Services;

use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WelcomeMailService
{
    public static function send(
    string  $email,
    string  $prenom,
    string  $nom,
    ?string $matricule,
    string  $role,
    string  $passwordRaw,
    string  $institution,
): void {
    if (empty(trim($email))) return;

    try {
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'api-key'      => config('services.brevo.key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'name'  => config('mail.from.name'),
                'email' => config('mail.from.address'),
            ],
            'to' => [['email' => $email, 'name' => "{$prenom} {$nom}"]],
            'subject' => "Bienvenue sur {$institution} — Vos accès",
            'htmlContent' => "
                <div style='font-family:sans-serif;max-width:600px;margin:auto'>
                    <h2 style='color:#1e40af'>Bienvenue, {$prenom} {$nom} !</h2>
                    <p>Votre compte <strong>{$role}</strong> a été créé sur <strong>{$institution}</strong>.</p>
                    <table style='border-collapse:collapse;width:100%'>
                        <tr><td style='padding:8px;background:#f3f4f6'><strong>Matricule</strong></td><td style='padding:8px'>{$matricule}</td></tr>
                        <tr><td style='padding:8px;background:#f3f4f6'><strong>Email</strong></td><td style='padding:8px'>{$email}</td></tr>
                        <tr><td style='padding:8px;background:#f3f4f6'><strong>Mot de passe</strong></td><td style='padding:8px'>{$passwordRaw}</td></tr>
                    </table>
                    <p style='margin-top:20px'>
                        <a href='".config('app.url')."/login'
                           style='background:#1e40af;color:white;padding:10px 20px;border-radius:5px;text-decoration:none'>
                           Se connecter
                        </a>
                    </p>
                    <p style='color:#6b7280;font-size:12px;margin-top:20px'>Conservez ces informations en lieu sûr.</p>
                </div>
            ",
        ]);

        if ($response->successful()) {
            \Log::info('[WelcomeMail] ✅ Envoyé à '.$email.' ('.$role.')');
        } else {
            \Log::error('[WelcomeMail] ❌ Brevo API erreur', $response->json());
        }

    } catch (\Throwable $e) {
        \Log::error('[WelcomeMail] ❌ Échec', [
            'email'  => $email,
            'erreur' => $e->getMessage(),
        ]);
    }
}

    public static function sendToApprenant(string $email, string $prenom, string $nom, ?string $matricule, string $passwordRaw, string $institution): void
    {
        self::send($email, $prenom, $nom, $matricule, 'Apprenant', $passwordRaw, $institution);
    }

    public static function sendToTeacher(string $email, string $prenom, string $nom, ?string $matricule, string $passwordRaw, string $institution): void
    {
        self::send($email, $prenom, $nom, $matricule, 'Enseignant', $passwordRaw, $institution);
    }

    public static function sendToStaff(string $email, string $prenom, string $nom, ?string $matricule, string $passwordRaw, string $institution, string $role = 'Personnel administratif'): void
    {
        self::send($email, $prenom, $nom, $matricule, $role, $passwordRaw, $institution);
    }

    // ✅ NOUVEAU — Parent / Tuteur
    public static function sendToParent(string $email, string $prenom, string $nom, ?string $matricule, string $passwordRaw, string $institution): void
    {
        self::send($email, $prenom, $nom, $matricule, 'Parent / Tuteur', $passwordRaw, $institution);
    }
}
