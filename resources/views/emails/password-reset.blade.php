<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de Mot de Passe</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #2e8b57 0%, #1e5631 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 40px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2e8b57;
        }
        .message {
            margin: 20px 0;
            line-height: 1.8;
        }
        .cta-button {
            display: inline-block;
            margin: 30px 0;
            padding: 15px 40px;
            background: linear-gradient(135deg, #2e8b57 0%, #1e5631 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(46, 139, 87, 0.3);
        }
        .link-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .link-section p {
            margin: 0 0 10px 0;
            font-size: 13px;
            color: #666;
        }
        .link-section code {
            background: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            word-break: break-all;
            display: block;
            color: #333;
            border: 1px solid #ddd;
        }
        .footer {
            background: #f8f9fa;
            padding: 30px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .footer p {
            margin: 10px 0;
        }
        .expiry {
            color: #dc3545;
            font-weight: bold;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning p {
            margin: 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔐 Réinitialisation de Mot de Passe</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">Bonjour {{ $userName }},</div>

            <div class="message">
                <p>Vous avez demandé la réinitialisation de votre mot de passe. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe.</p>
            </div>

            <!-- CTA Button -->
            <center>
                <a href="{{ $resetUrl }}" class="cta-button">
                    🔑 Réinitialiser mon Mot de Passe
                </a>
            </center>

            <!-- Link Section -->
            <div class="link-section">
                <p><strong>Ou copier ce lien dans votre navigateur:</strong></p>
                <code>{{ $resetUrl }}</code>
            </div>

            <!-- Warning -->
            <div class="warning">
                <p><strong>⏰ Important:</strong> Ce lien expirera dans <span class="expiry">60 minutes</span>. Agissez rapidement!</p>
            </div>

            <!-- Additional Message -->
            <div class="message" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                <p>Si vous n'avez pas demandé la réinitialisation de votre mot de passe, vous pouvez ignorer cet email. Votre compte reste sécurisé.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>ADECOB - Plateforme d'Infrastructures</strong></p>
            <p>Cet email a été envoyé à {{ $email }}</p>
            <p style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                Si vous continuez à avoir des problèmes, contactez le support: <strong>support@adecob.org</strong>
            </p>
        </div>
    </div>
</body>
</html>
