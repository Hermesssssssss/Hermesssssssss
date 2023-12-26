<?php
$banPage = true;
$page = "Historique des outils";
require '!#/init.php';
require '!#/header1.php';

$adminpage = true;
if($user["admin"] < "2") {
	redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue. (accès non autorisée)','dashboard');
    exit;
}

if ( checkArray($_POST,['viderinc'])) {
	$data = protectArray($_POST);
	$query = $odb->query("TRUNCATE paypal_inc");
	addLog($odb, $user["id"], "Admin histo - Vidage de l'historique INC");
	redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Historique vidé avec succès.',$actual_link);	
}   

if ( checkArray($_POST,['vidersms'])) {
	$data = protectArray($_POST);
	$query = $odb->query("TRUNCATE sms_histo");
	addLog($odb, $user["id"], "Admin histo - Vidage de l'historique SMS");
	redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Historique vidé avec succès.',$actual_link);	
}   

$_GET = protectArray($_GET);

$curPageSMS = isset($_GET['pageSMS'])?$_GET['pageSMS']:1;
$curPageINC = isset($_GET['pageINC'])?$_GET['pageINC']:1;
$smsCount = getCount($odb,'sms_histo',[]);
$incCount = getCount($odb,'paypal_inc',[]);

$histodd = getData($odb, 'sms_histo',['page'=>$curPageSMS,'itemPerPage'=>9],'id','DESC');
$histoddd = getData($odb, 'paypal_inc',['page'=>$curPageINC,'itemPerPage'=>9],'id','DESC');

?>

 
                    
                        <div class="row card-group-row">
                           
						               
							
							 <div class="col-md-12">
							 <form action="" method="POST">
							 <button class="btn mb-2 btn-danger" type="submit" name="vidersms">Vider les SMS</button>
							 <button class="btn mb-2 btn-danger" type="submit" name="viderinc">Vider les PP-INC</button>
							 <input type="hidden" name="csrf" value="<?php echo $token ?>">
</form>
								
							

							 	<div class="card card-form">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Historique des outils</h4>
                                        
										</div>
                              <div class="card-header card-header-tabs-basic nav" role="tablist">
                                        <a href="#sms" class="active" data-toggle="tab" role="tab" aria-controls="sms" aria-selected="true">SMS</a>
                                        <a href="#inc" data-toggle="tab" role="tab" aria-controls="inc" aria-selected="false" class="">PP-INC</a>
                                      </div>
                                    <div class="list-group tab-content list-group-flush">
                                        <div class="tab-pane fade show active" id="sms">
                                    <div  class="table-responsive border-bottom" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                              
                                        <table class="table mb-0 table-striped thead-border-top-0">
                                            <thead>
                                                <tr>
                                                    
                                                    <th style="width: 10px;">ID</th>
                                                    <th style="width: 37px;">Utilisateur</th>
                                                    <th style="width: 100px;">Expéditeur</th>
                                                    <th style="width: 100px;">Destinataire</th>
                                                    <th>Message</th>
                                                    <th style="width: 37px;">Date</th>
                                                    <th style="width: 51px;">Prix </th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody class="list" id="staff02">
											<?php 
											foreach($histodd as $log) { 
													
											
											
										
											?>
											<tr>

                                                    <td><small class="text-muted">#<?php echo $log["id"]; ?></small></td>
                                                    <td><small class="text-muted"><a href="./adm_user?id=<?php echo $log["user"]; ?>"><?php echo getUserFromId($odb, $log["user"])["username"]; ?></a></small></td>
                                                    <td><small class="text-muted"><?php echo $log["expediteur"]; ?></small></td>
                                                    <td><small class="text-muted"><?php echo $log["destinataire"]; ?></small></td>
                                                    <td><small class="text-muted"><?php if($user["admin"] == "1") echo "caché"; else echo $log["msg"]; ?></small></td>
                                                    <td><small class="text-muted"><?php echo @date("d/m H:i:s", $log["date"]); ?></small></td>
                                                    
                                                    <td><?php echo $log["prix"]; ?>€</td>
                                                    
                                                </tr>
												
			
											<?php } ?>
												</tbody>
                                        </table>
										 <div class="ml-3 mt-3"><?php echo generatePagination($curPageSMS,ceil($smsCount/9),$actual_link,'pageSMS') ?>
		
                                    </div>
                                    </div>


                                </div>
								
								
									 <div class="tab-pane fade" id="inc">
                                    <div  class="table-responsive border-bottom" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                              
                                        <table class="table mb-0 table-striped thead-border-top-0">
                                            <thead>
                                                <tr>
                                                    
                                                    <th style="width: 10px;">ID</th>
                                                    <th style="width: 37px;">Utilisateur</th>
                                                    <th style="width: 100px;">Numéro</th>
                                                    <th style="width: 100px;">Status</th>
                                                    <th style="width: 40px;">Code</th>
                                                    <th style="width: 37px;">Date</th>
                                                    <th style="width: 51px;">Prix </th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody class="list" id="staff02">
											<?php 
											foreach($histoddd as $log) { 
											
											switch($log["status"]) {
												case '0':
												       $status = 'warning">EN COURS';
													   $code = '<div class="loader loader-sm"></div>';
													   break;
												case '1':
												       $status = 'success">BYPASS';
													   $code = '<small class="badge badge-dark">'.$log["code"].'</small>';
													   break;
												case '2':
												       $status = 'danger">EXPIRÉ';
													   $code = 'Refund';
													   break;
												default:
												       $status = 'warning">EN COURS';
													   $code = '<div class="loader loader-sm"></div>';
													   break;
											}
													
											
											
										
											?>
											<tr>

                                                    <td><small class="text-muted">#<?php echo $log["id"]; ?></small></td>
                                                    <td><small class="text-muted"><a href="./adm_user?id=<?php echo $log["user"]; ?>"><?php echo getUserFromId($odb, $log["user"])["username"]; ?></a></small></td>
													<td><small class="text-muted"><?php echo $log["target"]; ?></small></td>
                                                    <td><span class="badge badge-soft-<?php echo $status; ?></span></td>
                                                    <td><small class="text-muted"><?php echo $code; ?></small></td>
                                                    <td><small class="text-muted"><?php echo @date("d/m H:i:s", $log["date"]); ?></small></td>
                                                    
                                                    <td><?php echo $log["prix"]; ?>€</td>
                                                    
                                                </tr>
												
			
											<?php } ?>
												</tbody>
                                        </table>
										 <div class="ml-3 mt-3"><?php echo generatePagination($curPageINC,ceil($incCount/9),$actual_link,'pageINC') ?>
		
                                    </div>
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