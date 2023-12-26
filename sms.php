<?php
$banPage = true;
$page = "SMS Spoofer";
require '!#/init.php';
require '!#/header1.php';

if ( checkArray($_POST,['exp'],['desti'],['msg'])) {
    if ( checkIfEmptyArray($_POST,['exp'],['desti'],['msg']) and checkApiSms($twilio_sid, $twilio_token) == "online") {
        $data = protectArray($_POST);
        if ( $user['balance'] >= $prixsms) {
            if ( $statusBypass == "online") {
				if( longueurEntre($data['exp'],2,11) and ctype_alnum($data["exp"])) {
				if( longueurEntre($data['msg'],4,160)) {
                if ( longueurEntre($data['desti'],10,10) and checkPhone($data["desti"]) ) {
                    removeBalance($odb, $user['id'], $prixsms, 'SMS Spoofer', 'systeme');
                    $query = $odb->prepare("INSERT INTO sms_histo VALUES (NULL, ?, ?, ?, ?, UNIX_TIMESTAMP(), ?)");
					$query->execute(array($user['id'], $data['exp'], $data['desti'], $data['msg'], $prixsms));
					curl($urlsms, ["num"=>substr($data['desti'], 1, 10), "sid"=>$twilio_sid, "token"=>$twilio_token, "from"=>$data['exp'], "msg"=>$data['msg']]);
	
					redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Ton SMS a été envoyé avec succès.',$actual_link);
                }
                else {
                    redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Le format du numéro de téléphone est invalide, il doit comprendre 10 caractères (format : 0612345678).',$actual_link);
                }
				
			  }
                else {
                    redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Le format du message est invalide, il doit comprendre entre 4-160 caractères.',$actual_link);
                }
			  }
                else {
                    redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Le format de l\'expéditeur est invalide, il doit comprendre entre 4-11 caractères alphanumerique (exemple : MesrineCC).',$actual_link);
                }
            }
            else {
                redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Notre serveur Bypass est temporairement indisponible.',$actual_link);
            }
        }
        else {
            redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Ta balance est trop faible il te faut <b>'.$prixBypass.'</b>€ pour utiliser notre outil (<a href="./depot">dépose des fonds</a>).',$actual_link);
        }
    }
    else {
        redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue.',$actual_link);
    }
}

$histo = $odb->prepare('SELECT * FROM sms_histo WHERE user = ? ORDER by id DESC LIMIT 6');
$histo->execute(array($user['id']));

$histod = $odb->prepare('SELECT * FROM sms_histo WHERE user = ? ORDER by id DESC LIMIT 6');
$histod->execute(array($user['id']));



?>

                    
                        <div class="row card-group-row">
						<?php if(checkApiSms($twilio_sid, $twilio_token) == "offline") { ?>
                           <div class="col-md-12">
						  <div class="alert alert-soft-warning d-flex align-items-center card-margin" role="alert">
                            <i class="material-icons mr-3">error_outline</i>
                            <div class="text-body"><strong>Oops!</strong> Notre API est temporairement en maintenance, essaye de revenir plus tard.</div>
                        </div>
						   </div>
						<?php } ?>
							
							 <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">SMS Spoofer <br>
										<small class="text-muted"><span class="badge badge-light">version <?php echo $versionsms; ?></span></small></h4>
                                        
										</div>
										
                                      
