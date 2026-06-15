@extends('layouts.app')

@section('title', 'Contactez-Nous')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --green-color: #2e8b57;
            --yellow-color: #ffd700;
            --red-color: #dc3545;
            --dark-green: #1e5631;
            --light-yellow: #fff9c4;
            --light-red: #ffcdd2;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 1200px;
        }
        
        .contact-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            margin: 30px auto;
            padding: 0;
            transition: transform 0.3s ease;
        }
        
        .contact-container:hover {
            transform: translateY(-5px);
        }
        
        .section-header {
            background: linear-gradient(135deg, var(--green-color) 0%, var(--dark-green) 100%);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .section-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(255, 215, 0, 0.1) 0%, transparent 30%),
                radial-gradient(circle at 80% 70%, rgba(220, 53, 69, 0.1) 0%, transparent 30%);
            pointer-events: none;
        }
        
        .section-header h1 {
            margin: 0;
            font-size: 2.8rem;
            font-weight: 800;
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.4);
            position: relative;
            z-index: 1;
        }
        
        .section-header::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 8px;
            background: linear-gradient(90deg, var(--green-color), var(--yellow-color), var(--red-color));
            border-radius: 4px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        .contact-content {
            padding: 50px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 14px 18px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #f8f9fa;
        }
        
        .form-control:focus {
            border-color: var(--green-color);
            box-shadow: 0 0 0 0.2rem rgba(46, 139, 87, 0.25);
            outline: 0;
            background-color: white;
            transform: scale(1.02);
        }
        
        .form-label {
            font-weight: 700;
            color: var(--dark-green);
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        
        .form-label i {
            margin-right: 8px;
            color: var(--green-color);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--green-color) 0%, var(--dark-green) 100%);
            border: none;
            padding: 14px 40px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(46, 139, 87, 0.4);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 2px solid rgba(255, 215, 0, 0.3);
        }
        
        .btn-success:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 12px 25px rgba(46, 139, 87, 0.5);
            background: linear-gradient(135deg, var(--dark-green) 0%, #1a4d2e 100%);
            border-color: rgba(255, 215, 0, 0.5);
        }
        
        .btn-success:active {
            transform: translateY(0) scale(1);
        }
        
        .contact-info {
            background: linear-gradient(135deg, var(--light-yellow) 0%, #fffde7 50%, var(--light-red) 100%);
            border-radius: 15px;
            padding: 35px;
            height: 100%;
            border: 3px solid var(--yellow-color);
            position: relative;
            overflow: hidden;
        }
        
        .contact-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--green-color), var(--yellow-color), var(--red-color));
        }
        
        .contact-info h4 {
            color: var(--dark-green);
            margin-bottom: 30px;
            font-size: 1.7rem;
            font-weight: 800;
            position: relative;
            padding-bottom: 12px;
            display: inline-block;
        }
        
        .contact-info h4::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--green-color), var(--yellow-color));
            border-radius: 2px;
        }
        
        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            border-left: 4px solid var(--green-color);
        }
        
        .contact-item:hover {
            transform: translateX(8px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            background: white;
            border-left-color: var(--red-color);
        }
        
        .contact-icon {
            background: linear-gradient(135deg, var(--green-color) 0%, var(--dark-green) 100%);
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 18px;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(46, 139, 87, 0.3);
            font-size: 1.2rem;
        }
        
        .contact-text {
            color: var(--dark-green);
            font-size: 1.1rem;
            line-height: 1.6;
        }
        
        .contact-text strong {
            color: var(--dark-green);
            font-weight: 800;
        }
        
        .contact-text a {
            color: var(--red-color);
            text-decoration: none;
            font-weight: 800;
            transition: all 0.3s ease;
            border-bottom: 2px dashed var(--red-color);
            padding-bottom: 2px;
        }
        
        .contact-text a:hover {
            color: #c82333;
            border-bottom: 2px solid var(--red-color);
            transform: scale(1.05);
        }
        
        .map-container {
            margin-top: 35px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border: 3px solid var(--yellow-color);
        }
        
        .map-container iframe {
            border: none;
            height: 300px;
            width: 100%;
            transition: height 0.3s ease;
        }
        
        @media (max-width: 768px) {
            .section-header h1 {
                font-size: 2.2rem;
            }
            
            .contact-content {
                padding: 30px 20px;
            }
            
            .contact-info {
                margin-top: 25px;
                padding: 25px;
            }
            
            .btn-success {
                width: 100%;
                justify-content: center;
            }
            
            .contact-item {
                padding: 12px;
            }
            
            .contact-text {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .section-header {
                padding: 25px 15px;
            }
            
            .contact-content {
                padding: 20px 15px;
            }
            
            .section-header h1 {
                font-size: 1.8rem;
            }
            
            .contact-info h4 {
                font-size: 1.4rem;
            }
        }
        
        .animate-in {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .delay-1 {
            animation-delay: 0.1s;
        }
        
        .delay-2 {
            animation-delay: 0.2s;
        }
        
        .delay-3 {
            animation-delay: 0.3s;
        }
        
        .delay-4 {
            animation-delay: 0.4s;
        }
        
        .floating {
            animation: floating 4s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <br>
        <br>
        <br>
        <br>
        <br>
        <div class="contact-container">
            <div class="section-header">
                <h1 class="floating">Contactez-Nous</h1>
            </div>
            <div class="contact-content">
                <div class="row g-5">
                    <!-- Contact Form -->
                    <div class="col-lg-6">
                        <form method="POST" action="{{ route('contact.submit') }}" class="animate-in delay-1">
                            @csrf
                            <div class="mb-4">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user"></i> Nom complet
                                </label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Votre nom complet" required>
                            </div>
                            <div class="mb-4">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Adresse Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Votre email" required>
                            </div>
                            <div class="mb-4">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone"></i> Numéro de téléphone
                                </label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Votre numéro de téléphone" required>
                            </div>
                            <div class="mb-4">
                                <label for="message" class="form-label">
                                    <i class="fas fa-comment"></i> Message
                                </label>
                                <textarea class="form-control" id="message" name="message" rows="5" placeholder="Votre message" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane"></i> Envoyer
                            </button>
                        </form>
                    </div>

                    <!-- Contact Information -->
                    <div class="col-lg-6">
                        <div class="contact-info animate-in delay-2">
                            <h4><i class="fas fa-info-circle"></i> Nos Coordonnées</h4>
                            
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="contact-text">
                                    <strong>Adresse:</strong> BP 01 N'Dali - République du Bénin
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div class="contact-text">
                                    <strong>Téléphone:</strong> <a href="tel:+2290195647373">0195647373</a>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-text">
                                    <strong>Email:</strong> <a href="mailto:secretariatadecob@yahoo.fr">secretariatadecob@yahoo.fr</a>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="contact-text">
                                    <strong>Horaires d'ouverture:</strong> Lun - Ven : 08h - 12h30 et 15h - 17h30
                                </div>
                            </div>
                            
                            <div class="map-container animate-in delay-3">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3930.9455596452967!2d2.714801573762502!3d9.854934675516681!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x10326196ca32b159%3A0x7c16d31fc8a99fbd!2sADECOB%20-%20Borgou!5e0!3m2!1sfr!2sbj!4v1775246108007!5m2!1sfr!2sbj" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formElements = document.querySelectorAll('.animate-in');
            formElements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                setTimeout(() => {
                    el.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>

@endsection
