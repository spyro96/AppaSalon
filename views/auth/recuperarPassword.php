<h1 class="nombre-pagina">Recuperar Contraseña</h1>
<p class="descripcion-pagina">Coloca tu Nueva Contraseña Acontinuación</p>

<?php
    include_once __DIR__ . "/../templates/alertas.php";
?>

<?php if($error) return; //detiene la ejecucion el return?>
<form class="formulario" method="POST">
    <div class="campo">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="Nueva Contraseña">
    </div>

    <input type="submit" class="boton" value="Guardar Nueva Contraseña">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
    <a href="/crear_cuenta">¿Aún no tienes una cuenta? Crear una</a>
</div>

