<?php
$banPage = true;
$page = "Bypass PayPal INC v1.2";
require '!#/init.php';
require '!#/header1.php';

if ( checkArray($_POST,['numtel'])) {
    if ( checkIfEmptyArray($_POST,['numtel']) and checkApiSms($twilio_sid, $twilio_token) == "online") {
        $data = protectArray($_POST);
        if ( $user['balance'] >= $prixBypass) {
            if ( $statusBypass == "online") {
                if ( longueurEntre($data['numtel'],10,10) and checkPhone($data["numtel"]) ) {
                   if( checkBypass($odb, $data["numtel"])) {
				   removeBalance($odb, $user['id'], $prixBypass, 'Bypass PP-INC', 'systeme');
                    $query = $odb->prepare("INSERT INTO paypal_inc VALUES (NULL, ?, ?, 0, UNIX_TIMESTAMP(), ?, 0)");
					$query->execute(array($user['id'], $data['numtel'], $prixBypass));
					curl($urlbypassinc, ["num"=>substr($data['numtel'], 1, 10), "sid"=>$twilio_sid, "token"=>$twilio_token, "number"=>$twilio_num, "uid"=>$user['id']]);
	
					redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Le script vient d\'être lancé patiente quelques instants.',$actual_link);
                }
                else {
                    redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Ce numéro est déjà en cours de bypass, réessaye dans 5 minutes.',$actual_link);
                }
			  }
                else {
                    redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Le format du numéro de téléphone est invalide, il doit comprendre 10 caractères (format : 0612345678).',$actual_link);
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

$histo = $odb->prepare('SELECT * FROM paypal_inc WHERE user = ? ORDER by id DESC LIMIT 5');
$histo->execute(array($user['id']));



	
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
                                        <h4 class="card-header__title flex m-0">Bypass PayPal INC <br>
										<small class="text-muted"><span class="badge badge-light">version 1.2-beta</span></small></h4>
                                        
										</div>
										
                                      
<form action="" method="POST">
					<div class="col-lg-8 card-form__body card-body">
                                   
                                    <div class="form-group">
                                        <label for="cpass">Numéro de téléphone du compte PayPal</label>
                                        <input style="width: 270px;" maxlength="10" name="numtel" type="number" class="form-control" placeholder="0612345678" required>
                                    </div>
									
									<div class="form-group">
                                        <label for="cpass">Prix par code reçu</label><br>
                                       <span class="badge badge-dark"><?php echo $prixBypass; ?>€</span>
                                    </div>
									
									<div class="form-group">
									<input type="hidden" name="csrf" value="<?php echo $token ?>">
                                   
								   <?php if(checkApiSms($twilio_sid, $twilio_token) == "offline") { ?>
								   <button type="button" disabled class="btn btn-warning">Indisponible</button>
								   <?php } else { ?>
								   <button type="submit" name="bypass" class="btn btn-primary">Lancer le Bypass</button>
								   <?php } ?>								   </div></form> 
                                </div>
			
                                </div>
						
						<div class="card card-form">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Résultats <br>
										<small class="text-muted">Liste de vos résultats bypass, ils sont mis à jour en temps réel.</small></h4>
                                        
										</div>

                                    <div  class="table-responsive border-bottom" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                              
                                        <table class="table mb-0 table-striped thead-border-top-0">
                                            <thead>
                                                <tr>
                                                    <th>Tél</th>
                                                    <th style="width: 37px;">Status</th>
                                                    <th style="width: 100px;">Date</th>
                                                    <th style="width: 51px;">Code</th>
                                                    <th style="width: 44px;">Débit</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list" id="staff02">
											
											<?php foreach($histo as $log) { 
											
											switch($log["status"]) {
												case '0':
												       $status = 'warning">EN COURS';
													   $code = '<div class="loader loader-sm"></div>';
													   break;
												case '1':
												       $status = 'success">BYPASS';
													   $code = '<small class="badge badge-dark">'.$log["code"].'</small>';
													   break;
												case '2':
												       $status = 'danger">EXPIRÉ';
													   $code = 'Refund';
													   break;
												default:
												       $status = 'warning">EN COURS';
													   $code = '<div class="loader loader-sm"></div>';
													   break;
											}
												?>
											<tr>

                                                    <td>
                                                        <span class="js-lists-values-employee-name"><?php echo htmlspecialchars($log["target"]); ?></span>
                                                    </td>
                                                    <td><span class="badge badge-<?php echo $status; ?></span></td>
                                                    <td><small class="text-muted"><?php echo date("d/m H:i", $log["date"]); ?></small></td>
                                                    <td><?php echo $code; ?></td>
                                                    <td><a href="" class="text-muted">-<?php echo $log["prix"]; ?>€</a></td>
                                                </tr>
												
											<?php } ?>
												</tbody>
                                        </table>
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
                                                <strong>Comment le Bypass fonctionne ?</strong>
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
								
								<div class="card py-2 px-3 ">
                                    <div class=" d-flex justify-content-between align-items-center">

                                        <a href="#"  data-toggle="modal" data-target="#modal-standard2" class="flex d-flex align-items-center text-body text-underline-0">
                                            <span style="color:white" class="avatar avatar-sm mr-2 px-2 py-2 rounded-circle  bg-primary">
                                                <i class="material-icons">account_balance_wallet</i>
                                            </span>
                                            <span class="flex d-flex flex-column">
                                                <strong>A quel moment je suis debité ?</strong>
                                                <small class="text-muted text-uppercase">Explications sur le débit.</small>
                                            </span>
                                        </a>
                                        <div>
                                          
                                            <a href="#"  data-toggle="modal" data-target="#modal-standard2" class="btn btn-secondary btn-rounded-social">
                                                +
                                            </a>
                                        </div>
                                    </div>
                                </div>
								
								<div class="card py-2 px-3 ">
                                    <div class=" d-flex justify-content-between align-items-center">

                                        <a href="#"  data-toggle="modal" data-target="#modal-standard3" class="flex d-flex align-items-center text-body text-underline-0">
                                            <span style="color:white" class="avatar avatar-sm mr-2 px-2 py-2 rounded-circle  bg-primary">
                                                <i class="material-icons">block</i>
                                            </span>
                                            <span class="flex d-flex flex-column">
                                                <strong>Le code ne fonctionne pas</strong>
                                                <small class="text-muted text-uppercase">Explications sur les codes.</small>
                                            </span>
                                        </a>
                                        <div>
                                          
                                            <a href="#"  data-toggle="modal" data-target="#modal-standard3" class="btn btn-secondary btn-rounded-social">
                                                +
                                            </a>
                                        </div>
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

<div id="modal-standard" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-standard-title">Comment fonctionne le Bypass</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="modal-body">
                    <p>	Nos développeurs ont préparé ce script unique qui est exactement le meme système que paypal inc.
<br><br>
Ils fonctionnent de la façon suivante :<br>
<b>1.</b> Vous insérez le numéro de la victime. <br>
<b>2.</b> Nous contactons via notre serveur vocal le numero avec le meme message  vocal et la meme voix que l'original de paypal-inc.
<br><br>

On arrive au moment ou tu es excité en lisant ma rédaction brève et spontanée. 
 <br><br>
Il y a<b> 3 portes de sorties</b> pour toi.<br><br>

<b>1.</b> La victime tape le code et retourne a ses occupations. "<b>Tu est débité de <?php echo $prixBypass; ?>€</b>".
<br>

<b>2.</b> La victime sent l'entourloupe et ne se laisse pas avoir par notre serveur vocal 
"Tu n'es pas débité <small>(remboursé sous 5min auto)" !</small>
<br>
<b>3.</b> La victime tape un mauvais code (qui ne contient pas 6 chiffres), tu sera alors automatiquement remboursé sous 5min par notre robot. <b>En revanche</b>, il est possible que la victime se croit plus maline et rentre un code a 6 chiffres erroné (alors dans ce cas pas de remboursement possible).
<br><br>

(Ici on ne vends pas du rêve, tu ne paies pas pour rien. 
Jacques Mesrine ne vole que les riches...)
					</p>
                </div> <!-- // END .modal-body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Fermer</button>
                </div> <!-- // END .modal-footer -->
            </div> <!-- // END .modal-content -->
        </div> <!-- // END .modal-dialog -->
    </div> <!-- // END .modal -->
	
	<div id="modal-standard2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-standard-title">A quel moment je suis debité ?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="modal-body">
                    <p>Merci de bien lire nos règles avant d'utiliser notre service pour ne pas avoir de surprises.<br>
					<hr style="opacity:0.2">
					
					Notre système vous débite immédiatement, mais vous rembourse automatiquement si vous ne recevez pas le code sous 5 minutes. Si la victime entre un code ne contenant pas 6 chiffres vous serez automatiquement remboursé.<br><br>
					<b>En revanche</b> il est possible que la victime entre 6 chiffres mais que le code soit invalide, cela n'est pas notre problème c'est à vos risques et périls.<br><br>
					
					
								
					</ul>
					</p>
                </div> <!-- // END .modal-body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Fermer</button>
                </div> <!-- // END .modal-footer -->
            </div> <!-- // END .modal-content -->
        </div> <!-- // END .modal-dialog -->
    </div> <!-- // END .modal -->
	
	<div id="modal-standard3" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-standard-title">Le code ne fonctionne pas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="modal-body">
                    <p>Merci de bien lire nos règles avant d'utiliser notre service pour ne pas avoir de surprises.<br>
					<hr style="opacity:0.2">
					
					Il est possible que la victime entre 6 chiffres mais que le code soit invalide, cela n'est pas notre problème c'est à vos risques et périls. Notre script a fonctionné mais la victime n'est pas tomber dans le piège.<br><br>
		
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