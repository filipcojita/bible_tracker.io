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
        <h1 class="display-4">Bun venit ACASĂ!</h1>
        <p class="lead">#tineretSperanta - comunitatea tinerilor din Biserica Baptistă Speranța Arad</p>
    </div>
</header>

<main>
    <section id="about" class="py-5">
        <div class="container">
            <h2>Despre noi</h2>
            <p>ACASA este grupul de tineri al Bisericii Speranța. Ne întâlnim pentru a studia Cuvântul, a ne ruga împreună și a construi relații bazate pe credință și prietenie. Fie că ești nou sau ai fost cu noi de mult timp, ești binevenit.</p>
            <img src="images/stage.jpeg" class="img-fluid rounded mt-3" alt="Tineri ACASA">
        </div>
    </section>

    <section id="mission" class="py-5 bg-light">
        <div class="container">
            <h2>Misiunea noastră</h2>
            <p>Ne propunem să creștem spiritual, să fim lumină în comunitate și să încurajăm tinerii să-și trăiască credința dincolo de zidurile bisericii.</p>
        </div>
    </section>

    <section id="when" class="py-5">
        <div class="container">
            <h2>Când ne întâlnim</h2>
            <p>Ne vedem în fiecare vineri seara, de la 19:00 în sala de tineret a bisericii (Strada Iustin Marsieu nr. 20). Uneori organizăm activități speciale în weekend.</p>
        </div>
    </section>

    <section id="activities" class="py-5 bg-light">
        <div class="container">
            <h2>Ce facem</h2>
            <ul class="list-unstyled">
                <li>&#8226; Studii biblice, discuții, închinare prin muzică</li>
                <li>&#8226; Serie de întrebări și răspunsuri</li>
                <li>&#8226; Jocuri, mese comune și activități de voluntariat</li>
            </ul>
            <img src="images/poland.jpeg" class="img-fluid rounded mt-3" alt="Activități ACASA">
        </div>
    </section>

    <section id="bibletracker" class="py-5">
        <div class="container">
            <h2>Bible Tracker</h2>
            <p>Urmărește-ți citirile biblice zilnice, reflectează și crește împreună cu noi. Această unealtă rămâne o parte centrală a programului nostru.</p>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Autentificare</h5>
                            <p class="card-text">Ești deja membru? Autentifică-te pentru a trimite citirile biblice și a-ți urmări progresul.</p>
                            <a href="login.php" class="btn btn-primary">Autentifică-te</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Înregistrează-te</h5>
                            <p class="card-text">Nou aici? Înscrie-te pentru a începe să trimiți reflecțiile biblice și a te alătura grupului.</p>
                            <a href="register.php" class="btn btn-secondary">Înregistrează-te</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Clasament</h5>
                            <p class="card-text">Cum te plasezi în grupul tău? Vezi utilizatorii de top pe baza activitatii lor.</p>
                            <a href="leaderboard.php" class="btn btn-info">Vezi Clasamentul</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alert alert-warning mt-4" role="alert">
                Nu uita să trimiți citirile biblice zilnice! Ai o fereastră de 3 zile pentru a recupera.
            </div>
        </div>
    </section>
</main>

<footer class="bg-dark text-white py-3">
    <div class="container text-center">
        <p>&copy; 2025 ACASA Tineret / Bible Tracker. Toate drepturile rezervate.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
