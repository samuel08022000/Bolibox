<?php
// Cargar los ayudantes del Paso 3
require_once __DIR__ . '/../app/helpers.php';

// Obtener la ruta de la URL
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = str_replace('/public', '', dirname($_SERVER['SCRIPT_NAME']));

if ($basePath !== '/' && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

<<<<<<< HEAD
// Limpiar la barra diagonal (/) al final de la ruta si el usuario la escribe por accidente
=======
// NUEVO: Limpiar la barra diagonal (/) al final de la ruta si el usuario la escribe por accidente
>>>>>>> a54a5a11c554ba13bec3314e3e2fb268c8f429b6
$path = rtrim($path, '/');

if ($path === '' || $path === false) $path = '/';

// SIMULADOR DE RUTAS: Aquí defines qué archivo de /views se muestra
switch ($path) {
    // ==========================================
    // RUTAS PÚBLICAS Y DE AUTENTICACIÓN
    // ==========================================
    case '/':
        require __DIR__ . '/../views/index.php'; 
        break;
    case '/registro':
        require __DIR__ . '/../views/registro.php';
        break;

    // ==========================================
<<<<<<< HEAD
    // RUTAS DEL PORTAL CLIENTE (Actualizadas a tu nueva estructura)
=======
    // RUTAS DEL PORTAL CLIENTE
>>>>>>> a54a5a11c554ba13bec3314e3e2fb268c8f429b6
    // ==========================================
    case '/cliente':
        require __DIR__ . '/../views/cliente/cliente.php';
        break;
<<<<<<< HEAD
    case '/catalogo':
        require __DIR__ . '/../views/cliente/catalogo.php'; 
        break;
    case '/pedidos':
        require __DIR__ . '/../views/cliente/pedidos.php'; 
        break;
    case '/chatbot':
        require __DIR__ . '/../views/cliente/chatbot.php'; 
=======
    case '/productos':
        require __DIR__ . '/../views/cliente/productos.php'; 
        break;
    case '/mis_pedidos':
        require __DIR__ . '/../views/cliente/mis_pedidos.php'; 
>>>>>>> a54a5a11c554ba13bec3314e3e2fb268c8f429b6
        break;

    // ==========================================
    // RUTAS DEL PANEL DE ADMINISTRACIÓN
    // ==========================================
    case '/admin':
        require __DIR__ . '/../views/admin/admin.php';
        break;
    case '/admin/productos':
        require __DIR__ . '/../views/admin/productos.php';
        break;
    case '/admin/proveedores':
        require __DIR__ . '/../views/admin/proveedores.php';
        break;
    case '/admin/clientes':
        require __DIR__ . '/../views/admin/clientes.php';
        break;
    case '/admin/empleados':
        require __DIR__ . '/../views/admin/empleados.php';
        break;
    case '/admin/pedidos':
        require __DIR__ . '/../views/admin/pedidos.php';
        break;
    case '/admin/stock':
        require __DIR__ . '/../views/admin/stock.php';
        break;
    case '/admin/bitacoras':
        require __DIR__ . '/../views/admin/bitacoras.php';
        break;

    // ==========================================
    // RUTAS DEL PANEL DE EMPLEADO
    // ==========================================
    case '/empleado':
        require __DIR__ . '/../views/empleado/empleado.php';
        break;
    case '/empleado/clientes':
        require __DIR__ . '/../views/empleado/clientes.php';
        break;
    case '/empleado/pedidos':
        require __DIR__ . '/../views/empleado/pedidos.php';
        break;

    // ==========================================
    // PÁGINA DE ERROR 404
    // ==========================================
    default:
        http_response_code(404);
        echo "404 - Página no encontrada en la simulación";
        break;
}