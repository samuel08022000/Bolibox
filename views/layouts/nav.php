

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
  *{
  margin: 0;
  padding: 0;
  text-decoration: none;
  list-style: none;
  box-sizing: border-box;
}
body{
  font-family: "Poppins", sans-serif;
}

nav{
  background: linear-gradient(1deg, rgba(0,0,0,.1), rgba(0,0,0,0.5));
  backdrop-filter: saturate(100%) blur(2px);
}
img{
  mix-blend-mode: multiply;
}
.navbar img{
  width: 160px;
  margin:  0 0 0 120px;
}

.icons{
  display: flex;
  gap: 2rem;
  margin: 5px 70px;
}

.butons{
  padding: 10px 15px;
  font-size: 20px;
  border-radius: 40px;
}

.user{
  border: 2px solid white;
  background-color: transparent;
  color: white;
}

.user:hover{
  transform: translateY(-3px);
  box-shadow: 0 5px 8px white;
}

.shop{
  background-color: white;
  color: black;
}

.shop:hover{
  color: white;
  background-color: black;
  transform: translateY(-3px);
  box-shadow: 0 5px 8px white;
}

nav ul li a{
  position: relative; 
}


nav ul li a::after {
  content: ''; 
  position: absolute; 
  bottom: 3px; 
  left: 0;
  width: 0; 
  height: 2px; 
  background-color: white; 
  transition: width 0.6s ease; 
}

nav ul li a:hover:after {
  width: 100%; 
}

</style>
</head>
<body>
  <nav class="navbar navbar-expand-lg fixed-top ">
  <div class="container-fluid">
    <button class="navbar-toggler" style="color: white;" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand col " href="inicio.php"><img src="https://fairplaybo.vtexassets.com/assets/vtex/assets-builder/fairplaybo.fairplay-theme/1.0.5/imgs/logo-fairplaybo___edd19225884e46691e35a9a4df4aac8c.png" alt=""></a>
    <div class="collapse navbar-collapse " id="navbarTogglerDemo01">
      <ul class="nav mx-auto navbar-nav">
        <li class="nav-item active">
          <a class="nav-link text-white" href="inicio.php">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="catalogo.php">catalogo</a>
            </li>
            <li class="nav-item ">
              <a class="nav-link text-white" href="contacto.php">sucursales</a>
            </li>
          </ul>
          <div class="mr-auto ">
            <form class="d-flex" role="search">
              <input class="form-control search me-2" type="search"  aria-label="Search"/>
              <button class="btn-search btn btn-outline-light" type="submit" >Buscar</button>
            </form>
          </div>
          <div class="icons navbar-nav">
            <div class="position-relative d-inline-block">
              <a href="checkout.php" class="shop butons"><i class="fa-solid fa-cart-shopping mt-3" ></i></a>

              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-light border border-dark p-2"
          style="font-size: 0.8em; color:black;">
              <?php echo $num_cart;?>
        
                </span>
              </div>
              <?php if(isset($_SESSION['user_id'])){ ?>

                
              <a href="cuenta.php" class="user butons" style="text-decoration:none;"><?php echo $_SESSION['user_name']. ' '?></a>
              <?php } else { ?>
                <a href="login.php" class="user butons"><i class="fa-solid fa-user "></i></a>
              <?php }?>
          </div>
        </div>
    </nav>
</body>
</html>