<?php
$offline = true;
$maintenance = true;
require './!#/init.php';

$erreur = false;
if ( checkArray($_POST,['username','password','password-confirm']) ) {
    if ( checkIfEmptyArray($_POST,['username','password','password-confirm'])) {
        $data = protectArray($_POST);
     
        if (ctype_alnum($data['username']) != 1) {
            redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Ton identifiant contient des caractères interdit.',$actual_link);
            $erreur = true;
		}
        if ( !longueurEntre($data['username'],4,32) ) {
            redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Ton identifiant doit comprendre entre 4-32 caractères.',$actual_link);
            $erreur = true;
	   }
        if ( !longueurEntre($data['password'],5,32) ) {
            redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Ton mot de passe doit comprendre entre 5-32 caractères.',$actual_link);
            $erreur = true;
		}
        if ( $data['password'] != $data['password-confirm'] ) {
            redirectWithMessage('soft-danger', '<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Tes mots de passe ne se correspondent pas.',$actual_link);
            $erreur = true;
		}
        if ( getUser($odb, $data['username']) != null ) {
            redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Un utilisateur utilise déjà ton identifiant.',$actual_link);
            $erreur = true;
		}

        if ( !$erreur ) {
            $query = $odb->prepare('INSERT INTO users VALUES (NULL, :username,:password,0,0,UNIX_TIMESTAMP(),0,0,0)');
            $query->execute([
                'username' => $data['username'],
                'password' => crypt($data['password'],'niqueletatniquelesbanquesjevousbz'),
            ]);
            $_SESSION['id'] = $odb->lastInsertId();
            $_SESSION['ip'] = getIp();
            redirectWithMessage('soft-primary d-flex align-items-center',' <i class="material-icons mr-3">flag</i><div class="text-body"><strong>Bienvenue</strong> parmis nous! Pour être au courant des dernières mise à jour et suivre l\'actualité de MesrineCC rejoint notre <a href="http://t.me/mesrinecc">Canal Telegram</a>. <small>(En cas de DOWN de notre site les instructions seront écrite sur notre telegram)</small></div>','dashboard');
        }
    }
    else {
        redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Des champs sont manquants. ',$actual_link);
    }
}

?><!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>MesrineCC - Rejoindre le cercle de Mesrine</title>
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
			
			<div class="form-group">
                <label class="text-label" for="password_2">confirmer le mot de passe:</label>
                <div class="input-group input-group-merge">
                    <input id="password_2" name="password-confirm" type="password" required="" class="form-control form-control-prepended" placeholder="Confirmer votre mot de passe">
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
                    <label class="custom-control-label" for="remember">J'accepte <a href="#"  data-toggle="modal" data-target="#modal-standard">les conditions d'utilisations</a></label>
                </div>
            </div>
			
			<input type="hidden" name="csrf" value="<?php echo $token ?>">
			
			<div class="form-group text-left">
			<a href="./login"><button class="btn btn-light mb-5" style="float:right;" type="button">J'ai déjà un compte</button></a>
			<button name="Reg" class="btn btn-primary mb-5"  style="float:left;" type="submit">Valider</button>
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
    <script src="assets/vendor/jquery.min.js"></script>

    <script src="assets/vendor/popper.min.js"></script>
    <script src="assets/vendor/bootstrap.min.js"></script>

    <!-- Perfect Scrollbar -->
    <script src="assets/vendor/perfect-scrollbar.min.js"></script>
    <script src="assets/vendor/material-design-kit.js"></script>
 <div id="modal-standard" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-standard-title">Conditions d'utilisation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="modal-body">
                    <p>Merci de bien lire nos règles avant de vous inscrire et utiliser notre site.<br><br>
					<ul>
					<li>Tous les <b>paiements</b> effectués sur notre site sont automatique.</li>
					<li><b>Un remboursement</b> de votre balance vers un wallet BTC externe est <b>impossible</b>.</li>
					<li>Notre shop se réserve le droit de <b>bloquer</b> l'accès à un utilisateur sans aucunes explications.</li>
									
					</ul>
					</p>
                </div> <!-- // END .modal-body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Fermer</button>
                </div> <!-- // END .modal-footer -->
            </div> <!-- // END .modal-content -->
        </div> <!-- // END .modal-dialog -->
    </div> <!-- // END .modal -->