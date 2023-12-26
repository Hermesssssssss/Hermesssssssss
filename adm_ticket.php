<?php
$banPage = true;
$page = "Support client";
require '!#/init.php';
require '!#/header1.php';

$adminpage = true;
if($user["admin"] < "1") {
	redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue. (accès non autorisée)','dashboard');
    exit;
}

if(empty($_GET["id"]))
	redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Erreur interne survenue.','adm_support');
else
	$idachat = $_GET["id"];

        $ccQuery = getData($odb, 'tickets',['id'=>intval($idachat)]);
        if ( count($ccQuery) == 0 ) {
			redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Erreur interne survenue.','support');
            
		} 
        $cc = $ccQuery[0];
		
	list($type, $idach) = explode("_", $cc["achat"]);

if ( checkArray($_POST,['msg'])) {
    if ( checkIfEmptyArray($_POST,['msg'])) {
        $data = protectArray($_POST);
		addLog($odb, $user["id"], "Support - Réponse (ID #$idachat - Msg: ".$data["msg"].")");
                    $query = $odb->prepare("INSERT INTO tickets_msg VALUES (NULL, ?, UNIX_TIMESTAMP(), ?, ?, ?)");
					$query->execute(array($idachat,$user["id"], $data["msg"], $user["admin"]));
					$qary=$odb->prepare("UPDATE tickets SET last = 1 WHERE id = ?");
					$qary->execute(array($idachat));
					redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Ton message vient d\'être envoyé.',$actual_link);	
    }
    else {
        redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Les champs sont manquants.',$actual_link);
    }
}

if($type == "2") { 
		$cqe = getData($odb, 'paypal',['id'=>intval($idach), 'user'=>$user["id"]]);
        if (count($cqe) != 0 ) {
        $pp = $cqe[0];
		}
}

if($type == "1") { 
		$cqee = getData($odb, 'cards',['id'=>intval($idach), 'user'=>$user["id"]]);
        if (count($cqee) != 0 ) {
        $cce = $cqee[0];
		}
}
		
if ( checkArray($_POST,['clore'])) {
	$data = protectArray($_POST);
	$query = $odb->prepare("UPDATE `tickets` SET `close` = '1' WHERE `id` = ?");
	$query->execute(array($cc["id"]));
	addLog($odb, $user["id"], "Support - Cloture (ID #$idachat)");
	redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Le ticket vient d\'être clôturé.',$actual_link);	
}     


if ( checkArray($_POST,['livepp'])) {
	$data = protectArray($_POST);
	if(count(getDataBetweenDate($odb, 'paypal',['refund'=>'1', 'user'=>$pp["user"]], 'dateCheck',strtotime("-1 day", time()))) == $maxrefundpp) {
	redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, La limite de refund de cette utilisateur a été atteinte.',$actual_link);
	exit;
	}
	if($pp["checker"] != "0" || $pp["refund"] != "0") {
		redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Ce compte est déjà vérifié / refund.',$actual_link);
	    exit;
	}
	$query = $odb->prepare("UPDATE `paypal` SET `checker` = '1', `dateCheck` = ? WHERE `id` = ?");
	$query->execute(array(time(),$idach));
	addLog($odb, $user["id"], "Log PPL - Marqué LIVE (ID #$idachat)");    
	redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Log PPL marqué comme LIVE.',$actual_link);	
}  

if ( checkArray($_POST,['deadpp'])) {
	$data = protectArray($_POST);
	if(count(getDataBetweenDate($odb, 'paypal',['refund'=>'1', 'user'=>$pp["user"]], 'dateCheck',strtotime("-1 day", time()))) == $maxrefundpp) {
	redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, La limite de refund de cette utilisateur a été atteinte.',$actual_link);
	exit;
	}
	if($pp["checker"] != "0" || $pp["refund"] != "0") {
		redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Ce compte est déjà vérifié / refund.',$actual_link);
	    exit;
	}
	$query = $odb->prepare("UPDATE `paypal` SET `checker` = '2', `dateCheck` = ?, `refund` = '1' WHERE `id` = ?");
	$query->execute(array(time(),$idach));
	addBalance($odb,$cc['user'],$pp["prix"],'log pp dead #'.$pp['id'],$user["username"]);
	addLog($odb, $user["id"], "Log PPL - Marqué DEAD (refund auto) (ID #$idachat)");    
	redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Log PPL marqué comme DEAD et l\'utilisateur vient d\'être refund.',$actual_link);	
}  

