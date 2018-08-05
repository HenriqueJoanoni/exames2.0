<!DOCTYPE html>
<head>
    <title>Gerencia Plus</title>
    <link rel="shortcut icon"  href="img/businessman.svg"/>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <script type="text/javascript" src="js/jquery-3.3.1.slim.min.js"></script>
    <script type="text/javascript" src="js/popper.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/app.js"></script>
    <script type="text/javascript" src="js/funcoes.js"></script>
    <script type="text/javascript" src="js/cep.js"></script>

</head>
<body>
<nav class="navbar navbar-expand-md navbar-light bg-light fixed-top py-3 box-shadow">
    <a href="index.php" class="navbar-brand">
        <img src="img/businessman.svg" alt="businessman.svg" width="120" height="50"><b>Gerencia Plus</b>
    </a>
    <div id="navbarsuportedcontent" class="navbar-nav ml-auto">
        <?php if(!isset($_SESSION)){echo 'Bem-vindo';}else{echo 'Olá,<b>'.$_SESSION['paramLogin'].'</b>!';}?>
    </div>
    <div class="nav nav-item ml-3">
        <?php if(isset($_SESSION)){echo '<button type="button" class="btn btn-outline-secondary" name="btSair" 
                                            onclick="javascript: location.href=\'apoio/logoff.php\'">Sair</button>';}?>
    </div>
</nav>
</body>
