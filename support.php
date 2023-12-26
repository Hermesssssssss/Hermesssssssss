<?php
$banPage = true;
$page = "Support client";
require '!#/init.php';
require '!#/header1.php';

$erreur = false;
if ( checkArray($_POST,['sujet','achat','msg']) ) {
    if ( checkIfEmptyArray($_POST,['sujet','achat','msg'])) {
        $data = protectArray($_POST);

        if ( !longueurEntre($data['sujet'],4,50) ) {
            redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Le sujet doit comprendre entre 4-50 caractères.',$actual_link);
            $erreur = true;
	   }
	   
	   if(preg_match("/[0-2]{1}[^0-9_]/", $data["achat"])) {
		    redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, regex Erreur interne survenue.',$actual_link);
            $erreur = true;
	   }
	   
	   if(empty($data["achat"])) {
		   redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Erreur interne survenue.',$actual_link);
           $erreur = true;
	   }
	   
	   list($type, $idachat) = explode("_", $data["achat"]);

	   if($type == "1" AND $idachat != "0") {
	   
	    $ccQuery = getData($odb, 'cards',['id'=>intval($idachat)]);
        if ( count($ccQuery) == 0 ) {
			redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Erreur interne survenue.',$actual_link);
            $erreur = true;
		} 
        $cc = $ccQuery[0];
		
		if($cc['user'] != $user['id']) { 
		redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Erreur interne survenue.',$actual_link);
            $erreur = true;
		}
		
	   }
	   
	   if($type == "2" AND $idachat != "0") {
	   
	    $ccQuery = getData($odb, 'paypal',['id'=>intval($idachat)]);
        if ( count($ccQuery) == 0 ) {
			redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Erreur interne survenue.',$actual_link);
            $erreur = true;
		} 
        $cc = $ccQuery[0];
		
		if($cc['user'] != $user['id']) { 
		redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Erreur interne survenue.',$actual_link);
            $erreur = true;
		}
		
	   }
	   
	   $reform = intval($type)."_".intval($idachat);

        if ( !$erreur ) {
            $query = $odb->prepare('INSERT INTO tickets VALUES (NULL, ?, ?, UNIX_TIMESTAMP(), ?, 0, 0)');
            $query->execute(array($data['sujet'], $reform, $user['id']));
			$ticket_id = $odb->lastInsertId();
			$query2 = $odb->prepare('INSERT INTO tickets_msg VALUES (NULL, ?, UNIX_TIMESTAMP(), ?, ?, 0)');
            $query2->execute(array($ticket_id, $user['id'], $data['msg']));
           
            redirectWithMessage('soft-success d-flex align-items-center',' <i class="material-icons mr-3">check_circle</i><div class="text-body"><strong>Bravo</strong>, Le ticket a été ouvert, un modérateur te répondra sous 24 heures.</div>',$actual_link);
        }
    }
    else {
        redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Des champs sont manquants. ',$actual_link);
    }
}

$histo = $odb->prepare('SELECT * FROM tickets WHERE user = ? ORDER by id DESC LIMIT 10');
$histo->execute(array($user['id']));

$achat = $odb->prepare('SELECT * FROM cards WHERE user = ? ORDER by dateAchat DESC LIMIT 5');
$achat->execute(array($user['id']));
$achatpp = $odb->prepare('SELECT * FROM paypal WHERE user = ? ORDER by dateAchat DESC LIMIT 5');
$achatpp->execute(array($user['id']));
?>

 
                    
                        <div class="row card-group-row">
                           
							
							 <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Ouvrir un ticket<br>
										<small class="text-muted">Tu as un problème sur notre site ? Un compte paypal invalide ? Un problème avec une carte ou autres, ouvre un ticket.</small></h4>
                                        
										</div>
										
                                      
