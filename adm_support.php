<?php
$banPage = true;
$page = "Support tickets";
require '!#/init.php';
require '!#/header1.php';

$adminpage = true;
if($user["admin"] < "1") {
	redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue. (accès non autorisée)','dashboard');
    exit;
}


$curPage = isset($_GET['page'])?$_GET['page']:1;
$feedsCount = getCount($odb,'tickets',['last'=>'1']);
$feedsCloseCount = getCount($odb,'tickets',['close'=>'1']);


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



$histod = getData($odb, 'tickets',['last'=>'1','close'=>'0','page'=>$curPage,'itemPerPage'=>9],'id','DESC');
$histodClose = getData($odb, 'tickets',['close'=>'1','page'=>$curPage,'itemPerPage'=>9],'id','DESC');
$histo = $odb->query('SELECT * FROM tickets WHERE close = 0 AND last = 0 ORDER by id DESC');
?>

 
                    
                        <div class="row card-group-row">
                           
							
							 <div class="col-md-12">
                              <div class="card">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Tickets</h4>

                                    </div>
                                    <div class="card-header card-header-tabs-basic nav" role="tablist">
                                        <a href="#activity_all" class="active" data-toggle="tab" role="tab" aria-controls="activity_all" aria-selected="true">En attente <span class="badge badge-pill badge-soft-primary"><?php echo getCount($odb, 'tickets',['close'=>'0', 'last'=>'0']); ?></span></a>
                                        <a href="#activity_purchases" data-toggle="tab" role="tab" aria-controls="activity_purchases" aria-selected="false" class="">Répondu <span class="badge badge-pill badge-soft-primary"><?php echo getCount($odb, 'tickets',['last'=>'1', 'close'=>'0']); ?></span></a>
                                        <a href="#activity_purchases2" data-toggle="tab" role="tab" aria-controls="activity_purchases2" aria-selected="false" class="">Clôturé <span class="badge badge-pill badge-soft-primary"><?php echo getCount($odb, 'tickets',['close'=>'1']); ?></span></a>
                                    </div>
                                    <div class="list-group tab-content list-group-flush">
                                        <div class="tab-pane fade active show" id="activity_all">


                                           <?php foreach($histo as $log) { ?>
										   <div class="list-group-item list-group-item-action d-flex align-items-center ">
                                               <div class="flex">
                                                    <div class="d-flex align-items-middle">
                                                       <strong class="text-15pt mr-1"><a href="./adm_user?id=<?php echo $log["user"]; ?>"><?php echo getUserFromId($odb, $log["user"])["username"]; ?></a>
													   <span class="badge ml-2 badge-soft-primary">Ticket ID #<?php echo $log["id"]; ?></span>
													   <span class="badge ml-2 badge-soft-secondary"><?php echo date("d/m à H:i", $log["date"]); ?></span></strong>
                                                    </div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($log["sujet"]); ?></small>
                                                </div>
                                                <a href="./adm_ticket?id=<?php echo $log["id"]; ?>"><button name="ref" type="button" class="btn btn-sm btn-primary">voir</button></a>
                                            </div>
										   <?php } ?>


                                        </div>
                                        <div class="tab-pane" id="activity_purchases">

                                            <?php foreach($histod  as $log) { ?>
										   <div class="list-group-item list-group-item-action d-flex align-items-center ">
                                               <div class="flex">
                                                    <div class="d-flex align-items-middle">
                                                       <strong class="text-15pt mr-1"><a href="./adm_user?id=<?php echo $log["user"]; ?>"><?php echo getUserFromId($odb, $log["user"])["username"]; ?></a>
													   <span class="badge ml-2 badge-soft-primary">Ticket ID #<?php echo $log["id"]; ?></span>
													   <span class="badge ml-2 badge-soft-secondary"><?php echo date("d/m à H:i", $log["date"]); ?></span></strong>
                                                    </div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($log["sujet"]); ?></small>
                                                </div>
                                                <a href="./adm_ticket?id=<?php echo $log["id"]; ?>"><button name="ref" type="button" class="btn btn-sm btn-primary">voir</button></a>
                                            </div>
										   <?php } ?>
										   
										   <div class="ml-3 mt-3"><?php echo generatePagination($curPage,ceil($feedsCount/9),$actual_link,'page') ?>
		

                                         
                                    </div>
                                </div>
								
								<div class="tab-pane" id="activity_purchases2">

                                            <?php foreach($histodClose  as $log) { ?>
										   <div class="list-group-item list-group-item-action d-flex align-items-center ">
                                               <div class="flex">
                                                    <div class="d-flex align-items-middle">
                                                       <strong class="text-15pt mr-1"><a href="./adm_user?id=<?php echo $log["user"]; ?>"><?php echo getUserFromId($odb, $log["user"])["username"]; ?></a>
													   <span class="badge ml-2 badge-soft-primary">Ticket ID #<?php echo $log["id"]; ?></span>
													   <span class="badge ml-2 badge-soft-secondary"><?php echo date("d/m à H:i", $log["date"]); ?></span></strong>
                                                    </div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($log["sujet"]); ?></small>
                                                </div>
                                                <a href="./adm_ticket?id=<?php echo $log["id"]; ?>"><button type="button" class="btn btn-sm btn-primary">voir</button></a>
                                            </div>
										   <?php } ?>
										   
										   <div class="ml-3 mt-3"><?php echo generatePagination($curPage,ceil($feedsCloseCount/9),$actual_link,'page') ?>
		

                                         
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