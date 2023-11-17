<h1 class="nombre-pagina">Login</h1>
<p class="descripcion-pagina">Inicia sesion con tus datos</p>

<?php
include_once __DIR__ . "/../templates/alertas.php";
?>
<form action="/" class="formulario" method="POST">
    <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Tu email">
    </div>

    <div class="campo">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Tu contraseña">
    </div>

    <input type="submit" class="boton" value="Iniciar Sesión">
</form>

<div class="acciones">
    <a href="/crear_cuenta">¿Aún no tienes una cuenta? Crear una</a>
    <a href="/olvide">¿Olvidaste tu contraseña?</a>
</div>