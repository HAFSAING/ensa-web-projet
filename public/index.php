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
        
        /* Styles pour la section d'authentification */
        .access-section {
            padding: 5rem 2rem;
            background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
            position: relative;
        }
    
        .access-section .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        /* Amélioration des onglets de type d'utilisateur */
        .user-type-tabs {
            display: flex;
            justify-content: center;
            gap: 2.5rem;
            margin-bottom: 3.5rem;
            position: relative;
            z-index: 1;
        }

        .user-tab {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.8rem 3rem;
            background: #fff;
            border: 2px solid #e0e0e0;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
            min-width: 180px;
        }

        .user-tab::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(29, 86, 107, 0.05) 0%, rgba(123, 186, 154, 0.08) 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: -1;
        }

        .user-tab:hover::before {
            opacity: 1;
        }

        .user-tab.active {
            border-color: var(--accent-color1);
            background-color: white;
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
        }

        .user-tab.active::before {
            opacity: 1;
            background: linear-gradient(45deg, rgba(29, 86, 107, 0.1) 0%, rgba(123, 186, 154, 0.15) 100%);
        }

        .user-tab:hover:not(.active) {
            border-color: #ccc;
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .tab-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.2rem;
            font-size: 2rem;
            transition: all 0.4s ease;
            position: relative;
        }

        .tab-icon::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            box-shadow: 0 0 0 8px rgba(123, 186, 154, 0.2);
            opacity: 0;
            transition: all 0.4s ease;
        }

        .user-tab.active .tab-icon::after {
            opacity: 1;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(123, 186, 154, 0.4);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(123, 186, 154, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(123, 186, 154, 0);
            }
        }

        .patient-icon {
            background: linear-gradient(135deg, var(--accent-color2) 0%, #5d8c9e 100%);
            color: white;
        }

        .doctor-icon {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #35647d 100%);
            color: white;
        }

        .user-tab.active .tab-icon {
            transform: scale(1.15);
        }

        .user-tab span {
            font-weight: 600;
            font-size: 1.25rem;
            color: var(--text-dark);
            transition: all 0.3s ease;
            position: relative;
        }

        .user-tab.active span {
            color: var(--primary-color);
        }

        .user-tab span::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 50%;
            transform: translateX(-50%) scaleX(0);
            width: 100%;
            height: 2px;
            background-color: var(--accent-color1);
            transition: transform 0.3s ease;
        }

        .user-tab.active span::after {
            transform: translateX(-50%) scaleX(1);
        }

        /* Amélioration du conteneur d'authentification */
        .auth-container {
            max-width: 950px;
            margin: 0 auto;
            position: relative;
            padding: 1rem;
        }

        .auth-content {
            display: none;
            padding: 3rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
            animation: fadeIn 0.6s ease;
            position: relative;
            overflow: hidden;
        }

        .auth-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, var(--accent-color1) 0%, var(--accent-color2) 100%);
        }

        .doctor-content::before {
            background: linear-gradient(90deg, var(--secondary-color) 0%, var(--accent-color3) 100%);
        }

        .auth-content.active {
            display: flex;
            flex-wrap: wrap;
            gap: 3rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Amélioration des boutons d'authentification */
        .auth-buttons {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .auth-button {
            width: 100%;
            padding: 1.2rem;
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 12px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.5px;
        }

        .auth-button i {
            margin-right: 12px;
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .auth-button:hover i {
            transform: translateX(-3px);
        }

        .auth-button::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1) translate(-50%, -50%);
            transform-origin: 50% 50%;
        }

        .auth-button:active::after {
            opacity: 0.4;
            transform: scale(20) translate(-50%, -50%);
            transition: transform 0.6s, opacity 0.6s;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-color1) 0%, #5faf7e 100%);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5faf7e 0%, var(--accent-color1) 100%);
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(123, 186, 154, 0.4);
        }

        .btn-outline-primary {
            background-color: transparent;
            color: var(--primary-color);
            border: 2px solid var(--accent-color1);
            position: relative;
            z-index: 1;
        }

        .btn-outline-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(123, 186, 154, 0.15) 0%, rgba(123, 186, 154, 0.05) 100%);
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-outline-primary:hover::before {
            opacity: 1;
        }

        .btn-outline-primary:hover {
            border-color: var(--accent-color1);
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(123, 186, 154, 0.2);
        }

        .btn-accent {
            background: linear-gradient(135deg, var(--accent-color3) 0%, #e63946 100%);
            color: white;
            border: none;
        }

        .btn-accent:hover {
            background: linear-gradient(135deg, #e63946 0%, var(--accent-color3) 100%);
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(204, 0, 0, 0.3);
        }

        .btn-outline-accent {
            background-color: transparent;
            color: var(--accent-color3);
            border: 2px solid var(--accent-color3);
            position: relative;
            z-index: 1;
        }

        .btn-outline-accent::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(230, 57, 70, 0.15) 0%, rgba(204, 0, 0, 0.05) 100%);
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-outline-accent:hover::before {
            opacity: 1;
        }

        .btn-outline-accent:hover {
            border-color: #e63946;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(204, 0, 0, 0.2);
        }

        /* Amélioration des connexions alternatives */
        .auth-alternative {
            text-align: center;
            margin-top: 1.5rem;
            position: relative;
        }

        .auth-alternative p {
            color: #777;
            margin-bottom: 1.2rem;
            position: relative;
            display: inline-block;
            padding: 0 15px;
            background: white;
            z-index: 1;
        }

        .auth-alternative::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 1px;
            background-color: #e0e0e0;
            z-index: 0;
        }

        .social-auth {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
        }

        .social-auth-btn {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background-color: #f7f7f7;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            color: #555;
            font-size: 1.25rem;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .social-auth-btn:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }

        .social-auth-btn.google:hover {
            background-color: #DB4437;
            color: white;
        }

        .social-auth-btn.facebook:hover {
            background-color: #4267B2;
            color: white;
        }

        .social-auth-btn.apple:hover {
            background-color: #000000;
            color: white;
        }

        /* Animation subtile pour les transitions */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 30px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        /* Ajout d'effets de hover pour la boîte d'information */
        .auth-info-box {
            background-color: rgba(29, 86, 107, 0.1);
            padding: 1.2rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }

        .auth-info-box:hover {
            background-color: rgba(29, 86, 107, 0.15);
            transform: translateX(5px);
        }

        .auth-info-box i {
            color: var(--primary-color);
            font-size: 1.6rem;
        }

        .auth-info-box p {
            color: var(--primary-color);
            font-size: 0.95rem;
            margin: 0;
            line-height: 1.5;
        }

        /* Media queries pour la responsivité */
        @media (max-width: 768px) {
            .user-type-tabs {
                flex-direction: column;
                align-items: center;
                gap: 1.5rem;
            }
            
            .user-tab {
                width: 80%;
                max-width: 300px;
            }
            
            .auth-content {
                padding: 2rem;
            }
            
            .form-row {
                flex-direction: column;
                gap: 1rem;
            }
        }

        @media (max-width: 480px) {
            .user-tab {
                width: 100%;
                padding: 1.5rem 2rem;
            }
            
            .tab-icon {
                width: 60px;
                height: 60px;
            }
            
            .auth-content {
                padding: 1.5rem;
            }
            
            .auth-button {
                padding: 1rem;
            }
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
        .auth-buttons a {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>

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
                       <li>
                            <a href="index.php" class="nav-link active">
                                <i class="fas fa-home"></i>
                                <span>Accueil</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="docFilterMedcin.php" class="nav-link">
                                <i class="fas fa-user-md"></i>
                                <span>Médecin</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="userPharmacie.php" class="nav-link">
                                <i class="fas fa-pills"></i>
                                <span>Pharmacie</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="medicaments.php" class="nav-link">
                                <i class="fas fa-capsules"></i>
                                <span>Médicaments</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Questions.php" class="nav-link">
                                <i class="fas fa-question-circle"></i>
                                <span>Questions</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="Magazine.php" class="nav-link">
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
                <a href="#access-cards" class="btn btn-primary">Commencer maintenant</a>
                <a href="#features" class="btn btn-outline">Découvrir nos services</a>
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
    </section>

<section class="access-section" id="access-cards">
    <div class="container">
        <h2 class="section-title">Accédez à votre espace personnel</h2>

        <div class="user-type-tabs">
            <button class="user-tab active" onclick="switchUserType('patient')">
                <div class="tab-icon patient-icon">👤</div>
                <span>Patient</span>
            </button>
            <button class="user-tab" onclick="switchUserType('doctor')">
                <div class="tab-icon doctor-icon">👨‍⚕️</div>
                <span>Médecin</span>
            </button>
        </div>
        
        <div class="auth-container">
            <!-- Partie Patient -->
            <div class="auth-content patient-content active">
                <div class="auth-info">
                    <h3>Espace Patient</h3>
                    <p>Accédez à votre dossier médical complet, gérez vos rendez-vous et suivez votre santé avec des outils interactifs personnalisés.</p>
                    <ul class="auth-features">
                        <li><i class="fas fa-check-circle"></i> Accès à votre historique médical</li>
                        <li><i class="fas fa-check-circle"></i> Prise de rendez-vous en ligne</li>
                        <li><i class="fas fa-check-circle"></i> Suivi des traitements</li>
                        <li><i class="fas fa-check-circle"></i> Notifications personnalisées</li>
                    </ul>
                </div>
                <div class="auth-actions">
                    <div class="auth-buttons">
                        <button class="btn btn-primary auth-button">
                            <i class="fas fa-sign-in-alt"></i><a href="userConnecter.php">Se connecter</a> 
                        </button>
                        <button class="btn btn-outline-primary auth-button">
                            <i class="fas fa-user-plus"></i><a href="userInscrire.php"> Créer un compte</a> 
                        </button>
                    </div>
                    <div class="auth-info-box">
                        <i class="fas fa-info-circle"></i>
                        <p>Vos données médicales sont protégées et sécurisées conformément aux réglementations en vigueur.</p>
                    </div>
                </div>
            </div>

            <div class="auth-content doctor-content">
                <div class="auth-info">
                    <h3>Espace Médecin</h3>
                    <p>Une interface sécurisée pour les professionnels de santé permettant de gérer les dossiers patients et d'optimiser le suivi médical.</p>
                    <ul class="auth-features">
                        <li><i class="fas fa-check-circle"></i> Gestion des dossiers patients</li>
                        <li><i class="fas fa-check-circle"></i> Planning de consultations</li>
                        <li><i class="fas fa-check-circle"></i> Prescription électronique</li>
                        <li><i class="fas fa-check-circle"></i> Messagerie sécurisée</li>
                    </ul>
                </div>
                <div class="auth-actions">
                    <div class="auth-buttons">
                        <button class="btn btn-accent auth-button">
                            <i class="fas fa-sign-in-alt"></i><a href="docConnecter.php">Se connecter</a> 
                        </button>
                        <button class="btn btn-outline-accent auth-button">
                            <i class="fas fa-user-plus"></i><a href="docInscrire.php"> Demande d'accès</a>
                        </button>
                    </div>
                    <div class="auth-info-box">
                        <i class="fas fa-info-circle"></i>
                        <p>L'accès médecin nécessite une vérification d'identité pour garantir la sécurité des données patients.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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
                <p><span class="contact-icon">📍</span> 123 Avenue de la Santé, 75001 casa</p>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        function switchUserType(type) {
            // Désactiver tous les onglets
            document.querySelectorAll('.user-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
          
            document.querySelectorAll('.auth-content').forEach(content => {
                content.classList.remove('active');
            });
            
  
            if (type === 'patient') {
                document.querySelector('.user-tab:nth-child(1)').classList.add('active');
                document.querySelector('.patient-content').classList.add('active');
            } else {
                document.querySelector('.user-tab:nth-child(2)').classList.add('active');
                document.querySelector('.doctor-content').classList.add('active');
            }
        }
        
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>
</body>
</html>