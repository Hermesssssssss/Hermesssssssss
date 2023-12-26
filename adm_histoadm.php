<?php
$banPage = true;
$page = "Historique des admins";
require '!#/init.php';
require '!#/header1.php';

$adminpage = true;
if($user["admin"] < "2") {
	redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue. (accès non autorisée)','dashboard');
    exit;
}
if ( checkArray($_POST,['vider'])) {
	$data = protectArray($_POST);
	$query = $odb->query("TRUNCATE admins_histo");
	addLog($odb, $user["id"], "Admin histo - Vidage de l'historique");
	redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Historique vidé avec succès.',$actual_link);	
}     

$curPage = isset($_GET['page'])?$_GET['page']:1;
$_GET = protectArray($_GET);
$histo = getData($odb, 'admins_histo',$_GET,'id','DESC');
$usersCount = getCount($odb, 'admins_histo',$_GET,'id','DESC');
?>

 
                    
                        <div class="row card-group-row">
                           
							
							 <div class="col-md-12">
                           							 <button data-toggle="modal" data-target="#modal-standard"  class="btn mb-2 btn-primary"><i class="material-icons">filter_list</i> Filtrer la recherche</button>

                                <form action="" method="POST">
								<div class="card">
                            <div class="card-header card-header-large bg-white">
							<button name="vider" type="submit" style="float:right" class="btn btn-sm btn-danger">vider l'historique</button>
                                <h4 class="card-header__title">Historique des actions 
								<span class="badge badge-pill badge-soft-primary"><?php echo $usersCount; ?></span>
								</h4>
                            </div>
                        
										<input type="hidden" name="csrf" value="<?php echo $token ?>">
										</form>
                              
                    <div  class="table-responsive border-bottom" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                              
                                        <table class="table mb-0 table-striped thead-border-top-0">
                                            <thead>
                                                <tr>
                                                    
                                                    <th style="width: 10px;">ID</th>
                                                    <th style="width: 100px;">Admin</th>
                                                    <th style="width: 100px;">Grade</th>
                                                    <th>Action</th>
													<th style="width: 40px;">Date</th>
													
                                                </tr>
                                            </thead>
                                            <tbody class="list" id="staff02">
																						<?php foreach($histo as $log) { 
																						
																						switch(getUserFromId($odb, $log["user"])["admin"]) {
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
																						
																						
																							
											?>
											<tr>

                                                    <td><small class="text-muted">#<?php echo $log["id"]; ?></small></td>
                                                    
                                                    <td><a href="./adm_user?id=<?php echo $log["user"]; ?>"><?php echo getUserFromId($odb, $log["user"])["username"]; ?></a></td>
													<td><?php echo $log["action"]; ?></td>
                                                    <td><span class="badge badge-soft-<?php echo $admi; ?></span></td>
                                                    <td><small><?php echo date("d/m H:i:s", $log["date"]); ?></small></td>
													
													
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
				 
                        <div class="col-6 mb-3">
                             <label class="mesrine">Action</label>
                
                        <input type="text" name="action" class="form-control tx-color-03" placeholder="Action recherché" value="<?php echo isset($_GET['action'])?$_GET['action']:'' ?>">
                  </div><!-- col -->
				  
				  <div class="col-6 mb-3">
                             <label class="mesrine">Admin (user #id)</label>
                
                        <input type="text" name="user" class="form-control tx-color-03" placeholder="Admin ID" value="<?php echo isset($_GET['user'])?$_GET['user']:'' ?>">
                  </div><!-- col -->
				  
			
				  
				
					 
                        <div class="col-6 mb-3">
                             <label class="mesrine">Actions par page</label>
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