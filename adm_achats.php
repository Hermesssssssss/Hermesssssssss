<?php
$banPage = true;
$page = "Historique des achats";
require '!#/init.php';
require '!#/header1.php';

$adminpage = true;
if($user["admin"] < "2") {
	redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue. (accès non autorisée)','dashboard');
    exit;
}


$_GET = protectArray($_GET);

$curPageCC = isset($_GET['pageCC'])?$_GET['pageCC']:1;
$curPagePP = isset($_GET['pagePP'])?$_GET['pagePP']:1;
$ccCount = getCount($odb,'cards',['status'=>'1']);
$ppCount = getCount($odb,'paypal',['status'=>'1']);

$histo = getData($odb, 'cards',['status'=>'1','page'=>$curPageCC,'itemPerPage'=>9],'dateAchat','DESC');
$histod = getData($odb, 'paypal',['status'=>'1','page'=>$curPagePP,'itemPerPage'=>9],'dateAchat','DESC');

?>

 
                    
                        <div class="row card-group-row">
                           
						               
							
							 <div class="col-md-12">
								
							

							 	<div class="card card-form">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Historique des achats</h4>
                                        
										</div>
                              <div class="card-header card-header-tabs-basic nav" role="tablist">
                                        <a href="#cc" class="active" data-toggle="tab" role="tab" aria-controls="cc" aria-selected="true">Achats CC</a>
                                        <a href="#pp" data-toggle="tab" role="tab" aria-controls="pp" aria-selected="false" class="">Achats PP </a>
                                      </div>
                                    <div class="list-group tab-content list-group-flush">
                                        <div class="tab-pane fade active show" id="cc">
                                    <div  class="table-responsive border-bottom" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                              
                                        <table class="table mb-0 table-striped thead-border-top-0">
                                            <thead>
                                                <tr>
                                                    
                                                    <th style="width: 10px;">ID</th>
                                                    <th style="width: 100px;">Utilisateur</th>
                                                    <th style="width: 100px;">Bin</th>
                                                    <th style="width: 100px;">Base</th>
                                                    <th style="width: 37px;">Date d'achat</th>
                                                    <th style="width: 37px;">Date checker</th>
                                                    <th style="width: 37px;">Status</th>
                                                    <th style="width: 37px;">Checker debug</th>
                                                    <th style="width: 51px;">Prix </th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody class="list" id="staff02">
											<?php 
											foreach($histo as $log) { 
											switch($log["checker"]) {
			case '0':
			    $chkr = 'soft-secondary">Non check';
				break;
		    case '1':
			    $chkr = 'soft-success">LIVE';
				break;
			case '2':
			    $chkr = 'soft-danger">DEAD (refund)';
				break;
			case '4':
			    $chkr = 'soft-primary">En cours de check';
				break;
		    default:
			    $chkr = 'soft-warning">Erreur';
				break;
		}
											
											
											
											
										
											?>
											<tr>

                                                    <td><small class="text-muted"><a href="./adm_card?id=<?php echo $log["id"]; ?>">#<?php echo $log["id"]; ?></a></small></td>
                                                    <td><small class="text-muted"><a href="./adm_user?id=<?php echo $log["user"]; ?>"><?php echo getUserFromId($odb, $log["user"])["username"]; ?></a></small></td>
                                                    <td><small class="text-muted"><?php echo substr($log["ccnum"], 0, 6); ?></small></td>
                                                    <td><span class="badge badge-soft-secondary">#<?php echo $log["base"]; ?></span></td>
                                                    <td><small class="text-muted"><?php echo date("d/m H:i:s", $log["dateAchat"]); ?></small></td>
                                                    <td><small class="text-muted"><?php echo @date("d/m H:i:s", $log["dateCheck"]); ?></small></td>
                                                    <td><span class="badge badge-<?php echo $chkr; ?></span></td>
                                                    <td><small class="text-muted"><?php echo $log["check_debug"]; ?></small></td>
                                                    
                                                    <td><?php echo $log["prix"]; ?>€</td>
                                                    
                                                </tr>
												
			
											<?php } ?>
												</tbody>
                                        </table>
										 <div class="ml-3 mt-3"><?php echo generatePagination($curPageCC,ceil($ccCount/9),$actual_link,'pageCC') ?>
		

                                         </div>
                                    </div></div>
									
									 <div class="tab-pane fade" id="pp">
                                    <div  class="table-responsive border-bottom" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                              
                                        <table class="table mb-0 table-striped thead-border-top-0">
                                            <thead>
                                                <tr>
                                                    
                                                    <th style="width: 10px;">ID</th>
                                                    <th style="width: 100px;">Utilisateur</th>
                                                    <th style="width: 100px;">Email</th>
                                                    <th style="width: 100px;">Base</th>
                                                    <th style="width: 37px;">Date d'achat</th>
                                                    <th style="width: 37px;">Date checker</th>
                                                    <th style="width: 37px;">Status</th>
                                                    <th style="width: 51px;">Prix </th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody class="list" id="staff02">
											<?php 
											foreach($histod as $log) { 
											switch($log["checker"]) {
			case '0':
			    $chkr = 'soft-secondary">Non check';
				break;
		    case '1':
			    $chkr = 'soft-success">LIVE';
				break;
			case '2':
			    $chkr = 'soft-danger">DEAD (refund)';
				break;
			
		    default:
			    $chkr = 'soft-warning">Erreur';
				break;
		}
											
											
											
											
										
											?>
											<tr>

                                                    <td><small class="text-muted"><a href="./adm_pp?id=<?php echo $log["id"]; ?>">#<?php echo $log["id"]; ?></a></small></td>
                                                    <td><small class="text-muted"><a href="./adm_user?id=<?php echo $log["user"]; ?>"><?php echo getUserFromId($odb, $log["user"])["username"]; ?></a></small></td>
                                                    <td><small class="text-muted"><?php echo $log["email"]; ?></small></td>
                                                    <td><span class="badge badge-soft-secondary">#<?php echo $log["base"]; ?></span></td>
                                                    <td><small class="text-muted"><?php echo date("d/m H:i:s", $log["dateAchat"]); ?></small></td>
                                                    <td><small class="text-muted"><?php echo @date("d/m H:i:s", $log["dateCheck"]); ?></small></td>
                                                    <td><span class="badge badge-<?php echo $chkr; ?></span></td>
                                                    
                                                    <td><?php echo $log["prix"]; ?>€</td>
                                                    
                                                </tr>
												
			
											<?php } ?>
												</tbody>
                                        </table>
										 <div class="ml-3 mt-3"><?php echo generatePagination($curPagePP,ceil($ppCount/9),$actual_link,'pagePP') ?>
		
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