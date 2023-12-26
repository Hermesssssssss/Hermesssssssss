<?php
$banPage = true;
$page = "Gérer les PayPal";
require '!#/init.php';
require '!#/header1.php';

$adminpage = true;
if($user["admin"] < "2") {
	redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue. (accès non autorisée)','dashboard');
    exit;
}

if ( checkArray($_POST,['base','data','prix'])) {
    if ( checkIfEmptyArray($_POST,['base','data','prix']) ) {
        $cards = explode("\r\n",$_POST['data']);
        $cptOk = 0;
		$cptIgnore = 0;
		$cptError = 0;
		
        foreach ($cards as $card) {
            try {
                $status = '';
				list($email, $pass, $titu, $adresse, $cp, $ville, $numtel, $dob) = explode("|", $card);
                $errorReason = '';
                $queryCheckExistCc = getData($odb, 'paypal',['email' => $email]);
                if ( count($queryCheckExistCc) > 0 ) {
                    $status = 'error';
                }
                
				$base = $_POST["base"];
                if ($status!='error' and  empty($email) or $email == '' or empty($pass) or $pass == '') {
                    $status = 'error';
                }
				
				if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$status = 'error';
				}
				$price = $_POST["prix"];
				$pays = $_POST["pays"];
				
				
				if($status == '') {
                $queryInsertCC = $odb->prepare('INSERT INTO paypal VALUE (NULL,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
                $queryInsertCC->execute(array($email, $pass, $titu, $adresse, $cp, $ville,$pays, $numtel, $dob,
				time(), "0", "0", "0", "0", $price, $base, "0", "0"));
				}
                
                if ( $status == 'error')
                    $cptError++;
				else
					$cptOk++;
					
            }
            catch (Exception $exception) {
                $cptIgnore++;
            }
        }

        redirectWithMessage('success',$cptOk . ' PP ADD | '.$cptError.' PP ERREUR | '. $cptIgnore . ' PP IGNORE',$actual_link);
    }
}


$_GET = protectArray($_GET);

$curPageCC = isset($_GET['pageCC'])?$_GET['pageCC']:1;
$ccCount = getCount($odb,'paypal',['status'=>'0']);
$histo = getData($odb, 'paypal',['status'=>'0','page'=>$curPageCC,'itemPerPage'=>9],'id','DESC');

?>

 
                        <div class="row card-group-row">
                           
							
							 <div class="col-md-12">
                                <form action="" method="POST">
								<div class="card">
                            <div class="card-header card-header-large bg-white">
                                <h4 class="card-header__title">Ajouter des comptes<br>
								</h4>
                            </div>
                        
                                      
<form action="" method="POST">
					<div class="col-lg-8 card-form__body card-body">
					<div class="row">
					<div class="col-md-12">
                                    <div class="form-group">
                                        <label for="opass">Data PP</label>
                                        <textarea name="data" id="opass" type="text" class="form-control" placeholder="1 log par ligne..."></textarea>
                                    </div>
									</div>
									
									</div>
									
									<div class="row">
					<div class="col-md-6">
                                    <div class="form-group">
                                        <label for="opass">Base</label>
                                        <input name="base" id="opass" type="text" class="form-control" placeholder="FRESHSPAMMED..">
                                    </div>
									</div>
									
									<div class="col-md-6">
                                    <div class="form-group">
                                        <label for="opass">Prix</label>
                                        <input name="prix" id="opass" type="text" class="form-control" placeholder="5">
                                    </div>
									</div>
									
									</div>
									<div class="row">
					<div class="col-md-12">
                                    <div class="form-group">
                                        <label for="opass">Pays</label>
                                        <input name="pays" id="opass" type="text" class="form-control" placeholder="Pays des Logs">
                                    </div>
									</div>
									</div>
					
									
                                    <div class="form-group">
                 <button class="btn btn-primary"  type="submit">Ajouter</button></div>
                                </div>
			<input type="hidden" name="csrf" value="<?php echo $token ?>">

                                </div>
						
						</form>
						
							 	<div class="card card-form">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Comptes en vente</h4>
                                        
										</div>
                              <div  class="table-responsive border-bottom" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                              
                                        <table class="table mb-0 table-striped thead-border-top-0">
                                            <thead>
                                                <tr>
                                                    
                                                    <th style="width: 10px;">ID</th>
                                                    <th style="width: 100px;">Email</th>
                                                    <th style="width: 100px;">Base</th>
                                                    <th style="width: 37px;">Date d'ajout</th>
                                                    <th style="width: 51px;">Prix </th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody class="list" id="staff02">
											<?php 
											foreach($histo as $log) { 
																
											
											
										
											?>
											<tr>

                                                    <td><small class="text-muted"><a href="./adm_pp?id=<?php echo $log["id"]; ?>">#<?php echo $log["id"]; ?></a></small></td>
                                                    <td><small class="text-muted"><?php echo $log["email"]; ?></small></td>
                                                    <td><span class="badge badge-soft-secondary">#<?php echo $log["base"]; ?></span></td>
                                                    <td><small class="text-muted"><?php echo @date("d/m H:i:s", $log["date"]); ?></small></td>
                                                    
                                                    <td><?php echo $log["prix"]; ?>€</td>
                                                    
                                                </tr>
												
			
											<?php } ?>
												</tbody>
                                        </table>
										 <div class="ml-3 mt-3"><?php echo generatePagination($curPageCC,ceil($ccCount/9),$actual_link,'pageCC') ?>
		

                                         </div>
                                    </div></div>

                        </div>
						
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