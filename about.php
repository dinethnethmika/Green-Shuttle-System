<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSBM Shuttle Service - Welcome</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* (Kept all your original styles same) */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #ffffff; color: #333; overflow-x: hidden; }
        
        /* --- NAVIGATION BAR (Desktop: Fixed) --- */
        nav { 
            width: 100%; 
            background-color: #28a745; 
            padding: 15px 50px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            position: fixed; /* Fixed on Desktop */
            top: 0; 
            z-index: 1000; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .logo { color: white; font-size: 24px; font-weight: bold; }
        .nav-links a { color: white; text-decoration: none; margin-left: 20px; font-weight: 500; transition: 0.3s; }
        .nav-links a:hover { opacity: 0.8; }
        
        /* Login Button Style */
        .btn-login { background: white; color: #28a745; padding: 8px 20px; border-radius: 20px; font-weight: bold; }
        .btn-login:hover { background: #f1f1f1; color: #218838; }

        /* --- HERO SECTION --- */
        .hero { 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            text-align: center; 
            background: url('1.jpg'); 
            background-size: cover; 
            background-position: center; 
            color: white; 
            padding: 20px; 
            position: relative; 
        }
        .hero::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.3); z-index: 1; }
        .hero-content { max-width: 900px; animation: fadeInUp 1s ease-out; position: relative; z-index: 2; }
        .hero h1 { font-size: 3.5rem; margin-bottom: 20px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
        .hero p { font-size: 1.3rem; margin-bottom: 30px; line-height: 1.6; text-shadow: 1px 1px 3px rgba(0,0,0,0.5); }

        /* --- SECTIONS --- */
        .section { padding: 80px 20px; max-width: 1200px; margin: 0 auto; text-align: center; }
        .section-title { color: #28a745; font-size: 2.5rem; margin-bottom: 50px; position: relative; display: inline-block; }
        .section-title::after { content: ''; display: block; width: 60px; height: 3px; background: #28a745; margin: 10px auto 0; }
        .grid-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 30px; }
        
        /* --- CARDS --- */
        .card { background: white; padding: 40px 30px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); transition: transform 0.3s; border-bottom: 5px solid #28a745; opacity: 0; transform: translateY(50px); }
        .card:hover { transform: translateY(-10px); }
        .card i { font-size: 3rem; color: #28a745; margin-bottom: 20px; }
        .card h3 { margin-bottom: 15px; color: #333; font-size: 1.5rem; }
        .card p { color: #666; line-height: 1.6; font-size: 1rem; }
        
        /* --- PARALLAX & OTHER --- */
        .parallax { 
            height: 400px; 
            background-attachment: fixed; 
            background-position: center; 
            background-repeat: no-repeat; 
            background-size: cover; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            color: white; 
            font-size: 2.5rem; 
            font-weight: bold; 
            text-shadow: 2px 2px 8px rgba(0,0,0,0.6); 
        }
        .img-break-1 { background-image: url('2.jpg'); } 
        .img-break-2 { background-image: url('3.jpg'); } 
        .mission-box { background: #f4fcf6; padding: 50px; border-radius: 15px; border-left: 10px solid #28a745; font-size: 1.2rem; line-height: 1.8; color: #555; max-width: 900px; margin: 0 auto; font-style: italic; }
        .contact-section { background: #333; color: white; padding: 60px 20px; text-align: center; }
        .contact-info { font-size: 1.2rem; margin-top: 20px; }
        .contact-info i { color: #28a745; margin-right: 10px; }
        
        /* --- ANIMATION --- */
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
        .visible { animation: fadeInUp 0.8s ease-out forwards; }
        .card:nth-child(1) { animation-delay: 0.1s; } .card:nth-child(2) { animation-delay: 0.2s; } .card:nth-child(3) { animation-delay: 0.3s; } .card:nth-child(4) { animation-delay: 0.4s; } .card:nth-child(5) { animation-delay: 0.5s; }

        /* =========================================
           MOBILE RESPONSIVENESS (FINAL FIXES)
           ========================================= */
        @media screen and (max-width: 768px) {
            nav {
                position: relative; /* SCROLLS AWAY WITH PAGE */
                flex-direction: column;
                padding: 15px;
                height: auto;
            }
            .logo {
                margin-bottom: 15px;
            }
            .nav-links {
                display: flex;
                flex-direction: column;
                width: 100%;
                text-align: center;
                gap: 10px;
            }
            .nav-links a {
                margin: 0;
                display: block;
                padding: 10px;
                background: rgba(255,255,255,0.1);
                border-radius: 5px;
            }
            
            /* --- TEXT SIZE & SPACING FIX FOR MOBILE --- */
            .hero {
                padding-top: 20px; /* Reset padding since Nav is not fixed */
                align-items: center; /* Center vertically again */
                height: 100vh;
            }
            .hero h1 { 
                font-size: 2rem; 
                line-height: 1.2;
            }
            .hero p { 
                font-size: 1rem; 
                padding: 0 10px; 
            }

            /* --- FIX FOR THE 2 PHOTOS (PARALLAX) --- */
            .parallax {
                height: 250px;
                background-attachment: scroll; /* DISABLING THIS FIXES THE ZOOM ISSUE */
                font-size: 1.5rem;
                background-position: center center;
            }
        }
    </style>
</head>
<body>

    <nav>
        <div class="logo"><i class="fas fa-bus-alt"></i> NSBM Shuttle</div>
        <div class="nav-links">
            <a href="#mission">Mission</a>
            <a href="#services">Services</a>
            <a href="#why-us">Why Us</a>
            <a href="#contact">Contact</a>
            <a href="login.php" class="btn-login" style="background: white; color: #28a745;">Login Portal</a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to the NSBM Shuttle Service Portal</h1>
            <p>Your all-in-one platform for checking shuttle schedules, booking seats, and accessing special event shuttles. Our goal is to make your daily travel safe, comfortable, and reliable.</p>
            
            <a href="login.php" style="background: #28a745; color: white; padding: 15px 35px; border-radius: 30px; text-decoration: none; font-weight: bold; font-size: 1.1rem; transition: 0.3s; margin-top: 20px; display: inline-block; border: 2px solid white;">
                Book a Ride Now
            </a>
        </div>
    </section>

    <section id="mission" class="section">
        <h2 class="section-title">Our Mission</h2>
        <div class="mission-box scroll-animate">
            "Our mission is to deliver a reliable and efficient transport system that supports the mobility needs of students and staff while promoting punctuality, safety, and comfort. We aim to enhance the campus experience through a user-friendly transport management solution."
        </div>
    </section>

    <div class="parallax img-break-1">Journey with Comfort</div>

    <section id="services" class="section">
        <h2 class="section-title">What This Website Provides</h2>
        <div class="grid-container">
            <div class="card scroll-animate"><i class="fas fa-calendar-alt"></i><h3>Daily Schedules</h3><p>Real-time updates on daily shuttle routes.</p></div>
            <div class="card scroll-animate"><i class="fas fa-chair"></i><h3>Seat Booking</h3><p>Secure your seat in advance.</p></div>
            <div class="card scroll-animate"><i class="fas fa-star"></i><h3>Event Shuttles</h3><p>Special transport for university events.</p></div>
            <div class="card scroll-animate"><i class="fas fa-info-circle"></i><h3>Driver Info</h3><p>Access info about drivers and vehicles.</p></div>
            <div class="card scroll-animate"><i class="fas fa-life-ring"></i><h3>24/7 Support</h3><p>Emergency contact and support.</p></div>
        </div>
    </section>

    <div class="parallax img-break-2">Safe. Reliable. Punctual.</div>

    <section id="why-us" class="section" style="background-color: #f9fff9;">
        <h2 class="section-title">Why Use NSBM Shuttle?</h2>
        <div class="grid-container">
            <div class="card scroll-animate"><i class="fas fa-map-marked-alt"></i><h3>Convenient Routes</h3><p>Covering all major pickup points.</p></div>
            <div class="card scroll-animate"><i class="fas fa-clock"></i><h3>Always On Time</h3><p>Strict adherence to schedules.</p></div>
            <div class="card scroll-animate"><i class="fas fa-mobile-alt"></i><h3>Easy Online Booking</h3><p>Book your ride from anywhere.</p></div>
            <div class="card scroll-animate"><i class="fas fa-user-shield"></i><h3>Safe Drivers</h3><p>Verified professional drivers.</p></div>
        </div>
    </section>

    <section id="contact" class="contact-section">
        <h2 style="margin-bottom: 20px; color: #28a745;">Contact Us</h2>
        <p>For support, timing issues, or lost items, please reach out to our desk.</p>
        <div class="contact-info">
            <p><i class="fas fa-phone"></i> Shuttle Service Desk: <strong>+94 xxx xxx xxx</strong></p>
            <p style="margin-top: 10px;"><i class="fas fa-envelope"></i> Email: <strong>shuttle@nsbm.ac.lk</strong></p>
        </div>
    </section>

    <footer style="background: #222; color: #777; text-align: center; padding: 20px; font-size: 0.9rem;">
        <p>&copy; 2025 NSBM Green Shuttle. All Rights Reserved.</p>
    </footer>

    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.scroll-animate').forEach((el) => {
            observer.observe(el);
        });
    </script>

</body>
</html>