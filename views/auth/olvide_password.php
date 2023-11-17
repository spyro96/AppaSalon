<h1 class="nombre-pagina">Olvide Passoword</h1>
<p class="descripcion-pagina">Reestablece tu contraseña escribiendo tu correo acontinuación</p>

<?php
include_once __DIR__ . "/../templates/alertas.php";
?>

<form action="/olvide" class="formulario" method="POST">
    <div class="campo">
        <label for="email">Correo</label>
        <input type="email" id="email" name="email" placeholder="Tu Correo">
    </div>

    <input type="submit" class="boton" value="Enviar instrucciones">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
    <a href="/crear_cuenta">¿Aún no tienes una cuenta? Crear una</a>
</div>