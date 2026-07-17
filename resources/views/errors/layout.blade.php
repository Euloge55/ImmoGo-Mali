<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImmoGo Mali — @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family:'Poppins',sans-serif; }
        body { background:linear-gradient(135deg,#2C3E50,#3498db); min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .error-card { background:white; border-radius:20px; padding:50px 40px; text-align:center; max-width:480px; width:100%; box-shadow:0 25px 60px rgba(0,0,0,0.3); }
        .error-code { font-size:6rem; font-weight:800; color:#4ECDC4; line-height:1; }
    </style>
</head>
<body>
<div class="error-card">
    <div class="error-code">@yield('code')</div>
    <h3 class="fw-bold mt-3 mb-2">@yield('title')</h3>
    <p class="text-muted mb-4">@yield('message')</p>
    <div class="d-flex gap-2 justify-content-center">
        <a href="/" class="btn fw-semibold px-4" style="background:#4ECDC4;color:white;border-radius:10px">
            <i class="fas fa-home me-2"></i>Accueil
        </a>
        <button onclick="history.back()" class="btn btn-outline-secondary px-4" style="border-radius:10px">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </button>
    </div>
</div>
</body>
</html>
