<?php
date_default_timezone_set('America/Los_Angeles');

$banPage = true;
$page = "Liste des utilisateurs";
require '!#/init.php';
require '!#/header1.php';

$adminpage = true;
if($user["admin"] < "1") {
	redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue. (accès non autorisée)','dashboard');
    exit;
}

$curPage = isset($_GET['page'])?$_GET['page']:1;
$_GET = protectArray($_GET);
$histo = getData($odb, 'users',$_GET,'id','DESC');
$usersCount = getCount($odb, 'users',$_GET,'id','DESC');
?>

 
                    
                        <div class="row card-group-row">
                           
							
							 <div class="col-md-12">
                           							 <button data-toggle="modal" data-target="#modal-standard"  class="btn mb-2 btn-primary"><i class="material-icons">filter_list</i> Filtrer la recherche</button>

                                
								<div class="card">
                            <div class="card-header card-header-large bg-white">
                                <h4 class="card-header__title">Liste des utilisateurs
								<span class="badge badge-pill badge-soft-primary"><?php echo $usersCount; ?></span>
								</h4>
                            </div>
                        
                    <div  class="table-responsive border-bottom" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                              
                                        <table class="table mb-0 table-striped thead-border-top-0">
                                            <thead>
                                                <tr>
                                                    
                                                    <th style="width: 10px;">ID</th>
                                                    <th style="width: 100px;">Identifiant</th>
                                                    <th style="width: 100px;">Status</th>
													<th style="width: 40px;">Balance</th>
													<th style="width: 40px;">Grade</th>
													<th style="width: 40px;">Inscription</th>
													<th style="width: 40px;">Connexion</th>
                                                     <th style="width: 44px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody class="list" id="staff02">
																						<?php foreach($histo as $log) { 
																						
																						switch($log["admin"]) {
																							case '0':
																							     $admi = 'secondary">Membre';
																								 break;
																							case '1':
																							     $admi = 'warning">Support';
																								 break;
																							case '2':
																							     $admi = 'danger">Admin';
																								 break;
																						}
																						
																						$deb = count(getDataBetweenDate($odb,'users',['id'=>$log["id"]],'last',strtotime('-15 minutes', time())));
																						
																						switch($deb) {
																							case '1':
																							     $online = '<span class="badge badge-pill badge-soft-success">En ligne</span>';
																								 break;
																							default:
																							     $online = '<span class="badge badge-pill badge-soft-secondary">Hors ligne</span>';
																								 break;
																						}
																							
																							
											?>
											<tr>

                                                    <td><small class="text-muted">#<?php echo $log["id"]; ?></small></td>
                                                    
                                                    <td><?php echo htmlspecialchars($log["username"]); ?></td>
													<td><?php echo $online; ?></td>
                                                    <td><span class="badge badge-soft-primary"><?php echo $log["balance"].'€'; ?></span></td>
                                                    <td><span class="badge badge-soft-<?php echo $admi; ?></span></td>
                                                    <td><small><?php echo date("d/m H:i", $log["register"]); ?></small></td>
                                                    <td><small><?php echo date("d/m H:i", $log["last"]); ?></small></td>
													
													<td><a href="./adm_user?id=<?php echo $log["id"]; ?>"><button type="button" class="btn btn-sm btn-primary">voir</button></a></td>
													
                                                     </tr>
												
			
											<?php } ?>
												</tbody>
                                        </table>
                                    </div>

                        </div>
							<?php echo generatePagination($curPage,ceil($usersCount/(isset($_GET['itemPerPage']) && is_numeric($_GET['itemPerPage'])?$_GET['itemPerPage']:10)),$actual_link) ?>
                   

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
                             <label class="mesrine">Identifiant</label>
                
                        <input type="text" name="username" class="form-control tx-color-03" placeholder="Identifiant de l'utilisateur recherché" value="<?php echo isset($_GET['username'])?$_GET['username']:'' ?>">
                  </div><!-- col -->
				  
			
                  </div><!-- col -->
				  
				  
				 
				  
				   <div class="form-row">
				   <div class="col-6 mb-3">
                             <label class="mesrine">Grade</label>
                     <select name="admin" class="form-control tx-color-03">
                        <option value="">Toutes</option>
                        <option value="0">Membre</option>
                        <option value="1">Support</option>
                        <option value="2">Admin</option>
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