<?php
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/auth.php';
session_start();

checkPersistentLogin();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>#tineretSperanta - ACASĂ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="margin-bottom: 0;">
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

<!-- hero section -->
<header class="hero" style="margin-top: 0; background-image:url('../images/hero.jpeg');">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">Bun venit ACASĂ!</h1>
        <p class="lead mb-4">#tineretSperanta - comunitatea tinerilor din Biserica Baptistă Speranța Arad</p>
        <a href="#bibletracker" class="btn btn-light btn-lg" style="text-shadow:none !important; box-shadow:none !important; filter:none !important;">Catre Bible Tracker</a>
    </div>
</header>

<main>
    <section id="camp" class="py-5 border border-primary rounded mx-3 my-4">
        <div class="container">
            <div class="row justify-content-center align-items-center gy-4 mb-5">
                <div class="col-lg-8">
                    <div class="card bg-primary text-white border-0 shadow-lg">
                        <div class="card-body p-5 text-center" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
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

    <section id="about" class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="mb-4"><i class="fas fa-users text-primary me-2"></i>Despre noi</h2>
                    <p class="lead">ACASĂ este grupul de tineri al Bisericii Speranța. Ne întâlnim pentru a studia Cuvântul, a ne ruga împreună și a construi relații bazate pe credință și prietenie. Fie că ești nou sau ai fost cu noi de mult timp, ești binevenit.</p>
                </div>
                <div class="col-lg-6">
                    <img src="../images/stage.jpeg" class="img-fluid rounded shadow" alt="Tineri ACASA">
                </div>
            </div>
        </div>
    </section>

    <section id="mission" class="py-5 bg-light">
        <div class="container text-center">
            <h2 class="mb-4"><i class="fas fa-cross text-primary me-2"></i>Misiunea noastră</h2>
            <p class="lead">Ne propunem să creștem spiritual, să fim lumină în comunitate și să încurajăm tinerii să-și trăiască credința dincolo de zidurile bisericii.</p>
        </div>
    </section>

    <section id="when" class="py-5">
        <div class="container text-center">
            <h2 class="mb-4"><i class="fas fa-clock text-primary me-2"></i>Când ne întâlnim</h2>
            <p class="lead">Ne vedem în fiecare vineri seara, de la 19:00 în sala de tineret a bisericii (Strada Iustin Marsieu nr. 20). Uneori organizăm activități speciale în weekend.</p>
        </div>
    </section>

    <section id="activities" class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="mb-4"><i class="fas fa-pray text-primary me-2"></i>Ce facem</h2>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-book-open text-secondary me-2"></i> Studii biblice, discuții, închinare prin muzică</li>
                        <li class="mb-2"><i class="fas fa-question-circle text-secondary me-2"></i> Serie de întrebări și răspunsuri</li>
                        <li class="mb-2"><i class="fas fa-gamepad text-secondary me-2"></i> Jocuri, mese comune și activități de voluntariat</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <img src="../images/poland.jpeg" class="img-fluid rounded shadow" alt="Activități ACASA">
                </div>
            </div>
        </div>
    </section>

    <section id="bibletracker" class="py-5 bg-primary text-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="mb-4"><i class="fas fa-bible text-white me-2"></i>Bible Tracker</h2>
                <p class="lead">Urmărește-ți citirile biblice zilnice, reflectează și crește împreună cu noi. Această unealtă rămâne o parte centrală a programului nostru.</p>
            </div>
            <div class="row justify-content-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Logged-in user view -->
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card border-0 shadow">
                            <div class="card-body text-center text-dark">
                                <i class="fas fa-tachometer-alt fa-2x text-success mb-3"></i>
                                <h5 class="card-title">Bun venit, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h5>
                                <p class="card-text">Accesează panoul tau pentru a gestiona citirile biblice și a-ți urmări progresul.</p>
                                <a href="../dashboard/dashboard.php" class="btn btn-success btn-lg">Mergi la Panou</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Non-logged-in user view -->
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card border-0 shadow">
                            <div class="card-body text-center text-dark">
                                <i class="fas fa-sign-in-alt fa-2x text-primary mb-3"></i>
                                <h5 class="card-title">Autentificare</h5>
                                <p class="card-text">Ești deja membru? Autentifică-te pentru a trimite citirile biblice și a-ți urmări progresul.</p>
                                <a href="../auth/login.php" class="btn btn-primary">Autentifică-te</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card border-0 shadow">
                            <div class="card-body text-center text-dark">
                                <i class="fas fa-user-plus fa-2x text-success mb-3"></i>
                                <h5 class="card-title">Înregistrează-te</h5>
                                <p class="card-text">Nou aici? Înscrie-te pentru a începe să trimiți reflecțiile biblice și a te alătura grupului.</p>
                                <a href="../auth/register.php" class="btn btn-secondary">Înregistrează-te</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card border-0 shadow">
                            <div class="card-body text-center text-dark">
                                <i class="fas fa-trophy fa-2x text-warning mb-3"></i>
                                <h5 class="card-title">Clasament</h5>
                                <p class="card-text">Cum se află alți utilizatori? Vezi pe cei mai activi pe baza activitatii lor.</p>
                                <a href="../leaderboard/leaderboard.php" class="btn btn-info">Vezi Clasamentul</a>
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

<footer class="bg-dark text-white py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5>ACASĂ #tineretSperanta</h5>
                <p>#tineretSperanta - Biserica Baptistă Speranța Arad</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p>Contact: <a href="mailto:contact@exemplu.ro" class="text-light">Ruben: 0731615153</a></p>
                <p>&copy; 2027 ACASĂ #tineretSperanta / Bible Tracker. Toate drepturile rezervate.</p>
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
