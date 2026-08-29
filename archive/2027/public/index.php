<?php
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/auth.php';
session_start();

checkPersistentLogin();

$aboutSlides = [
    [
        'src' => '../images/home/hero.jpg',
        'alt' => 'Hero ACASĂ',
        'title' => 'ACASĂ',
        'description' => 'Crăciunul Tinerilor 2025'
    ],
    [
        'src' => '../images/travel/maramures-church-room.jpg',
        'alt' => 'Sala de tineret din Maramureș',
        'title' => 'Închinare prin Cuvânt',
        'description' => 'Tabăra Maramureș 2024'
    ],
    [
        'src' => '../images/travel/maramures-hero-church-room.jpg',
        'alt' => 'Camera de rugăciune din Maramureș',
        'title' => 'Închinare prin rugăciune și cântare',
        'description' => 'Tabăra Maramureș 2024'
    ]
];

$missionSlides = [
    [
        'src' => '../images/community/targ1dec-cooks.jpg',
        'alt' => 'Pregătiri pentru eveniment',
        'title' => 'Comunitate și slujire',
        'description' => 'Targul Speranței - 1 Decembrie'
    ],
    [
        'src' => '../images/community/targ1dec-pork-on-tables.jpg',
        'alt' => 'Mâncare la Targul Speranței',
        'title' => 'Mâncare pentru trup și suflet',
        'description' => 'Targul Speranței - 1 Decembrie'
    ],
    [
        'src' => '../images/faith/Walk-With-Jesus-Soldier.jpg',
        'alt' => 'Soldat cu Isus',
        'title' => 'Rugăciune și mărturie',
        'description' => 'Walk With Jesus - 2024'
    ]
];

