<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediStatView - Votre Santé, Notre Priorité</title>
    <style>
        :root {
            --primary-color: #1d566b;
            --secondary-color: #216b4e;
            --accent-color1: #7bba9a;
            --accent-color2: #86b3c3;
            --accent-color3: #CC0000;
            --light-bg: #f8f9fa;
            --text-dark: #333;
            --text-light: #fff;
            --shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--light-bg);
            line-height: 1.6;
            color: var(--text-dark);
        }
                


/* Styles pour le header avec navigation et icônes */
header {
    background-color: var(--primary-color);
    color: var(--text-light);
    padding: 1rem 2rem;
    box-shadow: var(--shadow);
    position: sticky;
    top: 0;
    z-index: 100;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}


/* Navigation principale */
.main-nav {
    flex-grow: 1;
    display: flex;
    justify-content: center;
}

.nav-list {
    display: flex;
    list-style: none;
    gap: 0.5rem;
    margin: 0;
    padding: 0;
    flex-wrap: wrap;
    justify-content: center;
}

.nav-item {
    position: relative;
}

.nav-link {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: var(--text-light);
    text-decoration: none;
    padding: 0.7rem 1rem;
    font-weight: 500;
    border-radius: 6px;
    transition: all 0.3s ease;
    text-align: center;
}

.nav-link i {
    font-size: 1.3rem;
    margin-bottom: 0.3rem;
    color: var(--accent-color2);
    transition: all 0.3s ease;
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.nav-link:hover i {
    color: var(--accent-color1);
    transform: translateY(-2px);
}

.nav-link.active {
    background-color: rgba(255, 255, 255, 0.15);
}

.nav-link.active i {
    color: var(--accent-color1);
}

.nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 50%;
    transform: translateX(-50%);
    width: 30px;
    height: 3px;
    background-color: var(--accent-color1);
    border-radius: 10px;
}

.nav-buttons {
    display: flex;
    gap: 1rem;
}