<form action="" method="POST">
					<div class="col-lg-8 card-form__body card-body">
                                   
                                    <div class="form-group">
                                        <label for="cpass">Expéditeur</label>
                                        <input style="width: 100%;" maxlength="11" name="exp" type="text" class="form-control" placeholder="J Mesrines" required>
                                        <small class="form-text text-muted">Format: Alphanumerique (4-11 caractères)</small>
									</div>
									
									<div class="form-group">
                                        <label for="cpass">Numéro du destinataire</label>
                                        <input style="width: 100%;" name="desti" type="number" class="form-control" placeholder="0612345678" required>
									</div>
									
									<div class="form-group">
                                        <label for="cpass">Message</label>
                                        <input style="width: 100%;" maxlength="160" name="msg" type="text" class="form-control" placeholder="Tu braques chez moi maintenant ? Et alors il est ou le problème!" required>
                                       <small class="form-text text-muted">Longueur max du message : 160 caractères</small>
								   </div>
									
									<div class="form-group">
                                        <label for="cpass">Prix par SMS</label><br>
                                       <span class="badge badge-dark"><?php echo $prixsms; ?>€</span>
                                    </div>
									
									<div class="form-group">
									<input type="hidden" name="csrf" value="<?php echo $token ?>">
                                   
								   <?php if(checkApiSms($twilio_sid, $twilio_token) == "offline") { ?>
								   <button type="button" disabled class="btn btn-warning">Indisponible</button>
								   <?php } else { ?>
								   <button type="submit" name="bypass" class="btn btn-primary">Envoyer le SMS</button>
								   <?php } ?>								   </div></form> 
                                </div>
			
                                </div>
						
						
                            </div>
							
							 <div class="col-md-6">
                               <div class="card py-2 px-3 ">
                                    <div class=" d-flex justify-content-between align-items-center">

                                        <a href="#"  data-toggle="modal" data-target="#modal-standard" class="flex d-flex align-items-center text-body text-underline-0">
                                            <span style="color:white" class="avatar avatar-sm mr-2 px-2 py-2 rounded-circle  bg-primary">
                                                <i class="material-icons">info</i>
                                            </span>
                                            <span class="flex d-flex flex-column">
                                                <strong>Comment le Spoofer fonctionne ?</strong>
                                                <small class="text-muted text-uppercase">Explications sur notre script.</small>
                                            </span>
                                        </a>
                                        <div>
                                          
                                            <a href="#"  data-toggle="modal" data-target="#modal-standard" class="btn btn-secondary btn-rounded-social">
                                                +
                                            </a>
                                        </div>
                                    </div>
                                </div>
								
								
							<div class="card card-form">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Historique des envois</h4>
                                        
										</div>

                                    <div  class="table-responsive border-bottom" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                              
                                        <table class="table mb-0 table-striped thead-border-top-0">
                                            <thead>
                                                <tr>
                                                    
                                                    <th style="width: 37px;">Date</th>
                                                    <th style="width: 100px;">Destinataire</th>
													<th>Message</th>
                                                    <th style="width: 51px;">Expéditeur</th>
                                                    <th style="width: 44px;">Débit</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list" id="staff02">
																						<?php foreach($histo as $log) { 
											?>
											<tr>

                                                    <td><small class="text-muted"><?php echo date("d/m H:i", $log["date"]); ?></small></td>
                                                    
                                                    <td><?php echo $log["destinataire"]; ?></span></td>
                                                    <td>
                                                        <span class="js-lists-values-employee-name"><button data-toggle="modal" data-target="#modal-standard<?php echo $log["id"]; ?>" class="btn btn-sm btn-dark">voir</button></span>
                                                    </td>
													<td><span class="badge badge-primary"><?php echo htmlspecialchars($log["expediteur"]); ?></span></td>
                                                    <td><a href="" class="text-muted">-<?php echo $log["prix"]; ?>€</a></td>
                                                </tr>
												
			
											<?php } ?>
												</tbody>
                                        </table>
                                    </div>


                                </div>
								
						<!-- delim -->
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

<?php foreach($histod as $log) { 
											?>
<div id="modal-standard<?php echo $log["id"]; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-standard-title">SMS #<?php echo $log["id"]; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="modal-body">
                    <p><?php echo htmlspecialchars($log["msg"]); ?>
					</ul>
					</p>
                </div> <!-- // END .modal-body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Fermer</button>
                </div> <!-- // END .modal-footer -->
            </div> <!-- // END .modal-content -->
        </div> <!-- // END .modal-dialog -->
    </div> <!-- // END .modal -->
<?php } ?>
					

<div id="modal-standard" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-standard-title">Fonctionnement du SMS Spoofer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="modal-body">
                    <p>SMS spoofing est une technologie qui utilise le service de messages courts, disponible sur la plupart des téléphones mobiles et assistants numériques personnels, pour définir l'origine du message en remplaçant le numéro de mobile d'origine par un texte alphanumérique.
					<br><br>
					Notre script est actuellement en version <?php echo $versionsms; ?> et fonctionne uniquement pour les pays suivants : <b>France</b>.<br>
					
					<div class="col-sm-12">
                                <div class="card stories-card-popular">
                                    <img src="assets/proof-sms.jpg" alt="" class="card-img">
                                    <div class="stories-card-popular__content">
                                        
                                        <div class="stories-card-popular__title card-body">
                                            <small style="color:black;" class="text-uppercase">exemple sms spoofed (mesrinecc.to)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
							
					<small>(Quand Jacques Mesrine braque une banque, il n'a pas l'intention d'enfreindre la lois mais juste de voler un plus voleur que soit)</small>
					
					</ul>
					</p>
                </div> <!-- // END .modal-body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Fermer</button>
                </div> <!-- // END .modal-footer -->
            </div> <!-- // END .modal-content -->
        </div> <!-- // END .modal-dialog -->
    </div> <!-- // END .modal -->					
</body>

</html>