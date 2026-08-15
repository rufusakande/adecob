<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message de contact - {{ config('app.name') }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f8f9fa; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: linear-gradient(135deg, #2e8b57 0%, #1e5631 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 8px 0 0; opacity: 0.9; }
        .content { padding: 40px; }
        .field { margin-bottom: 16px; }
        .field strong { display: block; color: #2e8b57; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .field .value { margin: 0; background: #f8f9fa; padding: 10px 14px; border-radius: 6px; }
        .message-box { background: #f8f9fa; border-left: 4px solid #2e8b57; padding: 14px 16px; border-radius: 6px; white-space: pre-wrap; }
        .footer { background: #f8f9fa; padding: 30px; border-top: 1px solid #e9ecef; text-align: center; font-size: 12px; color: #666; }
        .footer p { margin: 6px 0; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📩 Nouveau message de contact</h1>
            <p>{{ config('app.name') }} — Plateforme d'Infrastructures</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p>Bonjour,</p>
            <p>Vous avez reçu un nouveau message via le formulaire de contact de la plateforme <strong>{{ config('app.name') }}</strong> :</p>

            <div class="field">
                <strong>Nom</strong>
                <p class="value">{{ $contactData['name'] }}</p>
            </div>
            <div class="field">
                <strong>Email</strong>
                <p class="value">{{ $contactData['email'] }}</p>
            </div>
            <div class="field">
                <strong>Téléphone</strong>
                <p class="value">{{ $contactData['phone'] }}</p>
            </div>
            <div class="field">
                <strong>Message</strong>
                <div class="message-box">{{ $contactData['message'] }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>{{ config('app.name') }} — Plateforme d'Infrastructures</strong></p>
            <p>Cet email a été généré automatiquement depuis le formulaire de contact du site.</p>
        </div>
    </div>
</body>
</html>
