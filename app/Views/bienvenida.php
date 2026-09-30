<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Cache-Control" content="no-store">
  <title>Bienvenida · Vuela Internet</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= e(asset('css/vuela.css')) ?>" rel="stylesheet">
</head>
<body>
<div class="min-vh-100 d-flex align-items-center">
  <div class="container text-center">
    <img src="<?= e(asset('img/logo-vuela.png')) ?>" alt="Vuela" height="78" class="mb-3">
    <div class="packet mb-4">
      <div class="loader"></div>
      <div class="loader"></div>
      <div class="loader"></div>
    </div>
    <h1 class="fw-bold bounce-in">Hola de nuevo a Vuela Internet</h1>
    <p class="fs-3" style="color:#F15A22"><?= e($user['nombre'] ?? 'Agente') ?></p>
    <p class="text-muted"><?= e($user['rol'] ?? '') ?> · <?= e(APP_TAGLINE) ?></p>
    <a class="btn btn-vuela mt-2 px-4" href="<?= e(url('/panel')) ?>">Continuar</a>
  </div>
</div>
<script src="<?= e(asset('js/app.js')) ?>"></script>
<script>
  history.replaceState(null, '', <?= json_encode(url('/bienvenida')) ?>);
  setTimeout(function () { location.replace(<?= json_encode(url('/panel')) ?>); }, 3200);
</script>
</body>
</html>
