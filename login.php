<?php
$offline = true;
$maintenance = true;
require './!#/init.php';

$erreur = false;
if ( checkArray($_POST,['username','password']) ) {
    if (checkIfEmptyArray($_POST, ['username', 'password'])) {
        $data = protectArray($_POST);
        if ( ($user = getUser($odb, $data['username'])) == null ) {
            redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Identifiant ou mot de passe invalide.',$actual_link);
        }
        else {
            if ( hash_equals($user['password'],crypt($data['password'],'niqueletatniquelesbanquesjevousbz'))) {
             
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['ip'] = getIp();
			        redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Tu est désormais connecté!','dashboard');
                    
            }
            else {
              redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Identifiant ou mot de passe invalide.',$actual_link);
            }
        }
    } else {
        redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Certains champs sont manquants.',$actual_link);
    }
}



?><!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>MesrineCC - Connexion</title>
    <link type="text/css" href="assets/css/vendor-material-icons.css" rel="stylesheet">
    <link type="text/css" href="assets/vendor/simplebar.min.css" rel="stylesheet">
    <link type="text/css" href="assets/css/app.css" rel="stylesheet">
    <link type="text/css" href="assets/css/vendor-fontawesome-free.css" rel="stylesheet">
    </head>

<body class="layout-login-centered-boxed bg-white">

    <div class="layout-login__form bg-white">
        <div class="d-flex justify-content-center mt-2 mb-5 navbar-light">
            <a href="./login" class="navbar-brand" style="min-width: 0">
                <center><img class="navbar-brand-icon" src="assets/2.png" width="250" alt="MesrineCC"></center>
            </a>
        </div>
		
		<?php
          $strFlash = '';
          if ( isset($_SESSION['flash']) or $strFlash != '') { ?>
              
                          <?php if ( $strFlash != '' ) {  ?>
                              <div class="alert alert-success">
                                  <?php echo $strFlash; ?>
                              </div>
                          <?php } ?>
                          <?php foreach ($_SESSION['flash'] as $type=>$message) { ?>
                              <div class="alert alert-<?php echo $type ?>">
                                  <?php echo $message; ?>
                              </div>

                          <?php } unset($_SESSION['flash']); ?>
                   
          <?php } ?>

       
        <form action="" method="POST">
            <div class="form-group">
                <label class="text-label" for="email_2">Identifiant</label>
                <div class="input-group input-group-merge">
                    <input id="email_2" name="username" type="text" required="" class="form-control form-control-prepended" placeholder="Anonguy">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <span class="far fa-user"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="text-label" for="password_2">Mot de passe:</label>
                <div class="input-group input-group-merge">
                    <input id="password_2" name="password" type="password" required="" class="form-control form-control-prepended" placeholder="Entrer votre mot de passe">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <span class="fa fa-key"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group mb-3">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" checked="" id="remember">
                    <label class="custom-control-label" for="remember">Se souvenir de moi</label>
                </div>
            </div>
			
			<input type="hidden" name="csrf" value="<?php echo $token ?>">
			
			<div class="form-group text-left">
			<a href="./register.php"><button class="btn btn-light mb-5" style="float:right;" type="button">Créer un compte</button></a>
			<button name="Log" class="btn btn-primary mb-5"  style="float:left;" type="submit">Connexion</button>
            </div>
			
			<div style="clear:both"></div>
			
            <div class="form-group text-center">
                

				<small>
				<a href="">« <?php echo randomCitation("./assets/jacques.txt"); ?> »</a>
				</small>
		   </div>
        </form>
    </div>


</body>
</html>

    <script src="assets/vendor/material-design-kit.js"></script>
