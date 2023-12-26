<?php
$banPage = true;
$page = "Déposer des fonds";
require '!#/init.php';
require '!#/header1.php';


if ( checkArray($_POST,['gen'])) {
    if ( getCurrentTransaction($odb, $user['id']) != "0") {
        redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Tu a déjà une adresse de dépôt, utilise celle déjà générée avant d\'en générer une nouvelle.',$actual_link);
    }
    else {
        $data = json_decode(curl('https://block.io/api/v2/get_new_address/',['api_key'=>$blockio_api]),true);
        if ( $data['status'] == 'fail') {
           redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Notre API de dépôt est temporairement indisponible.',$actual_link);
        }
        else {
			$dep = $data['data']['address'];
            $query = $odb->prepare('INSERT INTO DepotBTC  VALUES (NULL, UNIX_TIMESTAMP(), ?, 0, 0, 0, ?)');
            $query->execute(array($data['data']['address'], $_SESSION['id']));
           redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Voici ton adresse de dépôt : <b>'.$dep.'</b>',$actual_link);
                    
        }
    }
}

$sqle = "SELECT * FROM `DepotBTC` WHERE `user` = ? ORDER by id DESC ";
$u = $odb->prepare($sqle);
$u->execute(array(intval($_SESSION["id"])));

$sqle .= "LIMIT 1";
$p = $odb->prepare($sqle);
$p->execute(array(intval($_SESSION["id"])));
?>

 
                    
                        <div class="row card-group-row">
                           
							
							 <div class="col-md-7">
                                <div class="card">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Déposer des fonds via BTC <br>
										<small class="text-muted">Alimente ton compte pour pouvoir acheter sur le shop.</small></h4>
                                        
										</div>
										
                                      
