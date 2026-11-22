<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/clases/InventarioComputo.php';

// Aquí se recuperan como objetos los equipos guardados en la sesión.
$inventario = new InventarioComputo($_SESSION['equipos'] ?? []);
$mensaje = '';
$tipoMensaje = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = (string) ($_POST['accion'] ?? '');

    if ($accion === 'agregar') {
        $serie = trim((string) ($_POST['numeroSerie'] ?? ''));
        $tipo = (string) ($_POST['tipo'] ?? '');
        $marca = trim((string) ($_POST['marca'] ?? ''));
        $modelo = trim((string) ($_POST['modelo'] ?? ''));
        $estado = (string) ($_POST['estado'] ?? 'Disponible');

        $tiposValidos = ['Escritorio', 'Laptop', 'Telefono', 'Television'];
        $estadosValidos = ['Disponible', 'En uso', 'En reparación'];

        if ($serie === '' || $marca === '' || $modelo === '') {
            $mensaje = 'Completa el número de serie, marca y modelo.';
            $tipoMensaje = 'error';
        } elseif (!in_array($tipo, $tiposValidos, true) || !in_array($estado, $estadosValidos, true)) {
            $mensaje = 'Selecciona un tipo y un estado válidos.';
            $tipoMensaje = 'error';
        } else {
            // El tipo seleccionado determina qué clase derivada se instancia.
            $equipo = null;

            if ($tipo === 'Escritorio') {
                $procesador = trim((string) ($_POST['procesador'] ?? ''));
                $ram = filter_var($_POST['memoriaRam'] ?? null, FILTER_VALIDATE_INT);
                $almacenamiento = trim((string) ($_POST['almacenamiento'] ?? ''));
                $gabinete = trim((string) ($_POST['tipoGabinete'] ?? ''));
                $potencia = filter_var($_POST['potenciaFuente'] ?? null, FILTER_VALIDATE_INT);

                if ($procesador === '' || $almacenamiento === '' || $ram === false || $ram === null || $ram < 1 || $gabinete === '' || $potencia === false || $potencia === null || $potencia < 1) {
                    $mensaje = 'Completa las características de la computadora de escritorio.';
                    $tipoMensaje = 'error';
                } else {
                    $equipo = new PCEscritorio($serie, $marca, $modelo, $procesador, (int) $ram, $almacenamiento, $gabinete, $potencia, $estado);
                }
            } elseif ($tipo === 'Laptop') {
                $procesador = trim((string) ($_POST['procesador'] ?? ''));
                $ram = filter_var($_POST['memoriaRam'] ?? null, FILTER_VALIDATE_INT);
                $almacenamiento = trim((string) ($_POST['almacenamiento'] ?? ''));
                $pantalla = filter_var($_POST['tamanoPantalla'] ?? null, FILTER_VALIDATE_FLOAT);
                $bateria = filter_var($_POST['duracionBateria'] ?? null, FILTER_VALIDATE_INT);

                if ($procesador === '' || $almacenamiento === '' || $ram === false || $ram === null || $ram < 1 || $pantalla === false || $pantalla === null || $pantalla <= 0 || $bateria === false || $bateria === null || $bateria < 1) {
                    $mensaje = 'Completa las características de la laptop.';
                    $tipoMensaje = 'error';
                } else {
                    $equipo = new Laptop($serie, $marca, $modelo, $procesador, (int) $ram, $almacenamiento, (float) $pantalla, $bateria, $estado);
                }
            } elseif ($tipo === 'Telefono') {
                // Los teléfonos solamente requieren marca, modelo y número de serie.
                $equipo = new Telefono($serie, $marca, $modelo, $estado);
            } elseif ($tipo === 'Television') {
                $pulgadas = filter_var($_POST['pulgadas'] ?? null, FILTER_VALIDATE_FLOAT);

                if ($pulgadas === false || $pulgadas === null || $pulgadas <= 0) {
                    $mensaje = 'Escribe un tamaño de pantalla válido para la televisión.';
                    $tipoMensaje = 'error';
                } else {
                    $equipo = new Television($serie, $marca, $modelo, (float) $pulgadas, $estado);
                }
            }

            if ($equipo instanceof EquipoComputo) {
                if ($inventario->agregar($equipo)) {
                    $mensaje = 'Producto agregado al inventario.';
                    $tipoMensaje = 'success';
                } else {
                    $mensaje = 'Ya existe un producto con ese número de serie.';
                    $tipoMensaje = 'error';
                }
            }
        }
    } elseif ($accion === 'eliminar') {
        if ($inventario->eliminar((string) ($_POST['numeroSerie'] ?? ''))) {
            $mensaje = 'Producto eliminado del inventario.';
            $tipoMensaje = 'success';
        } else {
            $mensaje = 'No se encontró ese número de serie.';
            $tipoMensaje = 'error';
        }
    }

    $_SESSION['equipos'] = $inventario->obtenerTodos();
}

