<?php
$banPage = true;
$page = "Historique des dépôts";
require '!#/init.php';
require '!#/header1.php';

$adminpage = true;
if($user["admin"] < "2") {
	redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue. (accès non autorisée)','dashboard');
    exit;
}
$curPageBTC = isset($_GET['pageBTC'])?$_GET['pageBTC']:1;

$histodddd = getData($odb, 'DepotBTC',$_GET,'id','DESC');
$btcCount = getCount($odb,'DepotBTC',[]);
?>

 
                    
                        <div class="row card-group-row">
                           
							
							 <div class="col-md-6">
                                 <button data-toggle="modal" data-target="#modal-standard"  class="btn mb-2 btn-primary"><i class="material-icons">filter_list</i> Filtrer la recherche</button>

                                
								<div class="card">
                            <div class="card-header card-header-large bg-white">
                                <h4 class="card-header__title">Historique des dépôts
								</h4>
                            </div>
                        
                    <div  class="table-responsive border-bottom" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                                        <table class="table mb-0 table-striped thead-border-top-0">
                                          <thead>
                                                <tr>
                                                    
                                                    <th style="width: 10px;">ID</th>
                                                    <th style="width: 40px;">Utilisateur</th>
                                                    <th style="width: 100px;">Adresse BTC</th>
                                                    <th style="width: 100px;">Status</th>
                                                    <th style="width: 37px;">Date</th>
                                                    <th style="width: 37px;">Montant</th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody class="list" id="staff02">
											<?php 
											foreach($histodddd as $log) { 
															switch($log["status"]) {
	case '0':
	      $status = 'secondary">En attente de paiement';
		  break;
	case '1':
	      $status = 'warning">En attente de confirmation (0/1)';
		  break;
	case '2':
	      $status = 'success">Credité';
		  break;
	case '3':
	      $status = 'danger">Expiré';
		  break;
	default:
	      $status = 'danger">Erreur';
		  break;
}
											
										
											?>
											<tr>

                                                    <td><small class="text-muted">#<?php echo $log["id"]; ?></small></td>
                                                    <td><small class="text-muted"><?php echo getUserFromId($odb, $log["user"])["username"]; ?></small></td>
                                                    <td><small class="text-muted"><a href="<?php echo $log["txid"]; ?>"><?php echo $log["address"]; ?></a></small></td>
                                                    <td><span class="badge badge-soft-<?php echo $status; ?></span></td>
                                                    <td><small class="text-muted"><?php echo @date("d/m H:i:s", $log["date"]); ?></small></td>
                                                    
                                                    <td><?php echo $log["amount"]; ?>€</td>
                                                    
                                                </tr>
												
			
											<?php } ?>
												</tbody>
                                        </table>
										 <div class="ml-3 mt-3"><?php echo generatePagination($curPageBTC,ceil($btcCount/9),$actual_link,'pageBTC') ?>
		

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


<form action="" method="GET">
<div id="modal-standard" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-standard-title">Filtre de recherche</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="col-lg-12 card-form_body card-body">
                 <div class="form-row">
				 
                        <div class="col-12 mb-3">
                             <label class="mesrine">Wallet</label>
                
                        <input type="text" name="address" class="form-control tx-color-03" placeholder="Wallet de depot" value="<?php echo isset($_GET['address'])?$_GET['address']:'' ?>">
                  </div><!-- col -->
				  
			
                  </div><!-- col -->
				  
				  
				 
				  
				   <div class="form-row">
				   <div class="col-6 mb-3">
                             <label class="mesrine">Status</label>
                     <select name="status" class="form-control tx-color-03">
                        <option value="">Tous</option>
                        <option value="0">En attente de paiement</option>
                        <option value="1">En attente d'une confirmation</option>
                        <option value="2">Crédité</option>
                        <option value="3">Expiré</option>
                        </select>
					 </div>
					 
					 
                        <div class="col-6 mb-3">
                             <label class="mesrine">Utilisateurs par page</label>
							 <select name="itemPerPage" class="form-control tx-color-03">
                        <?php $arrayIPP = [
                            '10' => '10',
                            '25' => '25',
                            '50' => '50',
                            '75' => '75',
                            '100' => '100'
                        ];
                        foreach ($arrayIPP as $n ) {
                            echo '<option value="'.$n.'" '.(isset($_GET['itemPerPage'])&&$_GET['itemPerPage']==$n?'selected':'').'>'.$n.'</option>';
                        }
                        ?>
                    </select>
							 </div>
							 </div>
				  
				  
				  
				  
					
                </div> <!-- // END .modal-body -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Appliquer les filtres</button>
                </div> <!-- // END .modal-footer --></form>
            </div> <!-- // END .modal-content -->
        </div> <!-- // END .modal-dialog -->
    </div> <!-- // END .modal -->
</body>

</html>