<form action="" method="POST">
					<div class="col-lg-12 card-body">
					<span style="float: right; text-align:right"><small class="text-muted">TA BALANCE</small><br><b class="badge badge-light"><?php echo $user["balance"]; ?>€</b></span>
					<img src="./assets/btc-accepted.jpg" style="float:lefr;width:200px">
				    <hr style="opacity:0.3">
                                   <div style="text-align:justify">
								   Le processus de paiement via BITCOIN est entièrement automatisé. Il ne nécessite pas de validations manuelles. 
								   Notre système te prélèvera <b>1% de frais</b> sur chacun de tes dépôts sur notre site. Pense à toujours bien mettre assez de frais pour que ta transaction se valide sous 30 mins.<br><br>
								   </div>
								   
								   <input type="hidden" name="csrf" value="<?php echo $token ?>">
								   <?php if ( getCurrentTransaction($odb, $user['id']) != "0") { 														
while(($i=$p->fetch()) != null ) { 

switch($i["status"]) {
	case '0':
	      $status = 'En attente de paiement';
		  break;
	case '1':
	      $status = 'En attente de confirmation (0/1)';
		  break;
	case '2':
	      $status = 'Credité';
		  break;
	case '3':
	      $status = 'Expiré';
		  break;
	default:
	      $status = 'Erreur';
		  break;
}
	
?>

<div style="background:none; margin-bottom:-3px" class="card-form__body form-group">
<label for="opass">Envoyer les fonds sur l'adresse :</label>
</div>
<div style="text-align:center" class="alert alert-secondary mb-2">
<?php echo $i['address']; ?>
</div> 
<input id="appID" value="<?php echo $i['address']; ?>" type="hidden">
<button id="copyBtn" type="button" style="width:100%" class="btn mb-2 btn-light btn-sm">Copier l'adresse dans le presse-papiers</button>
								  <div class="card card-body bg-dark text-white mb-0">

                                        <ul class="list-unstyled ml-1 mb-0">
                                            <li class="d-flex align-items-center pb-1"><div class="loader loader-sm mr-2"></div> <?php echo $status; ?> </li>
                                            <li class="d-flex align-items-center"><i class="material-icons icon-16pt text-primary mr-2">error</i> 1 seul dépôt par adresse générée</li>
                                        </ul>
                                    </div>
		<script>
var copyBtn = document.getElementById("copyBtn");
copyBtn.onclick = function(){
    var myCode = document.getElementById("appID").value;
    var fullLink = document.createElement("input");
    document.body.appendChild(fullLink);
    fullLink.value =  myCode;
    fullLink.select();
    document.execCommand("copy", false);
    fullLink.remove();
    alert("Adresse BTC copié dans le presse-papiers: " + fullLink.value);
}
</script>
<?php }  } else { ?>
<span class="badge badge-dark">1 confirmation nécessaire</span><br><br>
								   
								   <button type="submit" name="gen" style="width:100%" class="btn btn-primary">Générer une adresse de dépôt</button>
								   
								   <?php } ?>
                                </div>
			</form>
							

                                </div>
								
								<div class="card card-form">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Historique de tes dépôts<br>
										<small class="text-muted">Liste de tes derniers dépôts, ils sont mis à jour en temps réel.</small></h4>
                                        
										</div>

                                    <div  class="table-responsive border-bottom" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                              
                                        <table class="table mb-0 table-striped thead-border-top-0">
                                            <thead>
                                                <tr>
												    <th style="width: 100px;">Date</th>
                                                   
                                                    <th>Adresse BTC</th>
                                                    <th style="width: 37px;">Montant</th>
                                                    <th style="width: 51px;">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list" id="staff02">
											
											<?php foreach($u as $log) { 
											
											
switch($log["status"]) {
	case '0':
	      $status = '<span class="badge badge-primary">En attente de paiement</span>';
		  break;
	case '1':
	      $status = '<span class="badge badge-warning">En attente de confirmation (0/1)</span>';
		  break;
	case '2':
	      $status = '<span class="badge badge-success">Credité</span>';
		  break;
	case '3':
	      $status = '<span class="badge badge-danger">Expiré</span>';
		  break;
	default:
	      $status = '<span class="badge badge-danger">Erreur</span>';
		  break;
}
												?>
											<tr>

                                                     <td><small class="text-muted"><?php echo date("d/m H:i", $log["date"]); ?></small></td>
                                                    
                                                    
                                                   <td><?php echo $log["address"]; ?></td>
                                                    <td><a href="" class="text-muted"><?php echo $log["amount"]; ?>€</a></td>
													<td><?php echo $status; ?></td>
                                                </tr>
												
											<?php } ?>
												</tbody>
                                        </table>
                                    </div>


                                </div>

                                </div>
								
								 <div class="col-md-5">
                              
							  <div class="alert alert-soft-secondary d-flex" role="alert">
                                            <i class="material-icons mr-3">adjust</i>
                                            <div class="text-body"><strong>1 BTC</strong> = <?php echo btcToEuro ("1"); ?>€ (coinbase-api)</div>
                                        </div>
							  
							  <div class="card py-2 px-3 ">
                                    <div class=" d-flex justify-content-between align-items-center">

                                        <a href="#"  data-toggle="modal" data-target="#modal-standard" class="flex d-flex align-items-center text-body text-underline-0">
                                            <span style="color:white" class="avatar avatar-sm mr-2 px-2 py-2 rounded-circle  bg-primary">
                                                <i class="material-icons">info</i>
                                            </span>
                                            <span class="flex d-flex flex-column">
                                                <strong>Je n'ai pas de BTC, que faire ?</strong>
                                                <small class="text-muted text-uppercase">Comment obtenir des BTC.</small>
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
                                                <i class="material-icons">access_time</i>
                                            </span>
                                            <span class="flex d-flex flex-column">
                                                <strong>Ma transaction n'est toujours pas confirmée</strong>
                                                <small class="text-muted text-uppercase">Informations sur les délais.</small>
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
                                                <i class="material-icons">file_upload</i>
                                            </span>
                                            <span class="flex d-flex flex-column">
                                                <strong>Puis-je récupérer mes fonds de ma balance ?</strong>
                                                <small class="text-muted text-uppercase">Informations sur wallet.</small>
                                            </span>
                                        </a>
                                        <div>
                                          
                                            <a href="#"  data-toggle="modal" data-target="#modal-standard3" class="btn btn-secondary btn-rounded-social">
                                                +
                                            </a>
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
	 </div>
	 </div>
    <!-- // END header-layout -->

  

