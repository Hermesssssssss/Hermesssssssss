<?php
$banPage = true;
$page = "Gérer les feedbacks";
require '!#/init.php';
require '!#/header1.php';

$adminpage = true;
if($user["admin"] < "1") {
	redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue. (accès non autorisée)','dashboard');
    exit;
}


$curPage = isset($_GET['page'])?$_GET['page']:1;
$feedsCount = getCount($odb,'feedbacks',['status'=>'1']);


if ( checkArray($_POST,['ref'],['idfeed'])) {
    if ( checkIfEmptyArray($_POST,['idfeed'])) {
        $data = protectArray($_POST);
				if ($user["admin"] >= "1") {
					addLog($odb, $user["id"], "Feedback - Refus");
                   
                    $query = $odb->prepare("DELETE FROM feedbacks WHERE id = ?");
					$query->execute(array(intval($data['idfeed'])));
					redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Feedback refusé avec succès.',$actual_link);
                }
                else {
                    redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur interne est survenue.',$actual_link);
                }
    }
    else {
        redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue.',$actual_link);
    }
}

if ( checkArray($_POST,['val'],['idfeed'])) {
    if ( checkIfEmptyArray($_POST,['idfeed'])) {
        $data = protectArray($_POST);
				if ($user["admin"] >= "1") {
					addLog($odb, $user["id"], "Feedback - Validation (#ID-FEED: ".$data["idfeed"].")");
                    $query = $odb->prepare("UPDATE feedbacks SET status = ? WHERE id = ?");
					$query->execute(array("1", intval($data['idfeed'])));
					redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Feedback accepté avec succès.',$actual_link);
                }
                else {
                    redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur interne est survenue.',$actual_link);
                }
    }
    else {
        redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue.',$actual_link);
    }
}


$histod = getData($odb, 'feedbacks',['status'=>'1','page'=>$curPage,'itemPerPage'=>9],'id','DESC');
$histo = $odb->query('SELECT * FROM feedbacks WHERE status = 0 ORDER by id');
?>

 
                    
                        <div class="row card-group-row">
                           
							
							 <div class="col-md-12">
                              <div class="card">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Historique des feedbacks</h4>

                                    </div>
                                    <div class="card-header card-header-tabs-basic nav" role="tablist">
                                        <a href="#activity_all" class="active" data-toggle="tab" role="tab" aria-controls="activity_all" aria-selected="true">En attente <span class="badge badge-pill badge-soft-primary"><?php echo getCount($odb, 'feedbacks',['status'=>'0']); ?></span></a>
                                        <a href="#activity_purchases" data-toggle="tab" role="tab" aria-controls="activity_purchases" aria-selected="false" class="">Acceptés <span class="badge badge-pill badge-soft-primary"><?php echo getCount($odb, 'feedbacks',['status'=>'1']); ?></span></a>
                                    </div>
                                    <div class="list-group tab-content list-group-flush">
                                        <div class="tab-pane fade active show" id="activity_all">


                                           <?php foreach($histo as $log) { ?>
										   <form action="" method="POST">
										   <div class="list-group-item list-group-item-action d-flex align-items-center ">
                                               <div class="flex">
                                                    <div class="d-flex align-items-middle">
                                                       <strong class="text-15pt mr-1"><a href="./adm_user?id=<?php echo $log["user"]; ?>"><?php echo getUserFromId($odb, $log["user"])["username"]; ?></a>
													   <span class="badge ml-2 badge-soft-secondary"><?php echo date("d/m à H:i", $log["date"]); ?></span></strong>
                                                    </div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($log["feedback"]); ?></small>
                                                </div>
												<input type="hidden" name="idfeed" value="<?php echo $log["id"]; ?>">
												<input type="hidden" name="csrf" value="<?php echo $token ?>">
                              
												<button name="val" type="submit" class="btn btn-sm btn-success">Valider</button>
                                                <button name="ref" type="submit" class="btn btn-sm ml-2 btn-danger">Refuser</button>
                                            </div>
											</form>
										   <?php } ?>


                                        </div>
                                        <div class="tab-pane" id="activity_purchases">

                                            <?php foreach($histod as $log) { ?>
										   <form action="" method="POST">
										   <div class="list-group-item list-group-item-action d-flex align-items-center ">
                                               <div class="flex">
											   <div class="d-flex align-items-middle">
                                                       <strong class="text-15pt mr-1"><a href="./adm_user?id=<?php echo $log["user"]; ?>"><?php echo getUserFromId($odb, $log["user"])["username"]; ?></a>
													   <span class="badge ml-2 badge-soft-secondary"><?php echo date("d/m à H:i", $log["date"]); ?></span></strong>
                                                    </div>
                                                    <small class="text-muted">(#<?php echo $log["id"]; ?>) <?php echo htmlspecialchars($log["feedback"]); ?></small>
                                                </div>
												<input type="hidden" name="idfeed" value="<?php echo $log["id"]; ?>">
												<input type="hidden" name="csrf" value="<?php echo $token ?>">
                              
                                                <button name="ref" type="submit" class="btn btn-sm ml-2 btn-danger">Supprimer</button>
                                            </div>
											</form>
										   <?php } ?>
										   
										   <div class="ml-3"><?php echo generatePagination($curPage,ceil($feedsCount/9),$actual_link,'page') ?>
		

                                         
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