$filtro = (string) ($_GET['tipo'] ?? '');
$busqueda = trim((string) ($_GET['buscar'] ?? ''));
$equipos = array_map(
    static fn (EquipoComputo $equipo): array => $equipo->toArray(),
    $inventario->filtrarPorTipo($filtro)
);
if ($busqueda !== '') {
    $equipos = array_values(array_filter($equipos, static function (array $equipo) use ($busqueda): bool {
        $texto = implode(' ', [$equipo['numeroSerie'], $equipo['marca'], $equipo['modelo'], $equipo['tipo']]);
        return stripos($texto, $busqueda) !== false;
    }));
}

/** Escapa los datos antes de imprimirlos en la página. */
function escapar(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventario de equipos | TecnoStock Tamerlands</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="index.php"><span class="brand-mark">TS</span><span>TecnoStock Tamerlands<small>INVENTARIO DE PRODUCTOS</small></span></a>
    <span class="topbar-note">Actividad formativa 4 · Polimorfismo</span>
</header>
<main class="page">
    <section class="intro">
        <div><p class="eyebrow">CLASE BASE Y CLASES DERIVADAS CON POLIMORFISMO</p><h1>Inventario de productos</h1><p class="muted">Cada producto comparte datos generales y agrega sus propias características.</p></div>
        <div class="total"><span>Productos registrados</span><strong><?= $inventario->contar() ?></strong></div>
    </section>

    <?php if ($mensaje !== ''): ?>
        <div class="notice <?= escapar($tipoMensaje) ?>" role="status"><?= escapar($mensaje) ?></div>
    <?php endif; ?>

    <div class="layout">
        <section class="panel form-panel">
            <div class="panel-heading"><div><p class="eyebrow">NUEVO REGISTRO</p><h2>Agregar producto</h2></div><span class="plus">+</span></div>
            <form method="post" action="index.php" class="equipment-form">
                <input type="hidden" name="accion" value="agregar">
                <label>Número de serie<input name="numeroSerie" required maxlength="60" placeholder="Ej. PC-2025-001"></label>
                <div class="form-row">
                    <label>Tipo
                        <select name="tipo" id="tipoEquipo" required>
                            <option value="">Selecciona</option>
                            <option value="Laptop">Laptop</option>
                            <option value="Escritorio">Escritorio</option>
                            <option value="Telefono">Teléfono</option>
                            <option value="Television">Televisión</option>
                        </select>
                    </label>
                    <label>Estado<select name="estado"><option>Disponible</option><option>En uso</option><option>En reparación</option></select></label>
                </div>
                <div class="form-row">
                    <label>Marca<input name="marca" required maxlength="60" placeholder="Samsung"></label>
                    <label>Modelo<input name="modelo" required maxlength="80" placeholder="Modelo del producto"></label>
                </div>
                <div id="camposComputadora" class="tipo-fields" hidden>
                    <label>Procesador<input name="procesador" maxlength="100" placeholder="Intel Core i5"></label>
                    <div class="form-row">
                        <label>Memoria RAM (GB)<input type="number" name="memoriaRam" min="1" max="2048" placeholder="16"></label>
                        <label>Almacenamiento<input name="almacenamiento" maxlength="80" placeholder="512 GB SSD"></label>
                    </div>
                </div>
                <div id="camposEscritorio" class="tipo-fields" hidden>
                    <div class="form-row">
                        <label>Tipo de gabinete<input name="tipoGabinete" maxlength="40" placeholder="Torre"></label>
                        <label>Fuente (W)<input type="number" name="potenciaFuente" min="1" max="3000" placeholder="650"></label>
                    </div>
                </div>
                <div id="camposLaptop" class="tipo-fields" hidden>
                    <div class="form-row">
                        <label>Pantalla (pulgadas)<input type="number" name="tamanoPantalla" min="1" max="100" step="0.1" placeholder="15.6"></label>
                        <label>Batería (horas)<input type="number" name="duracionBateria" min="1" max="48" placeholder="8"></label>
                    </div>
                </div>
                <div id="camposTelevision" class="tipo-fields" hidden>
                    <label>Pantalla (pulgadas)<input type="number" name="pulgadas" min="1" max="200" step="0.1" placeholder="55"></label>
                </div>
                <button class="button primary" type="submit">Agregar al inventario <span>→</span></button>
            </form>
        </section>

        <section class="panel list-panel">
            <div class="panel-heading list-heading"><div><p class="eyebrow">REGISTRO ACTUAL</p><h2>Productos</h2></div><span class="count"><?= count($equipos) ?> resultados</span></div>
            <form class="filters" method="get" action="index.php">
                <input type="search" name="buscar" value="<?= escapar($busqueda) ?>" placeholder="Buscar por serie, marca o modelo" aria-label="Buscar producto">
                <select name="tipo" aria-label="Filtrar por tipo"><option value="">Todos los tipos</option><option value="Escritorio" <?= $filtro === 'Escritorio' ? 'selected' : '' ?>>Escritorio</option><option value="Laptop" <?= $filtro === 'Laptop' ? 'selected' : '' ?>>Laptop</option><option value="Telefono" <?= $filtro === 'Telefono' ? 'selected' : '' ?>>Teléfono</option><option value="Television" <?= $filtro === 'Television' ? 'selected' : '' ?>>Televisión</option></select>
                <button class="button secondary" type="submit">Buscar</button>
            </form>
            <?php if ($equipos === []): ?>
                <div class="empty"><span>▤</span><strong>No hay productos para mostrar</strong><p>Agrega un registro o cambia los filtros.</p></div>
            <?php else: ?>
                <div class="table-wrap"><table><thead><tr><th>Equipo</th><th>Tipo</th><th>Resumen</th><th>Estado</th><th></th></tr></thead><tbody>
                <?php foreach ($equipos as $equipo): ?>
                    <tr>
                        <td><strong><?= escapar($equipo['marca'] . ' ' . $equipo['modelo']) ?></strong><small><?= escapar($equipo['numeroSerie']) ?></small></td>
                        <td><span class="type-pill"><?= escapar($equipo['tipo']) ?></span></td>
                        <td><?= escapar($equipo['resumen']) ?></td>
                        <td><span class="status <?= $equipo['estado'] === 'Disponible' ? 'available' : ($equipo['estado'] === 'En uso' ? 'in-use' : 'repair') ?>"><?= escapar($equipo['estado']) ?></span></td>
                        <td><form method="post" action="index.php" onsubmit="return confirm('¿Eliminar este producto del inventario?')"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="numeroSerie" value="<?= escapar($equipo['numeroSerie']) ?>"><button class="delete" type="submit" aria-label="Eliminar <?= escapar($equipo['numeroSerie']) ?>">Eliminar</button></form></td>
                    </tr>
                <?php endforeach; ?>
                </tbody></table></div>
            <?php endif; ?>
        </section>
    </div>
    <footer>Los datos de la sesión se vuelven a crear como objetos de su subclase al abrir la página.</footer>
</main>
<script>
function actualizarCamposTipo() {
    const tipo = document.getElementById('tipoEquipo').value;
    const computadora = document.getElementById('camposComputadora');
    const escritorio = document.getElementById('camposEscritorio');
    const laptop = document.getElementById('camposLaptop');
    const television = document.getElementById('camposTelevision');

    computadora.hidden = !['Escritorio', 'Laptop'].includes(tipo);
    escritorio.hidden = tipo !== 'Escritorio';
    laptop.hidden = tipo !== 'Laptop';
    television.hidden = tipo !== 'Television';

    computadora.querySelectorAll('input').forEach((campo) => {
        campo.required = ['Escritorio', 'Laptop'].includes(tipo);
    });
    escritorio.querySelectorAll('input').forEach((campo) => {
        campo.required = tipo === 'Escritorio';
    });
    laptop.querySelectorAll('input').forEach((campo) => {
        campo.required = tipo === 'Laptop';
    });
    television.querySelectorAll('input').forEach((campo) => {
        campo.required = tipo === 'Television';
    });
}
document.getElementById('tipoEquipo').addEventListener('change', actualizarCamposTipo);
actualizarCamposTipo();
</script>
</body>
</html>