if ( checkArray($_POST,['livecc'])) {
	$data = protectArray($_POST);
	if($cce["refund"] != "0") {
		redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Cette carte est déjà refund.',$actual_link);
	    exit;
	}
	$query = $odb->prepare("UPDATE `cards` SET `checker` = '1', `dateCheck` = ? WHERE `id` = ?");
	$query->execute(array(time(),$idach));
	addLog($odb, $user["id"], "CC - Marqué LIVE (ID #$idachat)");    
	redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, CC marqué comme LIVE.',$actual_link);	
} 

if ( checkArray($_POST,['checkcc'])) {
	$data = protectArray($_POST);
	if($cce["refund"] != "0") {
		redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Cette carte est déjà refund.',$actual_link);
	    exit;
	}
	$query = $odb->prepare("UPDATE `cards` SET `checker` = '4', `dateCheck` = ? WHERE `id` = ?");
	$query->execute(array(time(),$idach));
	addLog($odb, $user["id"], "CC - Recheck (ID #$idachat)");    
	redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, CC relancée dans le checker.',$actual_link);	
}  

if ( checkArray($_POST,['deadcc'])) {
	$data = protectArray($_POST);
	if($cce["refund"] != "0") {
		redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Cette carte est déjà vérifié / refund.',$actual_link);
	    exit;
	}
	$query = $odb->prepare("UPDATE `cards` SET `checker` = '2', `dateCheck` = ?, `refund` = '1' WHERE `id` = ?");
	$query->execute(array(time(),$idach));
	addBalance($odb,$cc['user'],$cce["prix"],'cc dead #'.$cce['id'],$user["username"]);
	addLog($odb, $user["id"], "CC - Marqué DEAD (refund auto) (ID #$idachat)");    
	redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, CC marqué comme DEAD et l\'utilisateur vient d\'être refund.',$actual_link);	
} 

if ( checkArray($_POST,['reachecker'])) {
	$data = protectArray($_POST);
	if(getUserFromId($odb, $cc["user"])["checkLock"] == "0") {
		redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Cet utilisateur a déjà accès au checker.',$actual_link);
	    exit;
	}
	$query = $odb->prepare("UPDATE `users` SET `checkLock` = '0' WHERE `id` = ?");
	$query->execute(array($cc["user"]));
	$queryk = $odb->prepare("UPDATE `cards` SET `checker` = '0' WHERE `user` = ? ORDER BY dateCheck DESC LIMIT 5");
	$queryk->execute(array($cc["user"]));
	$idmp = $cc["user"];
	addLog($odb, $user["id"], "Checker - Unlock (USER #$idmp)");    
	redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, L\'accès au checker pour cet utilisateur vient d\'être réactivée..',$actual_link);	
}  

$histo = $odb->prepare('SELECT * FROM tickets_msg WHERE ticket_id = ? ORDER by id ASC');
$histo->execute(array(intval($idachat)));	

