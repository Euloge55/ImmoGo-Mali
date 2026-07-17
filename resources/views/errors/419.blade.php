<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Session expirée — ImmoGo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta http-equiv="refresh" content="3;url=javascript:history.back()">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh">
    <div class="text-center p-5">
        <i class="fas fa-clock fa-4x mb-4" style="color:#f39c12"></i>
        <h3 class="fw-bold">Session expirée</h3>
        <p class="text-muted mb-4">
            Votre session a expiré. La page va se recharger automatiquement...
        </p>
        <div class="spinner-border text-warning mb-3" role="status"></div>
        <br>
        <a href="javascript:history.back()" class="btn fw-semibold"
           style="background:#4ECDC4; color:white; border-radius:10px">
            <i class="fas fa-redo me-2"></i>Recharger le formulaire
        </a>
    </div>
    <script>setTimeout(() => history.back(), 2000);</script>
</body>
</html>
