<?php

// =====================================================================
// 1. CONFIGURACIÓN INICIAL (Sesiones y Helpers)
// =====================================================================

session_start();
require_once __DIR__ . '/../app/helpers.php';


// =====================================================================
// 2. LIMPIEZA DE LA URL (Para que el enrutador entienda qué pedimos)
// =====================================================================

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = str_replace('/public', '', dirname($_SERVER['SCRIPT_NAME']));

if ($basePath !== '/' && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

$path = rtrim($path, '/') ?: '/';


// =====================================================================
// 3. ENRUTADOR PRINCIPAL (Switch)
// =====================================================================

switch ($path) {

    // ---------------------------------------------------
    // RUTAS PÚBLICAS Y DE AUTENTICACIÓN
    // ---------------------------------------------------

    case '/':
        require __DIR__ . '/../views/index.php';
        break;

    case '/login':
        require __DIR__ . '/../views/login.php';
        break;

    case '/registro':
        require __DIR__ . '/../views/registro.php';
        break;

    case '/login/ingresar':
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        (new AuthController())->login();
        break;

    case '/registro/guardar':
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        (new AuthController())->guardar();
        break;

    case '/verificar_registro_otp':
        // Esta ruta simplemente carga la vista del formulario para confirmar el correo
        require_once __DIR__ . '/../views/verificar_registro_otp.php';
        break;

    case '/registro/validar':
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        (new AuthController())->validar_registro_otp();
        break;

    case '/logout':
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        (new AuthController())->logout();
        break;
        
    case '/verificar_otp':
        // Esta ruta simplemente carga la vista del formulario
        require_once __DIR__ . '/../views/verificar_otp.php';
        break;

    case '/verificar_otp/validar':
        // Esta ruta procesa el código enviado
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        (new AuthController())->verificar_otp();
        break;
    case '/recuperar':
        // Vista para ingresar el correo electrónico
        require_once __DIR__ . '/../views/recuperar_password.php';
        break;

    case '/recuperar/enviar':
        // Procesa la solicitud y envía el correo con el link
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        (new AuthController())->solicitar_recuperacion();
        break;

    case '/reset-password':
        // Vista donde el usuario escribe su nueva contraseña (viene del link del correo)
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        (new AuthController())->mostrar_formulario_reset();
        break;

    case '/reset-password/actualizar':
        // Procesa el cambio final de la contraseña
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        (new AuthController())->actualizar_password();
        break;


    // ---------------------------------------------------
    // PORTAL CLIENTE
    // ---------------------------------------------------

    case '/cliente':
        require_once __DIR__ . '/../app/Controllers/ClientePortalController.php';
        (new ClientePortalController())->dashboard();
        break;

    case '/nuestro-catalogo':
        require_once __DIR__ . '/../app/Controllers/ClientePortalController.php';
        (new ClientePortalController())->nuestroCatalogo();
        break;

    case '/catalogos-asociados':
        require_once __DIR__ . '/../app/Controllers/ClientePortalController.php';
        (new ClientePortalController())->catalogosAsociados();
        break;

    case '/pedidos':
        require_once __DIR__ . '/../app/Controllers/ClientePortalController.php';
        (new ClientePortalController())->misPedidos();
        break;

    case '/chatbot':
        require_once __DIR__ . '/../app/Controllers/ClientePortalController.php';
        (new ClientePortalController())->chatbot();
        break;


    // ---------------------------------------------------
    // PANEL EMPLEADO
    // ---------------------------------------------------

    case '/empleado':
        require_once __DIR__ . '/../app/Controllers/EmpleadoPortalController.php';
        (new EmpleadoPortalController())->index();
        break;

    case '/empleado/clientes':
        require_once __DIR__ . '/../app/Controllers/EmpleadoPortalController.php';
        (new EmpleadoPortalController())->clientes();
        break;

    case '/empleado/pedidos':
        require_once __DIR__ . '/../app/Controllers/EmpleadoPortalController.php';
        (new EmpleadoPortalController())->pedidos();
        break;

    case '/empleado/pedidos/nuevo':
        require_once __DIR__ . '/../app/Controllers/PedidoController.php';
        (new PedidoController())->guardar();
        break;

    case '/empleado/clientes/actualizar':
        require_once __DIR__ . '/../app/Controllers/PedidoController.php';
        (new PedidoController())->actualizarCliente();
        break;

    case '/empleado/clientes/eliminar':
        require_once __DIR__ . '/../app/Controllers/PedidoController.php';
        (new PedidoController())->eliminarCliente();
        break;

    case '/empleado/pedidos/editar':
        require_once __DIR__ . '/../app/Controllers/PedidoController.php';
        (new PedidoController())->editar();
        break;

    case '/empleado/pedidos/actualizar':
        require_once __DIR__ . '/../app/Controllers/PedidoController.php';
        (new PedidoController())->actualizar();
        break;

    case '/empleado/pedidos/eliminar':
        require_once __DIR__ . '/../app/Controllers/PedidoController.php';
        (new PedidoController())->eliminar();
        break;


    // ---------------------------------------------------
    // PANEL ADMINISTRADOR (Dashboard Principal)
    // ---------------------------------------------------

    case '/admin':
        require_once __DIR__ . '/../app/Controllers/AdminController.php';
        (new AdminController())->index();
        break;


    // ---------------------------------------------------
    // ADMIN: PRODUCTOS
    // ---------------------------------------------------

    case '/admin/productos':
        require_once __DIR__ . '/../app/Controllers/ProductoController.php';
        (new ProductoController())->index();
        break;

    case '/admin/productos/guardar':
        require_once __DIR__ . '/../app/Controllers/ProductoController.php';
        (new ProductoController())->guardar();
        break;

    case '/admin/productos/editar':
        require_once __DIR__ . '/../app/Controllers/ProductoController.php';
        (new ProductoController())->editar();
        break;

    case '/admin/productos/actualizar':
        require_once __DIR__ . '/../app/Controllers/ProductoController.php';
        (new ProductoController())->actualizar();
        break;

    case '/admin/productos/eliminar':
        require_once __DIR__ . '/../app/Controllers/ProductoController.php';
        (new ProductoController())->eliminar();
        break;


    // ---------------------------------------------------
    // ADMIN: PROVEEDORES
    // ---------------------------------------------------

    case '/admin/proveedores':
        require_once __DIR__ . '/../app/Controllers/ProveedorController.php';
        (new ProveedorController())->index();
        break;

    case '/admin/proveedores/guardar':
        require_once __DIR__ . '/../app/Controllers/ProveedorController.php';
        (new ProveedorController())->guardar();
        break;

    case '/admin/proveedores/editar':
        require_once __DIR__ . '/../app/Controllers/ProveedorController.php';
        (new ProveedorController())->editar();
        break;

    case '/admin/proveedores/actualizar':
        require_once __DIR__ . '/../app/Controllers/ProveedorController.php';
        (new ProveedorController())->actualizar();
        break;

    case '/admin/proveedores/eliminar':
        require_once __DIR__ . '/../app/Controllers/ProveedorController.php';
        (new ProveedorController())->eliminar();
        break;


    // ---------------------------------------------------
    // ADMIN: CLIENTES
    // ---------------------------------------------------

    case '/admin/clientes':
        require_once __DIR__ . '/../app/Controllers/ClienteController.php';
        (new ClienteController())->index();
        break;

    case '/admin/clientes/guardar':
        require_once __DIR__ . '/../app/Controllers/ClienteController.php';
        (new ClienteController())->guardar();
        break;

    case '/admin/clientes/editar':
        require_once __DIR__ . '/../app/Controllers/ClienteController.php';
        (new ClienteController())->editar();
        break;

    case '/admin/clientes/actualizar':
        require_once __DIR__ . '/../app/Controllers/ClienteController.php';
        (new ClienteController())->actualizar();
        break;

    case '/admin/clientes/eliminar':
        require_once __DIR__ . '/../app/Controllers/ClienteController.php';
        (new ClienteController())->eliminar();
        break;


    // ---------------------------------------------------
    // ADMIN: PEDIDOS
    // ---------------------------------------------------

    case '/admin/pedidos':
        require_once __DIR__ . '/../app/Controllers/AdminPedidoController.php';
        (new AdminPedidoController())->index();
        break;

    case '/admin/pedidos/guardar':
        require_once __DIR__ . '/../app/Controllers/AdminPedidoController.php';
        (new AdminPedidoController())->guardar();
        break;

    case '/admin/pedidos/editar':
        require_once __DIR__ . '/../app/Controllers/AdminPedidoController.php';
        (new AdminPedidoController())->editar();
        break;

    case '/admin/pedidos/actualizar':
        require_once __DIR__ . '/../app/Controllers/AdminPedidoController.php';
        (new AdminPedidoController())->actualizar();
        break;

    case '/admin/pedidos/eliminar':
        require_once __DIR__ . '/../app/Controllers/AdminPedidoController.php';
        (new AdminPedidoController())->eliminar();
        break;


    // ---------------------------------------------------
    // ADMIN: STOCK
    // ---------------------------------------------------

    case '/admin/stock':
        require_once __DIR__ . '/../app/Controllers/StockController.php';
        (new StockController())->index();
        break;

    case '/admin/stock/guardar':
        require_once __DIR__ . '/../app/Controllers/StockController.php';
        (new StockController())->guardar();
        break;

    case '/admin/stock/editar':
        require_once __DIR__ . '/../app/Controllers/StockController.php';
        (new StockController())->editar();
        break;

    case '/admin/stock/actualizar':
        require_once __DIR__ . '/../app/Controllers/StockController.php';
        (new StockController())->actualizar();
        break;

    case '/admin/stock/eliminar':
        require_once __DIR__ . '/../app/Controllers/StockController.php';
        (new StockController())->eliminar();
        break;


    // ---------------------------------------------------
    // ADMIN: OTROS (Empleados y Bitácoras)
    // ---------------------------------------------------

    case '/admin/empleados':
        require_once __DIR__ . '/../app/Controllers/EmpleadoController.php';
        (new EmpleadoController())->index();
        break;

    case '/admin/empleados/guardar':
        require_once __DIR__ . '/../app/Controllers/EmpleadoController.php';
        (new EmpleadoController())->guardar();
        break;

    case '/admin/empleados/cambiar-estado':
        require_once __DIR__ . '/../app/Controllers/EmpleadoController.php';
        (new EmpleadoController())->cambiarEstado();
        break;

    case '/admin/bitacoras':
        require_once __DIR__ . '/../app/Controllers/BitacoraController.php';
        (new BitacoraController())->index();
        break;


    // ---------------------------------------------------
    // PÁGINA DE ERROR 404
    // ---------------------------------------------------

    default:
        http_response_code(404);
        echo "<h1 style='text-align:center; margin-top:50px; color:#FF8C00; font-family: sans-serif;'>404 - Página no encontrada</h1>";
        break;
}