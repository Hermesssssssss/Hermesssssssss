<?php
$banPage = true;
$page = "Gérer les news";
require '!#/init.php';
require '!#/header1.php';

$adminpage = true;
if($user["admin"] < "2") {
	redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue. (accès non autorisée)','dashboard');
    exit;
}

if ( checkArray($_POST,['titre'],['msg'])) {
    if ( checkIfEmptyArray($_POST,['titre'],['msg'])) {
        $data = protectArray($_POST);
				if ($user["admin"] == "2") {
					addLog($odb, $user["id"], "News - Publication (Titre: ".$data["titre"].")");
                    
                    $query = $odb->prepare("INSERT INTO news VALUES (NULL, ?, ?, UNIX_TIMESTAMP(), ?)");
					$query->execute(array($data['titre'], $data['msg'], $user['id']));
					redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, News publiée avec succès.',$actual_link);
                }
                else {
                    redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur interne est survenue.',$actual_link);
                }
    }
    else {
        redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue.',$actual_link);
    }
}


if ( checkArray($_POST,['del'],['idnews'])) {
    if ( checkIfEmptyArray($_POST,['idnews'])) {
        $data = protectArray($_POST);
				if ($user["admin"] == "2") {
					addLog($odb, $user["id"], "News - Suppression");
                    
                    $query = $odb->prepare("DELETE FROM news WHERE id = ?");
					$query->execute(array(intval($data['idnews'])));
					redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, News supprimée avec succès.',$actual_link);
                }
                else {
                    redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur interne est survenue.',$actual_link);
                }
    }
    else {
        redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue.',$actual_link);
    }
}

$histo = $odb->query('SELECT * FROM news ORDER by id DESC LIMIT 10');
?>

 
                    
                        <div class="row card-group-row">
                           
							
							 <div class="col-md-6">
                                
								<div class="card">
                            <div class="card-header card-header-large bg-white">
                                <h4 class="card-header__title">Déposer une news
								</h4>
                            </div>
                        
                                      
<form action="" method="POST">
					<div class="col-lg-8 card-form__body card-body">
                                    <div class="form-group">
                                        <label for="opass">Titre</label>
                                        <input style="width: 100%;" name="titre" id="opass" type="text" class="form-control" placeholder="Restock" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="npass">Message</label>
                                        <input style="width: 100%;" name="msg" id="npass" type="text" class="form-control" placeholder="Restock +1000CC ...." required">
                                    </div>
                                    <div class="form-group">
                                       <button class="btn btn-primary" type="submit">Publier</button></div>
                                </div>
			<input type="hidden" name="csrf" value="<?php echo $token ?>">

                                </div>
						
						</form>
							

                        </div>
						
						 <div class="col-md-6">
                                
								<div class="card">
                            <div class="card-header card-header-large bg-white">
                                <h4 class="card-header__title">Les news en ligne
								</h4>
                            </div>
                        
                    <div  class="table-responsive border-bottom" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                              
                                        <table class="table mb-0 table-striped thead-border-top-0">
                                            <thead>
                                                <tr>
                                                    
                                                    <th style="width: 37px;">Date</th>
                                                    <th style="width: 100px;">Titre</th>
													<th style="width: 40px;">Admin</th>
                                                     <th style="width: 44px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody class="list" id="staff02">
																						<?php foreach($histo as $log) { 
											?>
											<tr>

                                                    <td><small class="text-muted"><?php echo date("d/m H:i", $log["date"]); ?></small></td>
                                                    
                                                    <td><?php echo htmlspecialchars($log["sujet"]); ?></td>
                                                    <td><span class="badge badge-primary"><?php echo getUserFromId($odb, $log["admin"])["username"]; ?></span></td>
													<form action="" method="POST">
													<input type="hidden" name="idnews" value="<?php echo $log["id"]; ?>">
													<input type="hidden" name="csrf" value="<?php echo $token ?>">
													<td><button  type="sublit" name="del" class="btn btn-sm btn-danger">Supprimer</button></td>
													</form>
                                                     </tr>
												
			
											<?php } ?>
												</tbody>
                                        </table>
                                    </div>

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