.btn {
    padding: 0.7rem 1.4rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.btn-outline {
    background: transparent;
    border: 2px solid var(--text-light);
    color: var(--text-light);
}

.btn-outline:hover {
    background-color: rgba(255,255,255,0.1);
    transform: translateY(-2px);
}

.btn-primary {
    background-color: var(--accent-color1);
    color: var(--primary-color);
}

.btn-primary:hover {
    background-color: #6aa889;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* Responsive design pour le header */
@media (max-width: 992px) {
    .header-content {
        flex-direction: column;
        padding: 1rem 0;
    }
    
    .main-nav {
        order: 3;
        width: 100%;
        margin-top: 1rem;
    }
    
    .nav-list {
        justify-content: space-around;
        width: 100%;
    }
    
    .nav-link {
        padding: 0.5rem 0.8rem;
        font-size: 0.9rem;
    }
    
    .nav-link i {
        font-size: 1.1rem;
    }
}

@media (max-width: 768px) {
    .nav-list {
        flex-wrap: wrap;
        gap: 0.2rem;
    }
    
    .nav-item {
        flex-basis: 33.333%;
    }
    
    .nav-buttons {
        width: 100%;
        justify-content: center;
        margin-top: 1rem;
    }
}

@media (max-width: 576px) {
    .nav-item {
        flex-basis: 50%;
    }
    
    .nav-link {
        font-size: 0.8rem;
    }
    
    .btn {
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
    }
}





        .nav-buttons {
            display: flex;
            gap: 1rem;
        }
        
        .btn {
            padding: 0.7rem 1.4rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid var(--text-light);
            color: var(--text-light);
        }
        
        .btn-outline:hover {
            background-color: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background-color: var(--accent-color1);
            color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #6aa889;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .btn-accent {
            background-color: var(--accent-color3);
            color: var(--text-light);
        }
        
        .btn-accent:hover {
            background-color: #b80000;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--text-light);
            padding: 5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='rgba(255,255,255,0.05)' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.5;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .hero p {
            font-size: 1.3rem;
            max-width: 700px;
            margin: 0 auto 2.5rem;
            opacity: 0.9;
        }
        
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        
        /* Access Cards Section */
        .access-section {
            padding: 5rem 2rem;
            background-color: var(--light-bg);
            position: relative;
        }
        
        .access-cards {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .access-card {
            background-color: white;
            border-radius: 12px;
            padding: 2.5rem;
            width: 350px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            text-align: center;
            display: flex;
            flex-direction: column;
        }
        
        .access-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .access-card h3 {
            color: var(--primary-color);
            margin-bottom: 1.2rem;
            font-size: 1.8rem;
        }
        
        .access-card p {
            margin-bottom: 2rem;
            color: #555;
            font-size: 1.1rem;
            flex-grow: 1;
        }
        
        .access-card .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
        }
        
        .patient-icon {
            background-color: var(--accent-color2);
            color: white;
        }
        
        .doctor-icon {
            background-color: var(--secondary-color);
            color: white;
        }

        .auth-button {
            width: 100%;
            padding: 1rem;
            font-size: 1.1rem;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .auth-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .auth-button .auth-icon {
            margin-right: 10px;
        }
        
        .toggle-link {
            margin-top: 1rem;
            color: var(--secondary-color);
            text-decoration: none;
            font-size: 0.9rem;
            display: block;
        }
        
        .toggle-link:hover {
            text-decoration: underline;
        }
        
        /* Features Section */
        .section {
            padding: 5rem 2rem;
            background-color: white;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3.5rem;
            color: var(--primary-color);
            position: relative;
            font-size: 2.2rem;
            font-weight: 700;
        }
        
        .section-title::after {
            content: "";
            display: block;
            width: 80px;
            height: 4px;
            background-color: var(--accent-color1);
            margin: 0.8rem auto 0;
            border-radius: 2px;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .feature-card {
            background-color: var(--light-bg);
            border-radius: 12px;
            padding: 2.5rem 1.8rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.1);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background-color: var(--accent-color2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.8rem;
            color: white;
            font-size: 2rem;
            box-shadow: 0 6px 12px rgba(134, 179, 195, 0.3);
        }
        
        .feature-card h3 {
            color: var(--secondary-color);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .feature-card p {
            color: #555;
            font-size: 1.05rem;
        }
        
        /* Testimonials Section */
        .testimonials {
            padding: 5rem 2rem;
            background-color: var(--light-bg);
        }
        
        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .testimonial-card {
            background-color: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: var(--shadow);
            position: relative;
        }
        
        .testimonial-text {
            margin-bottom: 1.5rem;
            font-style: italic;
            color: #555;
            font-size: 1.05rem;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
        }
        
        .testimonial-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--accent-color1);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 1rem;
        }
        
        .testimonial-info h4 {
            color: var(--primary-color);
            margin-bottom: 0.2rem;
        }
        
        .testimonial-info p {
            color: #777;
            font-size: 0.9rem;
        }
        
        /* Footer */
        footer {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 4rem 2rem 2rem;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
        }
        
        .footer-column h3 {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .footer-column h3::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background-color: var(--accent-color1);
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 0.8rem;
        }
        
        .footer-links a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: var(--accent-color1);
        }
        
        .footer-contact p {
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
        }
        
        .contact-icon {
            margin-right: 0.8rem;
            color: var(--accent-color1);
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .social-icon:hover {
            background-color: var(--accent-color1);
            transform: translateY(-3px);
        }
        
        .copyright {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            border-radius: 10px;
            padding: 2rem;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
            animation: modalFadeIn 0.3s;
        }
        
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.5rem;
            cursor: pointer;
            background: none;
            border: none;
            color: #777;
        }
        
        .modal-title {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.5rem;
        }
        
        .auth-form .form-group {
            margin-bottom: 1.2rem;
        }
        
        .auth-form label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
        }
        
        .auth-form input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .auth-form input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 2px rgba(33, 107, 78, 0.2);
        }
        
        .auth-form .forgot-password {
            text-align: right;
            margin-bottom: 1rem;
        }
        
        .auth-form .forgot-password a {
            color: var(--secondary-color);
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .auth-form .submit-btn {
            width: 100%;
            padding: 0.8rem;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .auth-form .submit-btn:hover {
            background-color: #185a40;
        }
        
        .auth-form .register-link {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        
        .auth-form .register-link a {
            color: var(--secondary-color);
            text-decoration: none;
        }
        
        .tabs {
            display: flex;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }
        
        .tab-btn {
            padding: 10px 20px;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            color: #777;
            flex: 1;
            transition: all 0.3s ease;
        }
        
        .tab-btn.active {
            color: var(--secondary-color);
            border-bottom-color: var(--secondary-color);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
                padding: 0.5rem 0;
            }
            
            .nav-buttons {
                width: 100%;
                justify-content: center;
            }
            
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .section-title {
                font-size: 1.8rem;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
            }
            
            .access-card {
                width: 100%;
                max-width: 350px;
            }
        }
        
        @media (max-width: 480px) {
            .btn {
                padding: 0.6rem 1.2rem;
                font-size: 0.9rem;
            }
            
            .hero {
                padding: 3rem 1rem;
            }
            
            .section {
                padding: 3rem 1rem;
            }
            
            .feature-card, .access-card, .testimonial-card {
                padding: 1.5rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                gap: 1rem;
                align-items: center;
            }
            
            .cta-buttons .btn {
                width: 100%;
                max-width: 300px;
            }
        }
                /* Styles améliorés pour le footer */
        footer {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 4rem 2rem 2rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
        }

        .footer-column h3 {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .footer-column h3::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background-color: var(--accent-color1);
        }

        .footer-column p {
            color: #ccc;
            margin-bottom: 1.2rem;
            line-height: 1.6;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s ease;
            display: inline-block;
        }

        .footer-links a:hover {
            color: var(--accent-color1);
            transform: translateX(5px);
        }

        .footer-contact p {
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
        }

        .contact-icon {
            margin-right: 0.8rem;
            color: var(--accent-color1);
            display: inline-flex;
            width: 24px;
            justify-content: center;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            color: #fff;
            text-decoration: none;
        }

        .social-icon:hover {
            background-color: var(--accent-color1);
            transform: translateY(-3px);
        }

        .google-map {
            width: 100%;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .copyright {
            text-align: center;
            padding-top: 2rem;
            margin-top: 3rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #ccc;
        }

        .legal-links {
            margin-top: 1rem;
        }

        .legal-links a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s ease;
            font-size: 0.9rem;
        }

        .legal-links a:hover {
            color: var(--accent-color1);
        }

        /* Responsive design pour le footer */
        @media (max-width: 768px) {
            .footer-content {
                grid-template-columns: 1fr 1fr;
            }
            
            .footer-column.footer-map {
                grid-column: span 2;
            }
        }

        @media (max-width: 576px) {
            .footer-content {
                grid-template-columns: 1fr;
            }
            
            .footer-column.footer-map {
                grid-column: span 1;
            }
            
            .legal-links {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }
            
        }

    </style>
</head>
<body>

    <!-- Header avec navigation et icônes -->
    <header>
        <div class="container">
            <div class="header-content">
                        <svg width="180" height="50" viewBox="0 0 180 50">
                            <rect x="10" y="15" width="20" height="20" fill="#77c4a0" />
                            <polygon points="30,15 40,25 30,35" fill="#9fdec0" />
                            <text x="50" y="25" fill="#ffffff" font-size="18" font-weight="bold">MediStatView</text>
                            <text x="50" y="40" fill="#9fdec0" font-size="12">SERVICES</text>
                        </svg>

                <nav class="main-nav">
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="docFilterMedcin.php" class="nav-link active">
                                <i class="fas fa-user-md"></i>
                                <span>Médecin</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-pills"></i>
                                <span>Pharmacie</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-question-circle"></i>
                                <span>Questions</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-book-medical"></i>
                                <span>Magazine</span>
                            </a>
                        </li>
                    </ul>
                </nav>

            </div>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Votre santé connectée en toute sécurité</h1>
            <p>MediStatView vous permet d'accéder à votre dossier médical, de suivre vos rendez-vous et de visualiser l'évolution de votre santé depuis n'importe où.</p>
            <div class="cta-buttons">
                <button class="btn btn-primary" onclick="openModal('registerModal')">Commencer maintenant</button>
                <a href="#features" class="btn btn-outline">Découvrir nos services</a>
            </div>
        </div>
    </section>

    <section class="access-section" id="access-cards">
        <h2 class="section-title">Accédez à votre espace personnalisé</h2>
        <div class="access-cards">
            <div class="access-card">
                <div class="icon-circle patient-icon">👤</div>
                <h3>Espace Patient</h3>
                <p>Accédez à votre dossier médical complet, gérez vos rendez-vous et suivez votre santé avec des outils interactifs personnalisés.</p>
                <button class="btn btn-primary auth-button" onclick="openModal('registerModal')">
                    <span class="auth-icon">✓</span>Créer un compte patient
                </button>
            </div>
            <div class="access-card">
                <div class="icon-circle doctor-icon">👨‍⚕️</div>
                <h3>Espace Médecin</h3>
                <p>Une interface sécurisée pour les professionnels de santé permettant de gérer les dossiers patients et d'optimiser le suivi médical.</p>
                <button class="btn btn-accent auth-button" onclick="openModal('doctorModal')">
                    <span class="auth-icon">✓</span>Accès professionnel
                </button>
            </div>
        </div>
    </section>

    <section class="section" id="features">
        <h2 class="section-title">Nos Services</h2>
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>Dossier Médical Complet</h3>
                <p>Accédez à l'ensemble de votre historique médical, vos analyses, diagnostics et prescriptions en quelques clics. Gardez toutes vos informations médicales au même endroit.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Gestion des Rendez-vous</h3>
                <p>Prenez rendez-vous en ligne avec vos médecins, recevez des rappels automatiques et gérez facilement votre calendrier médical sans attente téléphonique.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Suivi Personnalisé</h3>
                <p>Visualisez l'évolution de vos paramètres de santé avec des graphiques interactifs. Comprenez mieux votre état de santé avec des statistiques claires.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💬</div>
                <h3>Messagerie Sécurisée</h3>
                <p>Communiquez directement avec votre équipe médicale via notre messagerie cryptée. Posez vos questions et recevez des réponses rapidement.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📱</div>
                <h3>Application Mobile</h3>
                <p>Accédez à tous vos services de santé depuis votre smartphone grâce à notre application mobile intuitive, disponible sur iOS et Android.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔔</div>
                <h3>Alertes & Notifications</h3>
                <p>Recevez des rappels pour vos médicaments, rendez-vous médicaux et examens périodiques afin de ne jamais manquer un élément important de votre suivi.</p>
            </div>
        </div>
    </section
    <!-- Footer avec Google Maps et informations de contact -->
    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>MediStatView</h3>
                <p>Votre plateforme de santé connectée pour un suivi médical optimal en toute sécurité.</p>
                <div class="social-links">
                    <a href="#" class="social-icon">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-icon">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-icon">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="social-icon">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
            
            <div class="footer-column footer-links-column">
                <h3>Liens Rapides</h3>
                <ul class="footer-links">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="#features">Nos Services</a></li>
                    <li><a href="#access-cards">Espaces Personnalisés</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Actualités Santé</a></li>
                    <li><a href="#">À Propos</a></li>
                </ul>
            </div>
            
            <div class="footer-column footer-contact">
                <h3>Contact</h3>
                <p><span class="contact-icon">📍</span> 123 Avenue de la Santé, 75001 Paris</p>
                <p><span class="contact-icon">📞</span> +212 5 23 45 67 89</p>
                <p><span class="contact-icon">✉️</span> contact@gmail.com</p>
                <p><span class="contact-icon">🕒</span> Lun - Ven: 9h00 - 18h00</p>
            </div>
            
            <div class="footer-column footer-map">
                <h3>Nous Trouver</h3>
                <div class="google-map">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.142047342751!2d2.3345!3d48.8608!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDjCsDA5JzUxLjgiTiAywrAyMCcwNi42IkU!5e0!3m2!1sfr!2sfr!4v1651234567890!5m2!1sfr!2sfr" 
                        width="100%" 
                        height="200" 
                        style="border:0; border-radius:8px;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
        
        <div class="copyright">
            <p>&copy; 2025 MediStatView. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Ajouter Font Awesome pour les icônes des réseaux sociaux -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</html>