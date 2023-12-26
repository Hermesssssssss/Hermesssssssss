<?php
$banPage = true;
$page = "Gérer les CC";
require '!#/init.php';
require '!#/header1.php';

$adminpage = true;
if($user["admin"] < "2") {
	redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue. (accès non autorisée)','dashboard');
    exit;
}

if ( checkArray($_POST,['base','data'])) {
    if ( checkIfEmptyArray($_POST,['base','data']) ) {
        $cards = explode("\r\n",$_POST['data']);
        $cptOk = 0;
		$cptIgnore = 0;
		$cptError = 0;
		
        foreach ($cards as $card) {
            try {
                $status = '';
				list($ccnum, $ccexp, $cccvc, $titu, $adresse, $cp, $ville, $numtel, $dob) = explode("|", $card);
                $errorReason = '';
                $queryCheckExistCc = getData($odb, 'cards',['ccnum' => $ccnum]);
                if ( count($queryCheckExistCc) > 0 ) {
                    $status = 'error';
                }
                if ( count(explode('/',$ccexp)) != 2 ) {
                    $status = 'error';
                }
                else {
                    $expM = explode('/',$ccexp)[0];
                    $expY = explode('/',$ccexp)[1];
                }
                $num = $ccnum;
				$base = $_POST["base"];
                $cvv = $cccvc;
                if ($status!='error' and  !isLuhnNum($num)) {
                    $status = 'error';
                }
                if ($status!='error' and  empty($num) or $num == '' or empty($expM) or $expM == '' or empty($expY) or $expY == '' or empty($cvv) or $cvv == '') {
                    $status = 'error';
                }

                $firstCc = substr($ccnum,0,1);
                if ($status!='error' and  $firstCc == '9' or $firstCc == '8' or $firstCc == '0' or $firstCc == '1' or $firstCc == '2' or $firstCc == '7' ) {
                    $status='error';
                }
                if ($status!='error' and  !is_numeric($num) and !is_numeric($expM) and !is_numeric($expY) and !is_numeric($cvv) ) {
                    $status = 'error';
					}
                if ($status!='error' and  $expM > 12 or $expM < 0 ) {
                    $status = 'error';
                }
                if ($status!='error' and  $expY < substr(date('Y'),-2)) {
                    $status = 'error';
                }
                $expM = sprintf('%02d',$expM);
                $expY = strlen($expY)!=2?substr($expY,-2):$expY;

                $dataApi = binchecker(substr($ccnum,0,6));
                if ($status!='error' and  $dataApi == null or count($dataApi) != 6 ) {
                    $status = 'error';
                    $priceQuery = $odb->query('SELECT * FROM cc_prix WHERE level = \'DEFAUT\'');
                    $price = $priceQuery->fetch()['prix'];
                }
                else {
                    $priceQuery = $odb->prepare('SELECT * FROM cc_prix WHERE level = :ccLevel');
                    $priceQuery->execute(['ccLevel'=>$dataApi[4]]);
                    if ( $priceQuery->rowCount() == 0 ) {
                        $priceQuery = $odb->query('SELECT * FROM cc_prix WHERE level = \'DEFAUT\'');
                    }
                    $price = $priceQuery->fetch()['prix'];
                }
				
                $queryInsertCC = $odb->prepare('INSERT INTO cards VALUE (NULL, ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
                $queryInsertCC->execute(array(isset($dataApi[5])?$dataApi[5]:'0',
				isset($dataApi[4])?$dataApi[4]:'0',
				isset($dataApi[3])?$dataApi[3]:'0',
				isset($dataApi[1])?$dataApi[1]:'',
				$ccnum, $ccexp, $cccvc, $titu, $adresse, $cp, $ville, $numtel, $dob,
				time(), "0", "0", "0", "0", "0", $price, $base, "0", "0"));
				
                
                if ( $status == 'error')
                    $cptError++;
				else
					$cptOk++;
					
            }
            catch (Exception $exception) {
                $cptIgnore++;
            }
        }

        redirectWithMessage('success',$cptOk . ' CC ADD | '.$cptError.' CC ERREUR | '. $cptIgnore . ' CC IGNORE',$actual_link);
    }
}

if ( checkArray($_POST,['new','newPrice']) ) {
    $data = protectArray($_POST);
    if ( checkIfEmptyArray($_POST,['new','newPrice']) ) {
        $querySearchExisting = getData($odb,'cc_prix',['level' => strtoupper($data['new'])]);
        if ( $data['newPrice'] < 0 ) $data['newPrice'] = -$data['newPrice'];
        if ( count($querySearchExisting) == 0 ) {
            $queryInsertNewPrice = $odb->prepare('INSERT INTO cc_prix VALUE (NULL,:ccLevel,:price)');
            $queryInsertNewPrice->execute([
                'ccLevel' => strtoupper($data['new']),
                'price' => $data['newPrice'],
            ]);
        }
    }
    foreach ($data as $key=>$val) {
        if ( $key != 'new' and $key != 'newPrice' ) {
            if ( $val < 0 ) $val = -$val;
            $queryUpdatePrice = $odb->prepare('UPDATE cc_prix SET prix = :price WHERE level = :ccLevel');
            $queryUpdatePrice->execute([
                'ccLevel' => $key,
                'price' => $val,
            ]);
        }
    }
    redirectWithMessage('success','Prix modifiés',$actual_link);
}

if ( checkArray($_POST,['deleteType']) and checkIfEmptyArray($_POST,['deleteType'])) {
    $query = $odb->prepare('DELETE FROM cc_prix WHERE level = :ccLevel');
    $query->execute(['ccLevel'=>$_POST['deleteType']]);
    redirectWithMessage('success','Prix supprimé',$actual_link);
}

$_GET = protectArray($_GET);
$prices = getData($odb, 'cc_prix',[]);

$curPageCC = isset($_GET['pageCC'])?$_GET['pageCC']:1;
$ccCount = getCount($odb,'cards',['status'=>'0']);
$histo = getData($odb, 'cards',['status'=>'0','page'=>$curPageCC,'itemPerPage'=>9],'id','DESC');

?>

 
                        <div class="row card-group-row">
                           
							
							 <div class="col-md-12">
                                <form action="" method="POST">
								<div class="card">
                            <div class="card-header card-header-large bg-white">
                                <h4 class="card-header__title">Ajouter des cartes bancaire<br>
								</h4>
                            </div>
                        
                                      
<form action="" method="POST">
					<div class="col-lg-8 card-form__body card-body">
					<div class="row">
					<div class="col-md-12">
                                    <div class="form-group">
                                        <label for="opass">Data CC</label>
                                        <textarea name="data" id="opass" type="text" class="form-control" placeholder="1 cc par ligne..."></textarea>
                                    </div>
									</div>
									
									</div>
									
									<div class="row">
					<div class="col-md-12">
                                    <div class="form-group">
                                        <label for="opass">Base</label>
                                        <input name="base" id="opass" type="text" class="form-control" placeholder="FRESHSPAMMED..">
                                    </div>
									</div>
									
									</div>
									
                                    <div class="form-group">
                 <button class="btn btn-primary"  type="submit">Ajouter</button></div>
                                </div>
			<input type="hidden" name="csrf" value="<?php echo $token ?>">

                                </div>
						
						</form>
						<div class="card">
                            <div class="card-header card-header-large bg-white">
                                <h4 class="card-header__title">Mettre à jour les prix<br>
								</h4>
                            </div>
                        
                                      
<form action="" method="POST">
					<div class="col-lg-8 card-form__body card-body">
					<input type="hidden" name="csrf" value="<?php echo $token ?>">

						 <?php foreach ($prices as $price) {  ?>
                                <div class="form-group row">
                                    <div class="col-5 text-right d-flex align-items-center justify-content-end">
                                        <span><?php echo $price['level'] ?></span>
                                    </div>
                                    <div class="col-5">
                                        <input type="number" class="form-control" name="<?php echo $price['level'] ?>" value="<?php echo $price['prix'] ?>">
                                    </div>
                                    <div class="col-2 d-flex align-items-center">
                                            <button name="deleteType" class="bg-transparent border-0" value="<?php echo $price['level'] ?>"><i class="text-danger fa fa-trash"></i></button>

                                    </div>
                                </div>
                            <?php } ?>
							<div class="form-group row">
                                <div class="col-5 text-right d-flex align-items-center justify-content-end">
                                    <input type="text" class="form-control" name="new">
                                </div>
                                <div class="col-5">
                                    <input type="number" class="form-control" name="newPrice">
                                </div>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">Enregistrer</button>
                            </div>
									
                                    

                                </div>
						
						</form>
						</div>
							
							
							

							 	<div class="card card-form">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Cartes en vente</h4>
                                        
										</div>
                              <div  class="table-responsive border-bottom" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                              
                                        <table class="table mb-0 table-striped thead-border-top-0">
                                            <thead>
                                                <tr>
                                                    
                                                    <th style="width: 10px;">ID</th>
                                                    <th style="width: 100px;">Bin</th>
                                                    <th style="width: 100px;">Level</th>
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

                                                    <td><small class="text-muted"><a href="./adm_card?id=<?php echo $log["id"]; ?>">#<?php echo $log["id"]; ?></a></small></td>
                                                    <td><small class="text-muted"><?php echo substr($log["ccnum"], 0, 6); ?></small></td>
                                                    <td><small class="text-muted"><?php echo $log["level"]; ?></small></td>
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