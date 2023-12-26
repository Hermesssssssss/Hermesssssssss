<?php
$banPage = true;
$page = "Support client";
require '!#/init.php';
require '!#/header1.php';

if(empty($_GET["id"]))
	redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Erreur interne survenue.','support');
else
	$idachat = $_GET["id"];

        $ccQuery = getData($odb, 'tickets',['id'=>intval($idachat), 'user'=>$user["id"]]);
        if ( count($ccQuery) == 0 ) {
			redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Erreur interne survenue.','support');
            
		} 
        $cc = $ccQuery[0];
		
	list($type, $idach) = explode("_", $cc["achat"]);
	
if ( checkArray($_POST,['msg'])) {
    if ( checkIfEmptyArray($_POST,['msg'])) {
        $data = protectArray($_POST);
                     $query = $odb->prepare("INSERT INTO tickets_msg VALUES (NULL, ?, UNIX_TIMESTAMP(), ?, ?, 0)");
					$query->execute(array($idachat,$user["id"], $data["msg"]));
					
					redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Ton message vient d\'être envoyé.',$actual_link);	
           
    }
    else {
        redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Les champs sont manquants.',$actual_link);
    }
}

if ( checkArray($_POST,['clore'])) {
	$data = protectArray($_POST);
	$query = $odb->prepare("UPDATE `tickets` SET `close` = '1' WHERE `id` = ? AND `user` = ?");
	$query->execute(array($idachat,$user["id"]));
	redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Ton ticket vient d\'être clôturé.',$actual_link);	
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

		<?php } } ?>
										
										
										
										</small></h4>
                                        
										</div>
										
                                 
					<div class="flex p-3 d-flex flex-column ps ps--active-y" data-perfect-scrollbar="">
<?php 
											foreach($histo as $log) { 
											
											switch($log["admin"]) {
												case '0':
												     $usr = getUserFromId($odb, $log["user"])["username"];
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
								
								<?php if($cc["close"] == "0") { ?>
							 
							<form action="" method="POST">
								 <div class="card">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Répondre<br>
										<small class="text-muted">N'oublie pas de <a href="" data-toggle="modal" data-target="#modal-standard">lire les règles</a> avant de poster une réponse.
										</small></h4>
                                        
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
								
								 <?php } ?>
						
					
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
 <div id="modal-standard" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-standard-title">Conditions d'utilisation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="modal-body">
                    <p>Pour le bon fonctionnement et l'efficacité de notre support, il est nécessaire de respecter certaines règles. Quand tu ouvres un ticket, si possible et si le soucis concerne un log PPL ou une carte, sélectionne bien l'achat concerné lors de l'ouverture du ticket.
					<br><br>
					
					<b>Il est interdit</b> de,
					<ul>
					<li>Ouvrir plusieurs tickets portant sur le même soucis.</li>
                    <li>Flooder le support en renvoyant plusieurs messages sur le ticket.</li>
                    <li>Insulter ou manquer de respect aux modérateurs du support.</li>
                    <li>Demander un refund pour une base <i>#NOREFUND...</i>, car ce n'est pas remboursable.</li>
                    <li>Demander un refund sur une carte <i>HORS DELAIS (CHECKER)</i>.</li>
					
					</ul>
					
					Si une de ces règles n'est pas respectée, tu pourrais être bannis du shop sans préavis.
					<br><br>
					* Les tickets sont vidés tous les dimanche.</p>
                </div> <!-- // END .modal-body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Fermer</button>
                </div> <!-- // END .modal-footer -->
            </div> <!-- // END .modal-content -->
        </div> <!-- // END .modal-dialog -->
    </div> <!-- // END .modal -->

</body>

</html>