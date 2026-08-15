<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Show the contact form.
     */
    public function show()
    {
        return view('contact');
    }

    /**
     * Handle the contact form submission.
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'required|string',
        ]);

        // Envoi de l'email via ContactFormMail Mailable vers le destinataire configuré (.env)
        \Mail::to(config('mail.contact_recipient'))->send(new \App\Mail\ContactFormMail($validated));

        return redirect()->back()->with('success', 'Merci pour votre message. Nous vous contacterons bientôt.');
    }
}
