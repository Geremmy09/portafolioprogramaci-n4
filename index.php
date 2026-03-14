<?php
// --- CONFIGURACIÓN DE BASE DE DATOS ---
$host = "localhost";
$user = "root"; // Cambia esto si tu usuario de MySQL es diferente
$pass = "";     // Cambia esto si tienes contraseña en MySQL
$db   = "portafolio_geremmy";

// Conexión
$conexion = new mysqli($host, $user, $pass, $db);
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// --- VARIABLES INICIALES DEL FORMULARIO ---
$id_editar = 0;
$modo_edicion = false;
$nombre = "";
$usuario = "";
$email = "";
$nota_texto = "";

// --- LÓGICA CRUD ---

// 1. ELIMINAR NOTA
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM comentarios WHERE id=$id");
    header("Location: index.php#comentarios");
}

// 2. OBTENER DATOS PARA EDITAR
if (isset($_GET['editar'])) {
    $id_editar = $_GET['editar'];
    $modo_edicion = true;
    $resultado = $conexion->query("SELECT * FROM comentarios WHERE id=$id_editar");
    
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $nombre = $fila['nombreyapellido'];
        $usuario = $fila['usuario'];
        $email = $fila['email'];
        $nota_texto = $fila['nota'];
    }
}

// 3. GUARDAR O ACTUALIZAR NOTA
if (isset($_POST['guardar_nota'])) {
    $id = $_POST['id'];
    // Seguridad básica (Evitar inyección SQL básica)
    $post_nombre = $conexion->real_escape_string($_POST['nombreyapellido']);
    $post_usuario = $conexion->real_escape_string($_POST['usuario']);
    $post_email = $conexion->real_escape_string($_POST['email']);
    $post_nota = $conexion->real_escape_string($_POST['nota']);
    
    // Función Date() como solicitaste
    date_default_timezone_set('America/Caracas'); 
    $fecha = date('Y-m-d H:i:s');

    if ($id == 0) {
        // CREAR
        $sql = "INSERT INTO comentarios (nombreyapellido, usuario, email, nota, fechanota) 
                VALUES ('$post_nombre', '$post_usuario', '$post_email', '$post_nota', '$fecha')";
    } else {
        // ACTUALIZAR
        $sql = "UPDATE comentarios SET 
                nombreyapellido='$post_nombre', 
                usuario='$post_usuario', 
                email='$post_email', 
                nota='$post_nota', 
                fechanota='$fecha' 
                WHERE id=$id";
    }
    
    $conexion->query($sql);
    header("Location: index.php#comentarios");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geremmy Ferrer | System Engineer Student</title>
    <link rel="icon" type="image/png" href="https://cdn-icons-png.flaticon.com/512/2001/2001571.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div class="logo">
            <h1>GEREMMY<span>FERRER</span></h1>
        </div>
        <nav>
            <ul>
                <li><a href="#perfil">Perfil</a></li>
                <li><a href="#proyectos">Intereses</a></li>
                <li><a href="#stack">Habilidades</a></li>
                <li><a href="#comentarios">Notas</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="perfil" class="fade-in">
            <h2>> Perfil del Operativo</h2>
            <p>Estudiante de <strong>Ingeniería en Sistemas</strong> me gusta el desarrollo de páginas web y de videojuegos, en un futuro espero poder trabajar y desarrollar mi propio videjuego</p>
        </section>

        <section id="proyectos">
            <h2>> Áreas de Interés</h2>
            <div class="grid">
                <div class="card">
                    <h3>Acción & Estrategia</h3>
                    <p>Admiro la precisión de <strong>John Wick</strong>; las artes marciales y el manejo de armas que tiene en las peliculas es muy bueno.</p>
                </div>
                <div class="card">
                    <h3>Survival Horror</h3>
                    <p>Fan de la saga <strong>Resident Evil</strong>. Me encanta los videojuegos de resident evil desde que era pequeño, el survival horror, los puzzle y los zombies es algo que me llama mucho la atención.</p>
                </div>
            </div>
        </section>

        <section id="stack">
            <h2>> Habilidades de Ingeniería</h2>
            <div class="skills">
                <span>Desarrollo de Software</span>
                <span>Hardware</span>
                <span>Sistemas Operativos</span>
                <span>Base de Datos</span>
            </div>
        </section>

        <section id="comentarios">
            <h2>> Bitácora de Operaciones (Notas)</h2>
            
            <form action="index.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $id_editar; ?>">
                
                <div class="form-group">
                    <label>Nombre y Apellido *</label>
                    <input type="text" name="nombreyapellido" required value="<?php echo $nombre; ?>">
                </div>
                
                <div class="form-group">
                    <label>Usuario (Opcional)</label>
                    <input type="text" name="usuario" value="<?php echo $usuario; ?>">
                </div>

                <div class="form-group">
                    <label>Correo Electrónico *</label>
                    <input type="email" name="email" required value="<?php echo $email; ?>">
                </div>

                <div class="form-group">
                    <label>Nota *</label>
                    <textarea name="nota" rows="4" required maxlength="1000"><?php echo $nota_texto; ?></textarea>
                </div>

                <?php if($modo_edicion): ?>
                    <button type="submit" name="guardar_nota" class="btn">Actualizar Nota</button>
                    <a href="index.php#comentarios" style="margin-left: 10px; color: #888;">Cancelar edición</a>
                <?php else: ?>
                    <button type="submit" name="guardar_nota" class="btn">Enviar Nota</button>
                <?php endif; ?>
            </form>

            <div class="notas-container">
                <?php
                // Consultar todas las notas ordenadas de la más reciente a la más antigua
                $notas = $conexion->query("SELECT * FROM comentarios ORDER BY id DESC");
                
                if ($notas->num_rows > 0) {
                    while ($fila = $notas->fetch_assoc()) {
                        ?>
                        <div class="nota-card">
                            <div class="nota-header">
                                <div>
                                    <h4><?php echo htmlspecialchars($fila['nombreyapellido']); ?> 
                                        <span style="color:#888; font-size: 0.8rem;">
                                            <?php echo !empty($fila['usuario']) ? "(@" . htmlspecialchars($fila['usuario']) . ")" : ""; ?>
                                        </span>
                                    </h4>
                                    <span class="nota-fecha">Registrado el: <?php echo $fila['fechanota']; ?></span>
                                </div>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($fila['nota'])); ?></p>
                            
                            <div class="nota-acciones">
                                <a href="index.php?editar=<?php echo $fila['id']; ?>#comentarios" class="btn-editar">Editar</a>
                                <a href="index.php?eliminar=<?php echo $fila['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Seguro que deseas eliminar esta nota?');">Eliminar</a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p style='color: #888;'>No hay registros en la bitácora aún.</p>";
                }
                ?>
            </div>
        </section>
    </main>

    <footer>
        <p>Estado: Online | Geremmy Ferrer &copy; 2026</p>
    </footer>

</body>
</html>