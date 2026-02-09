<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class DeleteAccountController extends Controller
{
    public function show()
    {
        return Inertia::render('DeleteAccount');
    }

    public function request(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
        ]);

        $email = $request->input('email');

        // Log the deletion request
        Log::info('Account deletion requested', ['email' => $email]);

        // Send notification email to admin
        try {
            Mail::raw(
                "Se ha recibido una solicitud de eliminación de cuenta.\n\n".
                "Correo del usuario: {$email}\n".
                'Fecha de solicitud: '.now()->format('d/m/Y H:i:s')."\n\n".
                'Por favor, procesa esta solicitud eliminando la cuenta del usuario en Firebase Console '.
                'en un plazo máximo de 30 días hábiles.',
                function ($message) use ($email) {
                    $message->to('support@tehila.com.mx')
                        ->subject('Solicitud de eliminación de cuenta - Tehila')
                        ->replyTo($email)
                        ->cc('cancioneroruah@gmail.com');
                }
            );
        } catch (\Exception $e) {
            Log::warning('Could not send deletion request email', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }

        return back()->with('success', 'Tu solicitud de eliminación ha sido recibida.');
    }
}
