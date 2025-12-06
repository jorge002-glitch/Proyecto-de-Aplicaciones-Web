<?php
session_start();
if (isset($_SESSION['usuario'])) header('Location: dashboard.php');
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Iniciar sesión</title><link rel="stylesheet" href="css/estilos.css"></head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <h2>SDLG</h2>
    <p class="small">Inicia sesión con tu cuenta</p>
    <form action="login.php" method="post">
      <input name="email" type="email" placeholder="Correo electrónico" class="input" required>
      <input name="password" type="password" placeholder="Contraseña" class="input" required>
      <button class="button" style="width:100%;margin-top:8px">Iniciar sesión</button>
    </form>
    <p style="text-align:center;margin-top:12px">¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
  </div>
</div>
</body>
</html>