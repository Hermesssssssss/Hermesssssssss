<?php
$banPage = true;
$page = "Configuration générale";
require '!#/init.php';
require '!#/header1.php';

$adminpage = true;
if($user["admin"] < "2") {
	redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue. (accès non autorisée)','dashboard');
    exit;
}

if ( isset($_POST) and !empty($_POST)) {
    foreach ($_POST as $key=>$value) {
        $query=$odb->prepare('UPDATE config SET value = :value WHERE name = :configName');
        $query->execute(['value'=>$value,'configName'=>$key]);
    }
    redirectWithMessage('soft-success','Modification effectuée.',$actual_link);
}

$configs = $odb->query('SELECT * FROM config WHERE name != \'blockio_api\'');

?>

 
                    
                        <div class="row card-group-row">
                           
							
							 <div class="col-md-12">
                                
								<div class="card">
                            <div class="card-header card-header-large bg-white">
                                <h4 class="card-header__title">Modifier la configuration
								</h4>
                            </div>
                        
                                      
<form action="" method="POST">
					<div class="col-lg-8 card-form__body card-body">
                                  <?php foreach ($configs as $conf) { ?>
                                        <div class="form-group">
                                            <label for="<?php echo $conf['name'] ?>"><?php echo $conf['name'] ?></label>
                                            <input class="form-control" type="text" name="<?php echo $conf['name'] ?>" value="<?php echo $conf['value'] ?>">
                                        </div>
                                <?php } ?> <div class="form-group">
			<input type="hidden" name="csrf" value="<?php echo $token ?>">
<button type="submit" class="btn btn-primary">Modifier</button>
                                </div>
								  </div>
						
						</form>
							

                        </div>
						
					
							
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