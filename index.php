<?php
include 'db.php';
session_start();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACASA Tineret - #tineretSperanta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">ACASA #tineretSperanta</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
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
<header class="hero" style="background-image:url('images/hero.jpeg');">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">Bun venit ACASĂ!</h1>
        <p class="lead mb-4">#tineretSperanta - comunitatea tinerilor din Biserica Baptistă Speranța Arad</p>
        <a href="#bibletracker" class="btn btn-light btn-lg shadow">Catre Bible Tracker</a>
    </div>
</header>

<main>
    <section id="about" class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="mb-4"><i class="fas fa-users text-primary me-2"></i>Despre noi</h2>
                    <p class="lead">ACASA este grupul de tineri al Bisericii Speranța. Ne întâlnim pentru a studia Cuvântul, a ne ruga împreună și a construi relații bazate pe credință și prietenie. Fie că ești nou sau ai fost cu noi de mult timp, ești binevenit.</p>
                </div>
                <div class="col-lg-6">
                    <img src="images/stage.jpeg" class="img-fluid rounded shadow" alt="Tineri ACASA">
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
                    <img src="images/poland.jpeg" class="img-fluid rounded shadow" alt="Activități ACASA">
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
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card border-0 shadow">
                        <div class="card-body text-center text-dark">
                            <i class="fas fa-sign-in-alt fa-2x text-primary mb-3"></i>
                            <h5 class="card-title">Autentificare</h5>
                            <p class="card-text">Ești deja membru? Autentifică-te pentru a trimite citirile biblice și a-ți urmări progresul.</p>
                            <a href="login.php" class="btn btn-primary">Autentifică-te</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card border-0 shadow">
                        <div class="card-body text-center text-dark">
                            <i class="fas fa-user-plus fa-2x text-success mb-3"></i>
                            <h5 class="card-title">Înregistrează-te</h5>
                            <p class="card-text">Nou aici? Înscrie-te pentru a începe să trimiți reflecțiile biblice și a te alătura grupului.</p>
                            <a href="register.php" class="btn btn-secondary">Înregistrează-te</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card border-0 shadow">
                        <div class="card-body text-center text-dark">
                            <i class="fas fa-trophy fa-2x text-warning mb-3"></i>
                            <h5 class="card-title">Clasament</h5>
                            <p class="card-text">Cum te plasezi în grupul tău? Vezi utilizatorii de top pe baza activitatii lor.</p>
                            <a href="leaderboard.php" class="btn btn-info">Vezi Clasamentul</a>
                        </div>
                    </div>
                </div>
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
                <p>&copy; 2026 ACASĂ #tineretSperanta / Bible Tracker. Toate drepturile rezervate.</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