$momentSlides = [
    ['src' => '../images/travel/maramures-group-photo.jpg', 'alt' => 'Fotografie de grup din Maramureș', 'title' => 'Vișeu', 'description' => 'Tabăra Maramureș 2024'],
    ['src' => '../images/travel/poland-landscape.jpg', 'alt' => 'Peisaj din Polonia', 'title' => 'Zakopane', 'description' => 'Tabăra Polonia 2025'],
    ['src' => '../images/travel/poland-city-landscape.jpg', 'alt' => 'Peisaj urban din Polonia', 'title' => 'Kracovia', 'description' => 'Tabăra Polonia 2025'],
    ['src' => '../images/community/targ1dec-pork-slashed.jpg', 'alt' => 'Preparare pentru Targul Speranței', 'title' => 'Șiștarovăț', 'description' => 'Targul Speranței - 1 Decembrie'],
    ['src' => '../images/community/targ1dec-Tibi-workshop.jpg', 'alt' => 'Atelier la Targul Speranței', 'title' => '#tineretSperanta - Atelier Tibi', 'description' => 'Targul Speranței - 1 Decembrie']
];
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>#tineretSperanta - ACASĂ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --camp-navy: #101820;
            --camp-navy-2: #183153;
            --camp-red: #c8102e;
            --camp-red-dark: #9d0c24;
            --camp-blue: #1d5ea8;
            --camp-soft: #edf3fa;
            --camp-card: rgba(255,255,255,0.08);
            --camp-border: rgba(255,255,255,0.12);
            --camp-shadow: rgba(16,24,32,0.2);
        }

        body {
            background: linear-gradient(180deg, #f4f7fb 0%, #edf3fa 100%);
            color: var(--camp-navy);
            font-family: Arial, sans-serif;
        }

        .camp-navbar {
            background: linear-gradient(135deg, var(--camp-navy) 0%, var(--camp-navy-2) 50%, var(--camp-red) 100%);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 8px 24px rgba(16,24,32,0.18);
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: 0.02em;
        }

        .nav-link {
            font-weight: 600;
            color: rgba(255,255,255,0.9) !important;
        }

        .nav-link:hover {
            color: #ffffff !important;
        }

        .hero {
            position: relative;
            min-height: 440px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-size: cover;
            background-position: center;
            margin-top: 0;
            color: white;
            text-shadow: 0 3px 10px rgba(0,0,0,0.5);
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(16,24,32,0.8) 0%, rgba(24,49,83,0.72) 42%, rgba(200,16,46,0.5) 100%);
        }

        .hero .container {
            position: relative;
            z-index: 1;
        }

        .hero h1 {
            font-weight: 900;
            letter-spacing: 0.02em;
            margin-bottom: 12px;
        }

        .hero .lead {
            font-size: 1.2rem;
            opacity: 0.95;
        }

        .hero .btn {
            background: #fff;
            color: var(--camp-navy);
            border: 0;
            font-weight: 800;
            border-radius: 12px;
            padding: 0.9rem 1.5rem;
            box-shadow: 0 12px 24px rgba(0,0,0,0.18);
        }

        .theme-panel {
            background: linear-gradient(180deg, rgba(16,24,32,0.96) 0%, rgba(24,49,83,0.98) 100%);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            box-shadow: 0 18px 35px rgba(16,24,32,0.18);
        }

        .camp-card {
            border: 1px solid rgba(16,24,32,0.05);
            border-radius: 20px;
            box-shadow: 0 14px 32px rgba(16,24,32,0.08);
            background: #fff;
            overflow: hidden;
        }

        .camp-card .card-body {
            padding: 1.75rem;
        }

        .camp-card h5 {
            font-weight: 800;
            margin-bottom: 0.8rem;
        }

        .camp-card .btn {
            border-radius: 12px;
            font-weight: 700;
            padding: 0.7rem 1.2rem;
        }

        .section-title {
            font-weight: 900;
            letter-spacing: 0.01em;
            color: var(--camp-navy);
        }

        .section-title i {
            color: var(--camp-red);
        }

        .feature-list li {
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(16,24,32,0.06);
            font-weight: 600;
            color: var(--camp-navy-2);
        }

        .feature-list li:last-child {
            border-bottom: none;
        }

        .feature-list i {
            color: var(--camp-red);
        }

        .image-frame {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 18px 35px rgba(16,24,32,0.12);
            border: 1px solid rgba(16,24,32,0.08);
        }

        .theme-section {
            background: linear-gradient(180deg, rgba(237,243,250,1) 0%, rgba(255,255,255,1) 100%);
        }

        #bibletracker,
        .theme-section-alt {
            background: linear-gradient(180deg, rgba(16,24,32,0.96) 0%, rgba(24,49,83,0.98) 100%);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            box-shadow: 0 18px 35px rgba(16,24,32,0.18);
            overflow: hidden;
        }

        .theme-section-alt .section-title,
        .theme-section-alt p,
        .theme-section-alt .lead {
            color: #fff;
        }

        .theme-section-alt .card {
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 18px 35px rgba(0,0,0,0.08);
        }

        .theme-section-alt .alert {
            border-radius: 16px;
            border: 0;
            font-weight: 700;
            color: var(--camp-navy);
        }

        .gallery-carousel {
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 18px 35px rgba(16,24,32,0.14);
            border: 1px solid rgba(16,24,32,0.08);
            background: #fff;
        }

        .gallery-carousel .carousel-item img {
            height: 470px;
            object-fit: cover;
        }

        .gallery-carousel .carousel-caption {
            background: linear-gradient(180deg, rgba(16,24,32,0.1) 0%, rgba(16,24,32,0.8) 100%);
            border-radius: 16px 16px 0 0;
            left: 12%;
            right: 12%;
            bottom: 12px;
            padding: 1rem 1.25rem;
        }

        .gallery-carousel .carousel-control-prev,
        .gallery-carousel .carousel-control-next {
            width: 8%;
            opacity: 0.9;
        }

        .gallery-carousel .carousel-control-prev-icon,
        .gallery-carousel .carousel-control-next-icon {
            background-color: rgba(16,24,32,0.7);
            border-radius: 50%;
            padding: 1.5rem;
            background-size: 50% 50%;
        }

        .site-footer {
            background: linear-gradient(135deg, var(--camp-navy) 0%, var(--camp-navy-2) 50%, var(--camp-red) 100%);
            color: #fff;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .site-footer a {
            color: #fff;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark camp-navbar" style="margin-bottom: 0;">
    <div class="container">
        <a class="navbar-brand" href="#">#tineretSperanta - ACASĂ</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#camp">Tabără 2027</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">Despre noi</a></li>
                <li class="nav-item"><a class="nav-link" href="#mission">Misiune</a></li>
                <li class="nav-item"><a class="nav-link" href="#when">Când ne întâlnim</a></li>
                <li class="nav-item"><a class="nav-link" href="#activities">Ce facem</a></li>
                <li class="nav-item"><a class="nav-link" href="#bibletracker">Bible Tracker</a></li>
            </ul>
        </div>
    </div>
</nav>

<header class="hero" style="background-image:url('../images/home/hero.jpg');">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">Bun venit ACASĂ!</h1>
        <p class="lead mb-4">#tineretSperanta - comunitatea tinerilor din Biserica Baptistă Speranța Arad</p>
        <a href="#bibletracker" class="btn btn-light btn-lg">Către Bible Tracker</a>
    </div>
</header>

<main>
    <section id="camp" class="py-5 mx-3 my-4">
        <div class="container">
            <div class="row justify-content-center align-items-center gy-4 mb-5">
                <div class="col-lg-8">
                    <div class="theme-panel text-white border-0 shadow-lg">
                        <div class="card-body p-5 text-center">
                            <h2 class="mb-3"><i class="fas fa-campground me-2"></i>Tabăra 2027</h2>
                            <p class="lead text-white-75 mb-4">13-22 August • Germania</p>
                            <h5 class="card-title mb-3">NUMĂRĂTOAREA INVERSĂ</h5>
                            <p class="text-white-50 mb-4">Zile până la cea mai frumoasă experiență a verii</p>
                            <div id="countdown" class="display-4 fw-bold text-white" style="letter-spacing: 2px; line-height: 1.2;"></div>
                            <p class="mt-4 mb-0 text-white-75">Germania te așteaptă</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="py-5 theme-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="mb-4 section-title"><i class="fas fa-users me-2"></i>Despre noi</h2>
                    <p class="lead">ACASĂ este grupul de tineri al Bisericii Speranța. Ne întâlnim pentru a studia Cuvântul, a ne ruga împreună și a construi relații bazate pe credință și prietenie. Fie că ești nou sau ai fost cu noi de mult timp, ești binevenit.</p>
                </div>
                <div class="col-lg-6">
                    <div id="aboutCarousel" class="carousel slide gallery-carousel" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <?php foreach ($aboutSlides as $index => $slide): ?>
                                <button type="button" data-bs-target="#aboutCarousel" data-bs-slide-to="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>" aria-current="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-label="Slide <?php echo $index + 1; ?>"></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="carousel-inner">
                            <?php foreach ($aboutSlides as $index => $slide): ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <img src="<?php echo $slide['src']; ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($slide['alt']); ?>">
                                    <div class="carousel-caption d-none d-md-block">
                                        <h5><?php echo htmlspecialchars($slide['title']); ?></h5>
                                        <p><?php echo htmlspecialchars($slide['description']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#aboutCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#aboutCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="mission" class="py-5">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="mb-4 section-title"><i class="fas fa-cross me-2"></i>Misiunea noastră</h2>
                <p class="lead">Ne propunem să creștem spiritual, să fim lumină în comunitate și să încurajăm tinerii să-și trăiască credința dincolo de zidurile bisericii.</p>
            </div>

            <div id="missionCarousel" class="carousel slide gallery-carousel" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <?php foreach ($missionSlides as $index => $slide): ?>
                        <button type="button" data-bs-target="#missionCarousel" data-bs-slide-to="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>" aria-current="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-label="Slide <?php echo $index + 1; ?>"></button>
                    <?php endforeach; ?>
                </div>
                <div class="carousel-inner">
                    <?php foreach ($missionSlides as $index => $slide): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <img src="<?php echo $slide['src']; ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($slide['alt']); ?>">
                            <div class="carousel-caption d-none d-md-block">
                                <h5><?php echo htmlspecialchars($slide['title']); ?></h5>
                                <p><?php echo htmlspecialchars($slide['description']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#missionCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#missionCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>

    <section id="gallery" class="py-5 theme-section">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="mb-3 section-title"><i class="fas fa-images me-2"></i>Momente</h2>
                <p class="lead">Câteva imagini care ne amintesc de bucuria, rugăciunea și prietenia din timpul nostru împreună.</p>
            </div>

            <div id="galleryCarousel" class="carousel slide gallery-carousel" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <?php foreach ($momentSlides as $index => $slide): ?>
                        <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>" aria-current="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-label="Slide <?php echo $index + 1; ?>"></button>
                    <?php endforeach; ?>
                </div>
                <div class="carousel-inner">
                    <?php foreach ($momentSlides as $index => $slide): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <img src="<?php echo $slide['src']; ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($slide['alt']); ?>">
                            <div class="carousel-caption d-none d-md-block">
                                <h5><?php echo htmlspecialchars($slide['title']); ?></h5>
                                <p><?php echo htmlspecialchars($slide['description']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>

    <section id="when" class="py-5 theme-section">
        <div class="container text-center">
            <h2 class="mb-4 section-title"><i class="fas fa-clock me-2"></i>Când ne întâlnim</h2>
            <p class="lead">Ne vedem în fiecare vineri seara, de la 19:00 în sala de tineret a bisericii (Strada Iustin Marsieu nr. 20). Uneori organizăm activități speciale în weekend.</p>
        </div>
    </section>

    <section id="activities" class="py-5 theme-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="mb-4 section-title"><i class="fas fa-pray me-2"></i>Ce facem</h2>
                    <ul class="list-unstyled feature-list">
                        <li><i class="fas fa-book-open me-2"></i> Studii biblice, discuții, închinare prin muzică</li>
                        <li><i class="fas fa-question-circle me-2"></i> Răspundem la întrebările importante din viață</li>
                        <li><i class="fas fa-gamepad me-2"></i> Ne distrăm, mâncăm împreună și ne implicăm în comunitate</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="image-frame">
                        <img src="../images/travel/poland-landscape.jpg" class="img-fluid" alt="Activități ACASA">
                    </div>
                    <p class="text-center mt-3 mb-0 fw-bold" style="color: #183153; letter-spacing: 0.04em;">Polonia 2025</p>
                </div>
            </div>
        </div>
    </section>

    <section id="bibletracker" class="py-5 theme-section-alt text-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="mb-4 section-title text-white"><i class="fas fa-bible me-2"></i>Bible Tracker</h2>
                <p class="lead text-white-75">Urmărește-ți citirile biblice zilnice, reflectează și crește împreună cu noi. Această unealtă rămâne o parte centrală a programului nostru.</p>
            </div>
            <div class="row justify-content-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card camp-card border-0 shadow">
                            <div class="card-body text-center text-dark">
                                <i class="fas fa-tachometer-alt fa-2x text-success mb-3"></i>
                                <h5 class="card-title">Bun venit, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h5>
                                <p class="card-text" style="color: #111111;">Accesează panoul tau pentru a gestiona citirile biblice și a-ți urmări progresul.</p>
                                <a href="/dashboard/dashboard.php" class="btn btn-success btn-lg">Mergi la Panou</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card camp-card border-0 shadow">
                            <div class="card-body text-center text-dark">
                                <i class="fas fa-sign-in-alt fa-2x text-primary mb-3"></i>
                                <h5 class="card-title">Autentificare</h5>
                                <p class="card-text" style="color: #111111;">Ești deja membru? Autentifică-te pentru a trimite citirile biblice și a-ți urmări progresul.</p>
                                <a href="/auth/login.php" class="btn btn-primary">Autentifică-te</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card camp-card border-0 shadow">
                            <div class="card-body text-center text-dark">
                                <i class="fas fa-user-plus fa-2x text-success mb-3"></i>
                                <h5 class="card-title">Înregistrează-te</h5>
                                <p class="card-text" style="color: #111111;">Nou aici? Înscrie-te pentru a începe să trimiți reflecțiile biblice și a te alătura grupului.</p>
                                <a href="/auth/register.php" class="btn btn-secondary">Înregistrează-te</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card camp-card border-0 shadow">
                            <div class="card-body text-center text-dark">
                                <i class="fas fa-trophy fa-2x text-warning mb-3"></i>
                                <h5 class="card-title">Clasament</h5>
                                <p class="card-text" style="color: #111111;">Cum se află alți utilizatori? Vezi pe cei mai activi pe baza activitatii lor.</p>
                                <a href="/leaderboard/leaderboard.php" class="btn btn-info">Vezi Clasamentul</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="alert alert-light mt-4" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>Nu uita să trimiți citirile biblice zilnice! Ai o fereastră de 3 zile pentru a recupera.
            </div>
        </div>
    </section>
</main>

<footer class="site-footer py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-center">
                <p class="mb-0">&copy; 2027 ACASĂ #tineretSperanta / Bible Tracker. Toate drepturile rezervate.</p>
            </div>
            <div class="col-md-6 text-md-right text-center">
                <p class="mb-0">Contact: <a href="mailto:contact@exemplu.ro" class="text-light">Ruben: 0731615153</a></p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Countdown to 13 August 2027
    const countDownDate = new Date("August 13, 2027 00:00:00").getTime();

    const x = setInterval(function() {
        const now = new Date().getTime();
        const distance = countDownDate - now;

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("countdown").innerHTML = days + "z " + hours + "h " + minutes + "m " + seconds + "s ";

        if (distance < 0) {
            clearInterval(x);
            document.getElementById("countdown").innerHTML = "Tabăra a început!";
        }
    }, 1000);
</script>
</body>
</html>