<?php require "!#/jsinclude.php"; ?>


<div id="modal-standard" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-standard-title">Je n'ai pas de BTC</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="modal-body">
                    <p>Comme pour beaucoup de nos clients, beaucoup n'utilise pas la crypto BTC car ils ne savent pas comment s'en procurer. Nous allons vous guider et vous expliquer comment vous procurer simplement, rapidement et surtout divers moyens de paiements (<b>CB, Espèce, PayPal..</b>).
					<br><br>
					<u>Achat en espèces (bureau de tabac) </u>: <br>
					<b>Keplerk</b> - Les coupons Keplerk vous permet d'acheter des bitcoins (disponible dans certains bureau de tabac), néanmoins il est nécessaire d'ouvrir un compte sur leurs site et le valider (un scan CNI suffit). Ensuite il faudra créditer votre compte Keplerk pour pouvoir obtenir des bitcoin et pouvoir envoyer les BTC sur ton adresse de dépôt MesrineCC. (<a href="https://youtu.be/Hdw5GiPBkzU" target="_blank">Vidéo tutoriel</a>)
					<br><br>
					<b>DigyCode</b> - Même principe et même fonctionnement que keplerk. (<a href="https://youtu.be/Ts46w-xNUBo" target="_blank">Vidéo tutoriel</a>)
					<br><br>
					
					<u>Achat par CB/PayPal</u>:<br>
					<b>CoinHouse, CoinMama, CoinBase, BitIt</b> - Ces sites fonctionnent de la même manière mais ils nécessitent certains documents pour valider ton inscription. (des tutoriels sont disponible sur youtube)<br><br>
					<u>Achat par PCS</u>:<br>
					<b>Bitwol.com</b> - Site très simplement d'utilisation mais nous recommandons de passer via Keplerk ou Digycode que PCS.
					
	
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
                    <h5 class="modal-title" id="modal-standard-title">Délais de confirmations</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="modal-body">
                    <p>Lorsque le volume d’activité sur le<b> réseau Bitcoin </b>est plus conséquent,
					les mineurs ont alors d'avantage de travail et ta <b>transaction prend place dans une file d’attente</b>
					plus ou moins longue avant d’être traitée (en générale sous 30 mins). 
					Si <b>tes frais de transactions sont plus élevés</b> que les autres opérations en cours,
					alors les mineurs seront amenés à la traiter <b>en priorité</b>. 
					Sinon, il faudra attendre et prendre son mal en patience.<br><br>
					
					En temps normal une confirmation met <b>entre 10-120 minutes</b> si le réseau blockchain est stable, 
					mais peut également prendre des heures si le réseau est saturé (quoi qu'il en soit ton wallet
					MesrineCC sera crédité une fois que <b>tu aura atteint 1 confirmation</b> du réseau).
					<br><br>
					<small>Si la transaction met trop de temps, tu peux aussi re-dépoeer un autre paiement en générant une nouvelle adresse de dépôt.</small>
	
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
                    <h5 class="modal-title" id="modal-standard-title">Informations sur le wallet MesrineCC</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="modal-body">
                    <p>Une fois ton argent déposé sur le wallet de ton compte MesrineCC il n'est plus possible de le retirer. <br><br>
					Les dépôts sur notre site sont définitifs, il est inutile de réclamer un remboursement de votre wallet au support ou sur telegram.
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