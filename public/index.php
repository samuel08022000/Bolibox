<?php
// Cargar los ayudantes del Paso 3
require_once __DIR__ . '/../app/helpers.php';

// Obtener la ruta de la URL
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = str_replace('/public', '', dirname($_SERVER['SCRIPT_NAME']));

if ($basePath !== '/' && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// Limpiar la barra diagonal (/) al final de la ruta si el usuario la escribe por accidente
$path = rtrim($path, '/');

if ($path === '' || $path === false) $path = '/';

// SIMULADOR DE RUTAS: Aquí defines qué archivo de /views se muestra
switch ($path) {
// ==========================================
    // 1. RUTAS PÚBLICAS Y DE AUTENTICACIÓN
    // ==========================================
    case '/':
        require __DIR__ . '/../views/index.php'; 
        break;
    case '/login':   // <--- AÑADE ESTA RUTA
        require __DIR__ . '/../views/login.php';
        break;
    case '/registro':
        require __DIR__ . '/../views/registro.php';
        break;

    // ==========================================
    // 2. RUTAS DEL PORTAL CLIENTE
    // ==========================================
    case '/cliente':
        require __DIR__ . '/../views/cliente/cliente.php';
        break;
    case '/nuestro-catalogo': 
        // Corregido el guion bajo para que coincida con el nombre del archivo
        require __DIR__ . '/../views/cliente/nuestro_catalogo.php'; 
        break;
    case '/catalogos-asociados': 
        require __DIR__ . '/../views/cliente/catalogo.php'; 
        break;
    case '/pedidos':
        require __DIR__ . '/../views/cliente/pedidos.php'; 
        break;
    case '/chatbot':
        require __DIR__ . '/../views/cliente/chatbot.php'; 
        break;

    // ==========================================
    // 3. RUTAS DE VISTAS: PANEL DE ADMINISTRACIÓN
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
    // 4. RUTAS DE ACCIÓN CRUD (ADMINISTRADOR)
    // ==========================================
    
    // --- EMPLEADOS ---
    case '/admin/empleados/guardar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        $sql = $con->prepare("INSERT INTO empleados (id_usuario, nombre, cargo, ci, celular) VALUES (?, ?, ?, ?, ?)");
        $sql->execute([$_POST['id_usuario'], $_POST['nombre'], $_POST['cargo'], $_POST['ci'], $_POST['celular']]);
        header("Location: " . url('admin/empleados'));
        break;

    // --- PROVEEDORES ---
    case '/admin/proveedores/guardar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        $sql = $con->prepare("INSERT INTO proveedor (nombre, pais, contacto, correo, tipo_moneda) VALUES (?, ?, ?, ?, ?)");
        $sql->execute([$_POST['nombre'], $_POST['pais'], $_POST['contacto'], $_POST['correo'], $_POST['tipo_moneda']]);
        header("Location: " . url('admin/proveedores'));
        break;
    case '/admin/proveedores/actualizar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        $sql = $con->prepare("UPDATE proveedor SET nombre=?, pais=?, contacto=?, correo=?, tipo_moneda=? WHERE id_proveedor=?");
        $sql->execute([$_POST['nombre'], $_POST['pais'], $_POST['contacto'], $_POST['correo'], $_POST['tipo_moneda'], $_POST['id_proveedor']]);
        header("Location: " . url('admin/proveedores'));
        break;
    case '/admin/proveedores/eliminar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        if (isset($_GET['id'])) {
            $sql = $con->prepare("DELETE FROM proveedor WHERE id_proveedor = ?");
            $sql->execute([$_GET['id']]);
        }
        header("Location: " . url('admin/proveedores'));
        break;

    // --- PRODUCTOS ---
    case '/admin/productos/guardar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        $sql = $con->prepare("INSERT INTO producto (nombre, descripcion, categoria, precio_unitario, id_proveedor) VALUES (?, ?, ?, ?, ?)");
        $sql->execute([$_POST['nombre'], $_POST['descripcion'], $_POST['categoria'], $_POST['precio_unitario'], $_POST['id_proveedor']]);
        header("Location: " . url('admin/productos'));
        break;
    case '/admin/productos/actualizar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        $sql = $con->prepare("UPDATE producto SET nombre=?, categoria=?, precio_unitario=? WHERE id_producto=?");
        $sql->execute([$_POST['nombre'], $_POST['categoria'], $_POST['precio_unitario'], $_POST['id_producto']]);
        header("Location: " . url('admin/productos'));
        break;
    case '/admin/productos/eliminar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        if (isset($_GET['id'])) {
            $sql = $con->prepare("DELETE FROM producto WHERE id_producto = ?");
            $sql->execute([$_GET['id']]);
        }
        header("Location: " . url('admin/productos'));
        break;

    // --- CLIENTES ---
    case '/admin/clientes/guardar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        $sql = $con->prepare("INSERT INTO clientes (nombre, nit, telefono, ciudad) VALUES (?, ?, ?, ?)");
        $sql->execute([$_POST['nombre'], $_POST['nit'], $_POST['telefono'], $_POST['ciudad']]);
        header("Location: " . url('admin/clientes'));
        break;
    case '/admin/clientes/actualizar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        $sql = $con->prepare("UPDATE clientes SET nombre=?, telefono=?, ciudad=? WHERE id_cliente=?");
        $sql->execute([$_POST['nombre'], $_POST['telefono'], $_POST['ciudad'], $_POST['id_cliente']]);
        header("Location: " . url('admin/clientes'));
        break;
    case '/admin/clientes/eliminar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        if (isset($_GET['id'])) {
            $sql = $con->prepare("DELETE FROM clientes WHERE id_cliente = ?");
            $sql->execute([$_GET['id']]);
        }
        header("Location: " . url('admin/clientes'));
        break;

    // --- STOCK ---
    case '/admin/stock/guardar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        $sql = $con->prepare("INSERT INTO stock (id_producto, id_almacen, cantidad) VALUES (?, ?, ?)");
        $sql->execute([$_POST['id_producto'], $_POST['id_almacen'], $_POST['cantidad']]);
        header("Location: " . url('admin/stock'));
        break;
    case '/admin/stock/actualizar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        $sql = $con->prepare("UPDATE stock SET cantidad=? WHERE id_stock=?");
        $sql->execute([$_POST['cantidad'], $_POST['id_stock']]);
        header("Location: " . url('admin/stock'));
        break;
    case '/admin/stock/eliminar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        if (isset($_GET['id'])) {
            $sql = $con->prepare("DELETE FROM stock WHERE id_stock = ?");
            $sql->execute([$_GET['id']]);
        }
        header("Location: " . url('admin/stock'));
        break;

    // --- PEDIDOS (ADMIN) ---
    case '/admin/pedidos/guardar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        
        $id_producto = !empty($_POST['id_producto']) ? $_POST['id_producto'] : null;
        $producto_importar = !empty($_POST['producto_importar']) ? $_POST['producto_importar'] : null;
        $fecha = date("Y-m-d"); 
        
        $sql = $con->prepare("INSERT INTO pedidos (fecha, total, ubicacion_clientes, nro_dui, id_cliente, id_empleado, id_producto, producto_importar) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $sql->execute([$fecha, $_POST['total'], $_POST['ubicacion'], $_POST['nro_dui'], $_POST['id_cliente'], $_POST['id_empleado'], $id_producto, $producto_importar]);
        
        header("Location: " . url('admin/pedidos'));
        break;

    case '/admin/pedidos/actualizar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        
        $id_producto = !empty($_POST['id_producto']) ? $_POST['id_producto'] : null;
        $producto_importar = !empty($_POST['producto_importar']) ? $_POST['producto_importar'] : null;
        
        $sql = $con->prepare("UPDATE pedidos SET total=?, ubicacion_clientes=?, nro_dui=?, id_cliente=?, id_empleado=?, id_producto=?, producto_importar=? WHERE id_pedido=?");
        $sql->execute([$_POST['total'], $_POST['ubicacion'], $_POST['nro_dui'], $_POST['id_cliente'], $_POST['id_empleado'], $id_producto, $producto_importar, $_POST['id_pedido']]);
        
        header("Location: " . url('admin/pedidos'));
        break;

    case '/admin/pedidos/eliminar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        if (isset($_GET['id'])) {
            $sql = $con->prepare("DELETE FROM pedidos WHERE id_pedido = ?");
            $sql->execute([$_GET['id']]);
        }
        header("Location: " . url('admin/pedidos'));
        break;

    // ==========================================
    // 5. RUTAS DE VISTAS: PANEL DE EMPLEADO
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
    // 6. RUTAS DE ACCIÓN CRUD (EMPLEADO)
    // ==========================================
    
    case '/empleado/pedidos/nuevo':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        
        // Buscamos si el cliente ya existe
        $stmt = $con->prepare("SELECT id_cliente FROM clientes WHERE nit = ?");
        $stmt->execute([$_POST['nit']]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($cliente) {
            $id_cliente = $cliente['id_cliente'];
        } else {
            $stmtInsert = $con->prepare("INSERT INTO clientes (nombre, nit, telefono, ciudad) VALUES (?, ?, ?, ?)");
            $stmtInsert->execute([$_POST['nombre'], $_POST['nit'], $_POST['telefono'], $_POST['ubicacion']]);
            $id_cliente = $con->lastInsertId(); 
        }

        $id_producto = !empty($_POST['id_producto']) ? $_POST['id_producto'] : null;
        $producto_importar = !empty($_POST['producto_importar']) ? $_POST['producto_importar'] : null;
        $fecha = date("Y-m-d"); 
        
        $sql = $con->prepare("INSERT INTO pedidos (fecha, total, ubicacion_clientes, nro_dui, id_cliente, id_empleado, id_producto, producto_importar) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $sql->execute([
            $fecha, $_POST['total'], $_POST['ubicacion'], $_POST['nro_dui'], 
            $id_cliente, $_POST['id_empleado'], $id_producto, $producto_importar
        ]);
        
        header("Location: " . url('empleado/pedidos'));
        break;
        
    case '/empleado/pedidos/actualizar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        $sql = $con->prepare("UPDATE pedidos SET total=?, ubicacion_clientes=?, nro_dui=? WHERE id_pedido=?");
        $sql->execute([$_POST['total'], $_POST['ubicacion'], $_POST['nro_dui'], $_POST['id_pedido']]);
        header("Location: " . url('empleado/pedidos'));
        break;
        
    case '/empleado/pedidos/eliminar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        if (isset($_GET['id'])) {
            $sql = $con->prepare("DELETE FROM pedidos WHERE id_pedido = ?");
            $sql->execute([$_GET['id']]);
        }
        header("Location: " . url('empleado/pedidos'));
        break;
        
    case '/empleado/clientes/actualizar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        $sql = $con->prepare("UPDATE clientes SET nombre=?, telefono=?, ciudad=? WHERE id_cliente=?");
        $sql->execute([$_POST['nombre'], $_POST['telefono'], $_POST['ciudad'], $_POST['id_cliente']]);
        header("Location: " . url('empleado/clientes'));
        break;
        
    case '/empleado/clientes/eliminar':
        require_once __DIR__ . '/../config/database.php';
        $db = new Database(); $con = $db->conectar();
        if (isset($_GET['id'])) {
            $sql = $con->prepare("DELETE FROM clientes WHERE id_cliente = ?");
            $sql->execute([$_GET['id']]);
        }
        header("Location: " . url('empleado/clientes'));
        break;

    // ==========================================
    // 7. PÁGINA DE ERROR 404
    // ==========================================
    default:
        http_response_code(404);
        echo "404 - Página no encontrada";
        break;
}