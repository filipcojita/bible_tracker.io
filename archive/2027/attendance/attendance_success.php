<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prezență înregistrată</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #101820 0%, #183153 45%, #c8102e 100%);
            color: #fff;
            font-family: Arial, sans-serif;
        }
        .success-card {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 24px;
            box-shadow: 0 18px 40px rgba(0,0,0,0.2);
            padding: 32px 28px;
            text-align: center;
            max-width: 540px;
            width: min(90vw, 540px);
        }
        .camp-badge {
            display: inline-block;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 999px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            font-size: 0.72rem;
            padding: 8px 14px;
            margin-bottom: 18px;
        }
        img {
            max-width: 260px;
            width: 100%;
            height: auto;
            border-radius: 18px;
            margin-bottom: 18px;
        }
        h1 {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            margin: 0;
        }
        p {
            margin: 14px 0 0;
            color: #edf3ff;
            font-size: 1.05rem;
        }
    </style>
</head>

<body>

<div class="success-card">
    <div class="camp-badge">ACASĂ • Tabăra 2027</div>
    <img src="/images/success.jpg" alt="Confirmare" class="img-fluid">
    <h1>Prezență înregistrată!</h1>
    <p>Ai primit un punct de prezență.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>