<form action="" method="POST">
					<div class="col-lg-8 card-form__body card-body">
					
					<div class="alert alert-soft-primary d-flex align-items-center" role="alert">
                                            <i class="material-icons mr-3">flag</i>
                                            <div class="text-body"><strong>Information</strong>,
											avant d'ouvrir un ticket il est nécessaire de <a href="" data-toggle="modal" data-target="#modal-standard">lire les règles</a> afin d'éviter de te faire ban.
											</div>
                                        </div>
										
                                    <div class="form-group">
                                        <label for="opass">Sujet</label>
                                        <input style="width: 100%;" name="sujet" id="opass" type="text" class="form-control" placeholder="Refund PP" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="npass">Achat concerné <small>(facultatif)</small></label>
                                        <select style="width: 100%" name="achat" class="form-control">
										<option value="0_0">Aucun sélectionné</option>
										<?php 
											foreach($achat as $log) {
                                             
											switch($log["checker"]) {
case '0':
     $checker = "Non check";
break;

case '1': 
     $checker = "Live";
break;

case '2':
     $checker = "Dead refund";
break;

case '4':
     $checker = "En cours de check";
break;

default:
     $checker = "Erreur lors du check";
break;
											}	 
											?>
										<option value="1_<?php echo $log["id"]; ?>">Carte #<?php echo $log["id"]; ?> (<?php echo $log['level']; ?>) - achetée le <?php echo date("d/m à H:i:s", $log["dateAchat"]); ?> (<?php echo $checker; ?>)</option>
									
										<?php } 
										
											foreach($achatpp as $log) {
                                             
											
											?>
										<option value="2_<?php echo $log["id"]; ?>">PayPal #<?php echo $log["id"]; ?> (<?php echo substr($log['email'], 0, 6).'***'; ?>) - achetée le <?php echo date("d/m à H:i:s", $log["dateAchat"]); ?></option>
									
										<?php } ?>
										</select>
                                   <small class="text-muted">Il est recommandé de sélectionner l'achat concerné pour un traitement plus rapide de votre ticket.</small>
								   </div>
                                    <div class="form-group mb-3">
                                        <label for="cpass">Explications</label>
                                        <textarea style="width: 100%;" name="msg" id="cpass" class="form-control" placeholder="Mon log paypal ne fonctionne pas... voici un screen lienduscren.fr" required></textarea>
										</div>
										
										<div class="form-group">
<button type="submit" class="btn btn-primary">Ouvrir le ticket</button></div>
                                </div>
			<input type="hidden" name="csrf" value="<?php echo $token ?>">

                                </div>
						
					
						</form>
							

                                </div>
								
								<div class="col-md-6">
								
								<div class="card card-form">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Historique des tickets</h4>
                                        
										</div>

                                    <div  class="table-responsive border-bottom" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                              
                                        <table class="table mb-0 table-striped thead-border-top-0">
                                            <thead>
                                                <tr>
                                                    
                                                    <th style="width: 37px;">Date</th>
                                                    <th style="width: 37px;">Sujet</th>
                                                    <th style="width: 100px;">Status</th>
                                                    <th style="width: 51px;">Dernière interlocuteur </th>
                                                    <th style="width: 44px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody class="list" id="staff02">
											<?php 
											foreach($histo as $log) { 
											
											switch(lastTicket($odb, $log["id"])['admin']) {
											case '0':
											     $rank = '<small>'.getUserFromId($odb, $log["user"])["username"].'</small>';
												 $status = '<span class="badge badge-soft-secondary">En attente d\'une réponse</span>';
												 
												 break;
											case '1':
											     $rank = '<span class="badge badge-soft-warning">Support</span>';
												 $status = '<span class="badge badge-soft-success">Répondu</span>';
												 break;
											case '2':
											     $rank = '<span class="badge badge-soft-danger">Admin</span>';
												 $status = '<span class="badge badge-soft-success">Répondu</span>';
												 break;
											}
											
											if($log["close"] != "0")
												$status = '<span class="badge badge-soft-danger">Ticket clôturé</span>';
												
											
										
											?>
											<tr>

                                                    <td><small class="text-muted"><?php echo date("d/m H:i", $log["date"]); ?></small></td>
                                                    <td><small class="text-muted">
													<a href="./ticket?id=<?php echo $log["id"]; ?>"><?php echo substr($log["sujet"], 0, 25).'...'; ?></small>
													</a></td>
                                                    
                                                    <td>
                                                        <span class="js-lists-values-employee-name"><?php echo $status; ?></span>
                                                    </td>
													<td><?php echo $rank; ?>
													</td>
                                                    <td><a href="./ticket?id=<?php echo $log["id"]; ?>"><button type="button" class="btn btn-sm btn-info">voir</button></a></td>
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