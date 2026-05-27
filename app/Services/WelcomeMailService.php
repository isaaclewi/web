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
        if (empty(trim($email))) {
            return;
        }

        try {
            Mail::to($email)->send(new WelcomeMail([
                'prenom'       => $prenom,
                'nom'          => $nom,
                'email'        => $email,
                'matricule'    => $matricule ?? '',
                'role'         => $role,
                'password_raw' => $passwordRaw,
                'institution'  => $institution,
                'app_url'      => config('app.url', 'http://localhost'),
            ]));

            Log::info('[WelcomeMail] ✅ Envoyé à '.$email.' ('.$role.')');

        } catch (\Throwable $e) {
            Log::error('[WelcomeMail] ❌ Échec', [
                'email'  => $email,
                'erreur' => $e->getMessage(),
                'ligne'  => $e->getFile().':'.$e->getLine(),
            ]);

            // if (config('app.debug')) {
            //     throw $e;
            // }
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
