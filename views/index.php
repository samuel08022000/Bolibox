<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BOLIBOX - Importadora</title>
  
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
  
  <header>
    <div class="nav-inner">
      <a href="<?= url('/') ?>" class="logo">
        <i class="bi bi-globe-americas"></i> BOLI<span>BOX</span>
      </a>
      <nav>
        <a href="#home" class="active">Inicio</a>
        <a href="#nosotros">Quiénes Somos</a>
        <a href="#servicios">Servicios</a>
        <a href="#sucursales">Sucursales</a>
        <a href="<?= url('registro') ?>">Regístrate</a>
      </nav>
    </div>
  </header>

  <section id="home">
    <div class="hero-bg">
      <img src="https://images.unsplash.com/photo-1580674285054-bed31e145f59?w=1920&h=1080&fit=crop&q=80" alt="Furgoneta de entrega Bolibox" />
      <div class="hero-overlay"></div>
    </div>
    
    <div class="hero-content">
      <div class="hero-text">
        <h1 class="hero-title">Tus paquetes protegidos y entregados con total seguridad.</h1>
        <p class="hero-desc">Courier especializado en importaciones, consolidación de paquetes y entregas puerta a puerta.</p>
        <p class="hero-subtext">Con <strong>BOLIBOX SRL</strong>, tus compras del exterior llegan de forma rápida, segura y con seguimiento permanente.</p>
        
        <div class="hero-btns">
          <a href="#servicios" class="btn-naranja">Nuestros Servicios →</a>
        </div>
      </div>

      <div class="login-card">
        <h3>Iniciar sesión</h3>
        <p>Ingresa tus credenciales para acceder a tu panel y conocer el estado de tus paquetes en tiempo real.</p>
        
        <form action="<?= url('login') ?>" method="POST">
          <div class="form-group-login">
            <input type="text" name="usuario" placeholder="Usuario" required />
          </div>
          <div class="form-group-login">
            <input type="password" name="password" placeholder="Contraseña" required />
          </div>
          <button type="submit" class="btn-login-submit">
            <i class="bi bi-box-arrow-in-right"></i> Ingresar
          </button>
        </form>
        
        <div class="login-footer">
          <i class="bi bi-person-plus"></i> ¿Aún no eres cliente? 
          <a href="<?= url('registro') ?>">Regístrate aquí</a>
        </div>
      </div>
    </div>
  </section>

  <section id="nosotros" class="section-pad">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">¿Quiénes somos?</h2>
      </div>
      
      <div class="nosotros-texto-intro">
        <h4>BOLIBOX SRL es una empresa boliviana especializada en logística, importaciones y soluciones de courier puerta a puerta.</h4>
        <p>Consolidamos tus compras del exterior (Estados Unidos, China, Europa, etc.) y las entregamos directamente en tu ciudad.</p>
      </div>

      <div class="nosotros-grid">
        <div class="card-nosotros">
          <i class="bi bi-award"></i>
          <h4>Más de 10 años</h4>
          <p>De experiencia en logística y courier internacional</p>
        </div>
        <div class="card-nosotros">
          <i class="bi bi-shield-check"></i>
          <h4>Seguridad Garantizada</h4>
          <p>Tus paquetes están asegurados durante todo el proceso</p>
        </div>
        <div class="card-nosotros">
          <i class="bi bi-headset"></i>
          <h4>Soporte Personalizado</h4>
          <p>Atención directa y seguimiento individualizado</p>
        </div>
      </div>

      <div class="nosotros-footer">
        <p>Nuestro compromiso es ofrecer un servicio rápido, transparente y confiable, pensado para personas y empresas que necesitan traer productos sin complicaciones ni trámites complejos.</p>
      </div>
    </div>
  </section>

  <section id="servicios" class="section-pad">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Nuestros servicios</h2>
      </div>

      <div class="servicios-grid">
        <div class="card-servicio">
          <i class="bi bi-globe-americas"></i>
          <h4>Importaciones internacionales</h4>
          <p>Consolidación de paquetes, asesoramiento, seguimiento y gestión completa hasta Bolivia.</p>
        </div>
        <div class="card-servicio">
          <i class="bi bi-truck"></i>
          <h4>Envíos nacionales</h4>
          <p>Distribución segura en las principales ciudades del país con seguimiento en tiempo real.</p>
        </div>
        <div class="card-servicio">
          <i class="bi bi-house-door"></i>
          <h4>Entrega puerta a puerta</h4>
          <p>De nuestro almacén a tu dirección. Fácil, rápido y sin estrés.</p>
        </div>
        <div class="card-servicio">
          <i class="bi bi-search"></i>
          <h4>Rastreo en tiempo real</h4>
          <p>Consulta el estado de tu guía desde cualquier dispositivo, 24/7.</p>
        </div>
      </div>
    </div>
  </section>

  <section id="sucursales" class="section-pad">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Nuestras sucursales</h2>
      </div>

      <div class="sucursales-grid">
        <div class="card-sucursal">
          <h3>Sucursal La Paz</h3>
          <div class="info-line">
            <i class="bi bi-geo-alt"></i>
            <div><strong>Dirección:</strong> Zona San Pedro entre 20 de octubre y Otero de la Vega. Edificio Torre Mollinedo Nro 1628 piso 6 oficina 3.</div>
          </div>
          <div class="info-line">
            <i class="bi bi-clock"></i>
            <div><strong>Horarios:</strong> Lunes a viernes de 8:30 a 12:30 / 14:30 a 18:30<br>Sábados de 9:00 a 13:00</div>
          </div>
        </div>

        <div class="card-sucursal">
          <h3>Sucursal Cochabamba</h3>
          <div class="info-line">
            <i class="bi bi-geo-alt"></i>
            <div><strong>Dirección:</strong> Calle Alcides Arguedas esquina Manchay Puito Edif Akros PB oficina 1.</div>
          </div>
          <div class="info-line">
            <i class="bi bi-clock"></i>
            <div><strong>Horarios:</strong> Lunes a viernes de 8:30 a 12:30 / 14:30 a 18:30<br>Sábados de 9:00 a 13:00</div>
          </div>
        </div>

        <div class="card-sucursal">
          <h3>Sucursal Santa Cruz</h3>
          <div class="info-line">
            <i class="bi bi-geo-alt"></i>
            <div><strong>Dirección:</strong> Entre 4to y 5to anillo (Zona Cambodromo), Calle 1 # 1225, Entre c/ transversal 1 y 2.<br><strong>BOLIBOX SRL</strong></div>
          </div>
          <div class="info-line">
            <i class="bi bi-clock"></i>
            <div><strong>Horarios:</strong> Lunes a viernes de 8:30 a 12:30 / 14:30 a 18:30<br>Sábados de 9:00 a 13:00</div>
          </div>
        </div>
      </div>
    </div>
  </section>

</body>
</html>