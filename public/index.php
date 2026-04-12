<?php
// 1. VITAL: Iniciar sesiones y cargar helpers
session_start();
require_once __DIR__ . '/../app/helpers.php';

// 2. Procesar la URL limpia
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = str_replace('/public', '', dirname($_SERVER['SCRIPT_NAME']));

if ($basePath !== '/' && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

$path = rtrim($path, '/') ?: '/';

// 3. ENRUTADOR (Switch de Rutas Plano)
switch ($path) {

    // ==========================================
    // PÚBLICO Y AUTENTICACIÓN
    // ==========================================
    case '/':                  require __DIR__ . '/../views/index.php'; break;
    case '/login':             require __DIR__ . '/../views/login.php'; break;
    case '/registro':          require __DIR__ . '/../views/registro.php'; break;
    
    case '/login/ingresar':    require_once __DIR__ . '/../app/Controllers/AuthController.php'; (new AuthController())->login(); break;
    case '/registro/guardar':  require_once __DIR__ . '/../app/Controllers/AuthController.php'; (new AuthController())->guardar(); break;
    case '/logout':            require_once __DIR__ . '/../app/Controllers/AuthController.php'; (new AuthController())->logout(); break;

    // ==========================================
    // PORTAL CLIENTE
    // ==========================================
    case '/cliente':             require_once __DIR__ . '/../app/Controllers/ClientePortalController.php'; (new ClientePortalController())->dashboard(); break;
    case '/nuestro-catalogo':    require_once __DIR__ . '/../app/Controllers/ClientePortalController.php'; (new ClientePortalController())->nuestroCatalogo(); break;
    case '/catalogos-asociados': require_once __DIR__ . '/../app/Controllers/ClientePortalController.php'; (new ClientePortalController())->catalogosAsociados(); break;
    case '/pedidos':             require_once __DIR__ . '/../app/Controllers/ClientePortalController.php'; (new ClientePortalController())->misPedidos(); break;
    
    case '/chatbot':             require_once __DIR__ . '/../app/Controllers/ClientePortalController.php'; (new ClientePortalController())->chatbot(); break;

    // ==========================================
    // PANEL EMPLEADO
    // ==========================================
    case '/empleado':                require __DIR__ . '/../views/empleado/empleado.php'; break;
    case '/empleado/clientes':       require __DIR__ . '/../views/empleado/clientes.php'; break;
    case '/empleado/pedidos':        require __DIR__ . '/../views/empleado/pedidos.php'; break;
    case '/empleado/pedidos/nuevo':  require_once __DIR__ . '/../app/Controllers/PedidoController.php'; (new PedidoController())->guardar(); break;

    // ==========================================
    // PANEL ADMINISTRADOR
    // ==========================================
    case '/admin': require __DIR__ . '/../views/admin/admin.php'; break;

    // --- PRODUCTOS ---
    case '/admin/productos':            require __DIR__ . '/../views/admin/productos.php'; break;
    case '/admin/productos/nuevo':      require_once __DIR__ . '/../app/Controllers/ProductoController.php'; (new ProductoController())->nuevo(); break;
    case '/admin/productos/guardar':    require_once __DIR__ . '/../app/Controllers/ProductoController.php'; (new ProductoController())->guardar(); break;
    case '/admin/productos/editar':     require_once __DIR__ . '/../app/Controllers/ProductoController.php'; (new ProductoController())->editar(); break;
    case '/admin/productos/actualizar': require_once __DIR__ . '/../app/Controllers/ProductoController.php'; (new ProductoController())->actualizar(); break;
    case '/admin/productos/eliminar':   require_once __DIR__ . '/../app/Controllers/ProductoController.php'; (new ProductoController())->eliminar(); break;

    // --- PROVEEDORES ---
    case '/admin/proveedores':            require __DIR__ . '/../views/admin/proveedores.php'; break;
    case '/admin/proveedores/nuevo':      require_once __DIR__ . '/../app/Controllers/ProveedorController.php'; (new ProveedorController())->nuevo(); break;
    case '/admin/proveedores/guardar':    require_once __DIR__ . '/../app/Controllers/ProveedorController.php'; (new ProveedorController())->guardar(); break;
    case '/admin/proveedores/editar':     require_once __DIR__ . '/../app/Controllers/ProveedorController.php'; (new ProveedorController())->editar(); break;
    case '/admin/proveedores/actualizar': require_once __DIR__ . '/../app/Controllers/ProveedorController.php'; (new ProveedorController())->actualizar(); break;
    case '/admin/proveedores/eliminar':   require_once __DIR__ . '/../app/Controllers/ProveedorController.php'; (new ProveedorController())->eliminar(); break;

    // --- CLIENTES ---
    case '/admin/clientes':            require __DIR__ . '/../views/admin/clientes.php'; break;
    case '/admin/clientes/nuevo':      require_once __DIR__ . '/../app/Controllers/ClienteController.php'; (new ClienteController())->nuevo(); break;
    case '/admin/clientes/guardar':    require_once __DIR__ . '/../app/Controllers/ClienteController.php'; (new ClienteController())->guardar(); break;
    case '/admin/clientes/editar':     require_once __DIR__ . '/../app/Controllers/ClienteController.php'; (new ClienteController())->editar(); break;
    case '/admin/clientes/actualizar': require_once __DIR__ . '/../app/Controllers/ClienteController.php'; (new ClienteController())->actualizar(); break;
    case '/admin/clientes/eliminar':   require_once __DIR__ . '/../app/Controllers/ClienteController.php'; (new ClienteController())->eliminar(); break;

    // --- PEDIDOS (ADMIN) ---
    case '/admin/pedidos':            require __DIR__ . '/../views/admin/pedidos.php'; break;
    case '/admin/pedidos/editar':     require_once __DIR__ . '/../app/Controllers/AdminPedidoController.php'; (new AdminPedidoController())->editar(); break;
    case '/admin/pedidos/actualizar': require_once __DIR__ . '/../app/Controllers/AdminPedidoController.php'; (new AdminPedidoController())->actualizar(); break;
    case '/admin/pedidos/eliminar':   require_once __DIR__ . '/../app/Controllers/AdminPedidoController.php'; (new AdminPedidoController())->eliminar(); break;

    // --- STOCK ---
    case '/admin/stock':            require __DIR__ . '/../views/admin/stock.php'; break;
    case '/admin/stock/nuevo':      require_once __DIR__ . '/../app/Controllers/StockController.php'; (new StockController())->nuevo(); break;
    case '/admin/stock/guardar':    require_once __DIR__ . '/../app/Controllers/StockController.php'; (new StockController())->guardar(); break;
    case '/admin/stock/editar':     require_once __DIR__ . '/../app/Controllers/StockController.php'; (new StockController())->editar(); break;
    case '/admin/stock/actualizar': require_once __DIR__ . '/../app/Controllers/StockController.php'; (new StockController())->actualizar(); break;
    case '/admin/stock/eliminar':   require_once __DIR__ . '/../app/Controllers/StockController.php'; (new StockController())->eliminar(); break;

    // --- OTROS ---
    case '/admin/empleados': require __DIR__ . '/../views/admin/empleados.php'; break;
    case '/admin/bitacoras': require __DIR__ . '/../views/admin/bitacoras.php'; break;

    // ==========================================
    // PÁGINA DE ERROR 404
    // ==========================================
    default:
        http_response_code(404);
        echo "<h1 style='text-align:center; margin-top:50px; color:#FF8C00;'>404 - Página no encontrada</h1>";
        break;
}