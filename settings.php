<?php
$banPage = true;
$page = "Modifier mon mot de passe";
require '!#/init.php';
require '!#/header1.php';

if ( checkArray($_POST,['opass','npass','cpass'])) {
    if ( checkIfEmptyArray($_POST,['opass','npass','cpass']) ) {
        $data = protectArray($_POST);
        if ( hash_equals($user['password'],crypt($data['opass'],$user['password']))) {
            if ( $data['npass'] == $data['cpass']) {
                if ( longueurEntre($data['npass'],4,32) ) {
                    $query = $odb->prepare('UPDATE users SET password = :password WHERE id = :idUser');
                    $query->execute([
                        'password' => crypt($data['npass'],'niqueletatniquelesbanquesjevousbz'),
                        'idUser' => $_SESSION['id']
                    ]);
                    redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Tu vient de modifié ton mot de passe.',$actual_link);
                }
                else {
                    redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, La longueur de ton mot de passe doit contenir 4-30 caractères.',$actual_link);
                }
            }
            else {
                redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Tes nouveaux mots de passe ne se correspondent pas.',$actual_link);
            }
        }
        else {
            redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Ton mot de passe actuel est invalide.',$actual_link);
        }
    }
    else {
        redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue.',$actual_link);
    }
}
?>

 
                    
                        <div class="row card-group-row">
                           
							
							 <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Modifier mon mot de passe <br>
										<small class="text-muted">Si vous perdez votre mot de passe, vous perdrez l'accès a votre compte (pas de reset possible).</small></h4>
                                        
										</div>
										
                                      
<form action="" method="POST">
					<div class="col-lg-8 card-form__body card-body">
                                    <div class="form-group">
                                        <label for="opass">Ancien mot de passe</label>
                                        <input style="width: 270px;" name="opass" id="opass" type="password" class="form-control" placeholder="******" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="npass">Nouveau mot de passe</label>
                                        <input style="width: 270px;" name="npass" id="npass" type="password" class="form-control" placeholder="**********" required">
                                    </div>
                                    <div class="form-group">
                                        <label for="cpass">Confirmer le mot de passe</label>
                                        <input style="width: 270px;" name="cpass" id="cpass" type="password" class="form-control" placeholder="*********" required>
                                    </div>
                                </div>
			<input type="hidden" name="csrf" value="<?php echo $token ?>">

                                </div>
						
						<div class="text-right mb-5">
                            <button type="submit" name="update" class="btn btn-success">Modifier</button>
                        </div>
						</form>
							

                                </div>
                            </div>
							</div>
							</div>
							</div>
                <!-- // END drawer-layout__content -->
<?php require "!#/header2.php"; ?>

        </div>
        <!-- // END header-layout__content -->

    </div>
    <!-- // END header-layout -->

  

<?php require "!#/jsinclude.php"; ?>


</body>

</html>