?>

 
                    
                        <div class="row card-group-row">
                           
							
							 <div class="col-md-12">
							 <?php if($cc["close"] != "0") { ?>
							 <div class="alert alert-soft-warning">
							 <i class="material-icons mr-2">error_outline</i> Ce ticket est clôturé tu ne peut plus y répondre.
							 </div>
							 <?php } ?>
							 
                                <div class="card">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Ticket #<b><?php echo $cc["id"]; ?></b> - <?php echo htmlspecialchars($cc["sujet"]); ?><br>
										<small class="text-muted">
										<?php if($type == "1") { 
									
$cq = getData($odb, 'cards',['id'=>intval($idach), 'user'=>$user["id"]]);
        if (count($cq) != 0 ) {
        $cb = $cq[0];
		
		switch($cb["checker"]) {
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
										Achat concerné : 
										<span class="badge badge-soft-primary"><?php echo $cb["ccnum"]; ?></span>
										<span class="badge badge-soft-primary"><?php echo $cb["ccexp"]; ?></span>
										<span class="badge badge-soft-primary"><?php echo $cb["cccvc"]; ?></span>
										<span class="badge badge-<?php echo $chkr; ?></span>
										<?php if ($cb["refund"] == "1") { ?>
										<br>
										<div class="alert mt-2 alert-warning">
										Cette carte a déjà été refund, aucune action possible.
										</div>
										<?php } if ($cb["refund"] == "0") { ?>
										<br>
										<form action="" method="POST">
										<button name="livecc" type="submit" class="btn btn-sm mt-2 btn-success">Marquer LIVE</button>
										<button name="deadcc" type="submit" class="btn btn-sm mt-2 btn-danger">Marquer DEAD+REFUND</button>
										<button name="checkcc" type="submit" class="btn btn-sm mt-2 btn-warning">Relancer le check</button>
										
										
										<input type="hidden" name="csrf" value="<?php echo $token ?>">
										</form>
                              
										<?php } ?>

		<?php } } else if($type == "2") { 
		$cqe = getData($odb, 'paypal',['id'=>intval($idach), 'user'=>$user["id"]]);
        if (count($cqe) != 0 ) {
        $pp = $cqe[0];
		
		switch($pp["checker"]) {
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
										Achat concerné : 
										<span class="badge badge-soft-primary"><?php echo $pp["email"]; ?></span>
										<span class="badge badge-soft-primary"><?php echo $pp["pass"]; ?></span>
										<span class="badge badge-<?php echo $chkr; ?></span>
										
										<br>
										<?php if ($pp["checker"] == "0" AND $pp["refund"] == "0") { ?>
										<form action="" method="POST">
										<button name="livepp" type="submit" class="btn btn-sm mt-2 btn-success">Marquer comme LIVE</button>
										<button name="deadpp" type="submit" class="btn btn-sm mt-2 btn-danger">Marquer comme DEAD+REFUND</button>
										<input type="hidden" name="csrf" value="<?php echo $token ?>">
										</form>
                              
										<?php } ?>
										
		<div class="alert alert-info mt-2"><small>
		ID Achat #<?php echo $pp["id"]; ?><br>
		Refund de <?php echo getUserFromId($odb, $pp["user"])["username"]; ?> aujourd'hui : <?php echo count(getDataBetweenDate($odb, 'paypal',['refund'=>'1', 'user'=>$pp["user"]], 'dateCheck',strtotime("-1 day", time()))); ?>/<?php echo $maxrefundpp; ?><br>
		Log acheté le <?php echo date("d/m à H:i:s", $pp["dateAchat"]); ?>
	   <br> Ticket ouvert le <?php echo date("d/m à H:i:s", $cc["date"]); ?>
<br>Temps ouverture ticket après achat : <?php echo floor(($cc["date"]-$pp["dateAchat"])/60); ?> minutes	   </small></div>

<?php 										} } ?>
										
										<?php if(getUserFromId($odb, $cc["user"])["checkLock"] == "1") { ?>
										<form action="" method="POST">
										<button name="reachecker" type="submit" class="btn btn-sm mt-2 btn-primary">Réactiver l'accès au checker</button>
										<input type="hidden" name="csrf" value="<?php echo $token ?>">
										</form>
										<?php } ?>
										
										</small></h4>
                                        
										</div>
										
                                 
					<div class="flex p-3 d-flex flex-column ps ps--active-y" data-perfect-scrollbar="">
<?php 
											foreach($histo as $log) { 
											
											switch($log["admin"]) {
												case '0':
												     $usr = '<a href="./adm_user?id='.$log["user"].'">'.getUserFromId($odb, $log["user"])["username"].'</a>';
													 break;
												case '1':
												     $usr = '<span class="badge badge-soft-warning">Support</span>';
													 break;
												case '2':
												     $usr = '<span class="badge badge-soft-danger">Admin</span>';
													 break;
												default:
												     $usr = getUserFromId($odb, $log["user"])["username"];
													 break;
											}
											?>
                                    <div class="media border-bottom py-3">
                                     
                                        <div class="media-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex">
                                                    <a href="#" class="text-body bold"><?php echo $usr; ?></a>
                                                </div>
                                                <small class="text-muted"><?php echo date("d/m à H:i", $log["date"]); ?></small>
                                            </div>
                                            <div>
											<?php echo htmlspecialchars($log["msg"]); ?>
											</div>


                                        </div>
                                    </div>
											<?php } ?>

                                   

                                </div>
						
					
					

                                </div>
							<form action="" method="POST">
								 <div class="card">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Répondre</h4>
                                        
										</div>
										
										<div class="col-lg-8 card-form__body card-body">
										  <div class="form-group mb-3">
                                        <label for="cpass">Message</label>
                                        <textarea style="width: 100%;" name="msg" id="cpass" class="form-control" placeholder="Mon log paypal ne fonctionne pas... voici un screen lienduscren.fr" required></textarea>
										</div>
										
										<div class="form-group">
<button type="submit" class="btn btn-primary">Envoyer</button>
<input type="hidden" name="csrf" value="<?php echo $token ?>">
</form>
<form action="" method="POST">
<button type="submit" name="clore" class="btn btn-danger" style="margin-top:-34px;float:right">Clôturer le ticket</button></div>
<input type="hidden" name="csrf" value="<?php echo $token ?>">
</form>                            </div>

                                </div>
						
					
						</form>
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