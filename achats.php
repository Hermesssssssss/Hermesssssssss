<?php
$banPage = true;
$page = "Mes achats";
require '!#/init.php';
require '!#/header1.php';

$curPageCC = isset($_GET['pageCC'])?$_GET['pageCC']:1;
$curPagePP = isset($_GET['pagePP'])?$_GET['pagePP']:1;
$cards = getData($odb, 'cards',['user'=>$_SESSION['id'],'page'=>$curPageCC,'itemPerPage'=>9],'dateAchat','DESC');
$cardsCount = getCount($odb, 'cards',['user'=>$_SESSION['id']],'dateAchat','ASC');
$pps = getData($odb, 'paypal',['user'=>$_SESSION['id'],'page'=>$curPagePP,'itemPerPage'=>9],'dateAchat','DESC');
$ppsCount = getCount($odb, 'paypal',['user'=>$_SESSION['id']],'dateAchat','ASC');


if (isset($_POST['check'])){
    $data = addCcToCheck($odb, $_POST['idCC'], $prixcheck, $checktime, $abuschecker);
	file_get_contents("https://onlira.fr/MesrineCC/gateway/gate-checker.php");
    redirectWithMessage($data['status'],$data['detail'],$actual_link);
	
}
?>
<style>
.mesrine {
    font-size: .9rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    line-height: .9375rem;
    font-weight: 600;
}

.mess {
    font-size: .8rem;
    text-transform: uppercase;
    letter-spacing: 1.0px;
    color: rgba(147,159,173,.84);
    line-height: .9375rem;
    margin-bottom: -1px;
    font-weight: 600;
}
</style> 
                    
                        <div class="row card-group-row">
                           
							
							 <div class="col-md-12">
                               <div class="card">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                       <h4 class="card-header__title flex m-0">Mes achats</span><br>
										<small class="text-muted">Parcours tes achats que tu as effectué sur notre shop, si tu as besoin d'aide un onglet a été conçu spécialement. </small></h4>
                                        
                                    </div>
                                    <div class="card-header card-header-tabs-basic nav" role="tablist">
                                        <a href="#cc" class="active" data-toggle="tab" role="tab" aria-controls="activity_all" aria-selected="true">Cartes bancaire</a>
                                        <a href="#pp" data-toggle="tab" role="tab" aria-selected="false">Comptes PayPal</a>
                                        <a href="#aide" data-toggle="tab" role="tab" aria-selected="false">Aide</a>
                                       
									   
                                    </div>
                                    
                                </div>
								
								
								<div class="tab-content">
                                        <div class="tab-pane active show fade" id="cc">
                                           <?php if($cardsCount == "0") {
											   ?>
											   <div class="alert alert-warning">
											   Tu n'as effectué aucun achats de carte bancaire.
											   </div>
										   <?php } foreach ($cards as $cc) {
?>			 
			 <div class="card py-2 px-3 my-4">
                                    <div class=" d-flex justify-content-between align-items-center">

                                        <a  onclick="myFunction<?php echo $cc["id"]; ?>()" class="flex d-flex align-items-center text-body text-underline-0">
                                            <span class="avatar avatar-sm mr-2">
                                                <img src="assets/visa.png" style="width:28px;height:20px;border-radius:4px;margin-top:9px" class="shadow">
                                            </span>
                                            <span class="flex d-flex flex-column">
                                                <strong>#<?php echo $cc["id"]; ?>
												<span class="badge badge-pill badge-soft-info"><?php echo substr($cc["ccnum"], 0, 6); ?></span>
												<span class="badge badge-pill badge-soft-info"><?php echo $cc["type"].' '.$cc["level"]; ?></span>
												<?php if ($cc['checker'] == "1") { ?>
                          <span class="badge badge-soft-success badge-pill ml-auto">LIVE</span>
                      <?php } elseif ($cc['checker'] == "2") { ?>
					 
					  <span class="badge badge-pill badge-soft-danger ml-auto">DEAD (refund)</span>
                      <?php } elseif ($cc['checker'] == "3") { ?>
                          <span class="badge badge-pill badge-soft-warning ml-auto">ERREUR</span>
                      <?php } elseif ($cc['checker'] == "4") { ?>
                          <span class="badge badge-pill badge-dark ml-auto">CHECK EN COURS</span>
						  <?php } elseif ( ($cc['dateAchat']+$checktime) < time()  ) { ?>
                          <span class="badge badge-pill badge-soft-secondary ml-auto">HORS DÉLAIS</span>
                      <?php } elseif ($cc['checker'] == "0") { ?>
					  
												<span class="badge badge-pill badge-soft-secondary">NON CHECK</span>
					  <?php } ?>
												</strong><small class="text-muted text-uppercase"><i class="material-icons icon-16pt mr-1 text-muted">alarm</i> 
												Achetée le <?php echo date("d/m/Y à H:i", $cc["dateAchat"]); ?></small>
                                            </span>
                                        </a>
                                        <div>
                                            <a id="myDIV<?php echo $cc["id"]; ?>" onclick="myFunction<?php echo $cc["id"]; ?>()" class="btn btn-soft-info btn-rounded-social">
                                                <i class="material-icons">add_circle_outline</i>
                                            </a>
											
											<a id="myDIV3<?php echo $cc["id"]; ?>" onclick="myFunction<?php echo $cc["id"]; ?>()" style="display:none" class="btn btn-soft-info btn-rounded-social">
                                                <i class="material-icons">close</i>
                                            </a>
                                        </div>
										<script>
function myFunction<?php echo $cc["id"]; ?>() {
  var x = document.getElementById("myDIV<?php echo $cc["id"]; ?>");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
  
  var x = document.getElementById("myDIV2<?php echo $cc["id"]; ?>");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
  
  var x = document.getElementById("myDIV3<?php echo $cc["id"]; ?>");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}
</script>
										
										</div>
										
										<div id="myDIV2<?php echo $cc["id"]; ?>" style="display:none">
										<hr style="opacity:.3">
									 <?php if ($cc['checker'] == "0" && ($cc['dateAchat']+$checktime) > time()) { ?>
										<form action="" method="POST">
										     <input type="hidden" name="csrf" value="<?php echo $token ?>">
                          <input type="hidden" value="<?php echo htmlspecialchars($cc['id']); ?>" name="idCC">
                         
										<button style="width:100%" type="submit" name="check" class="btn mb-3 btn-outline-primary"> <i class="material-icons">autorenew</i> Checker la carte</button>
										
										</form>
									 <?php } ?>
										<div class="card card-body bg-gray shadow-none border mb-3">
                                        <ul class="list-unstyled ml-1 mb-0">
                                            <li class="d-flex mesrine align-items-center pb-1">
											<i class="material-icons icon-24pt mr-2">credit_card</i> 
											<?php echo $cc["ccnum"]; ?>
											</li>
                                            <li class="d-flex mesrine align-items-center pb-1">
											<i class="material-icons icon-24pt mr-2">date_range</i>
											<?php echo $cc["ccexp"]; ?></li>
                                            <li class="d-flex mesrine align-items-center"><i class="material-icons icon-24pt mr-2">code</i>
											<?php echo $cc["cccvc"]; ?></li>
											<hr style="opacity:.3;">
											
											<li class="d-flex mesrine align-items-center" style="margin-bottom:-4px"><i class="material-icons icon-24pt mr-2">account_balance</i>
											<?php echo $cc["banque"]; ?></li>
											
                                        </ul>
                                    </div>
									
									<small><span style="float:right;margin-bottom:-12px" class="badge badge-soft-primary">
																Base #<?php echo $cc["base"]; ?>
																</span></small>

									<div style="padding:none" class="table-responsive">
                                        <table class="table mb-0 thead-border-top-0">
                                            <thead class="bg-white">
                                                <tr>
                                                    <th colspan="2">
                                                        <a >Informations personnelles</a>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="list">
											<tr>
                                                    <td>
                                                        <div class="media align-items-center">
                                                            
                                                            <div class="media-body">
                                                                <strong><?php echo $cc["titulaire"]; ?></strong><br>
                                                                <span class="text-muted">
																<?php if($cc["adresse"] == "0") echo "Inconnu"; else echo $cc["adresse"]; ?><br>
																<?php if($cc["cp"] == "0") echo "Inconnu"; else echo $cc["cp"]; ?>, <?php if($cc["ville"] == "0") echo "Inconnu"; else echo $cc["ville"]; ?><br>
																<?php if($cc["pays"] == "0") echo "Inconnu"; else echo $cc["pays"]; ?>
																</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
												<tr>
                                                    <td>
                                                        <div class="media align-items-center">
                                                            
                                                            <div class="media-body">
                                                               <span class="text-muted">
																<i class="material-icons mr-1 icon-16pt">phone</i>
																<?php if($cc["num"] == "0") echo "Inconnu"; else echo $cc["num"]; ?><br>
																<i class="material-icons mr-1 icon-16pt">cake</i>
																Née le <?php if($cc["dob"] == "0") echo "Inconnu"; else echo $cc["dob"]; ?>
																</span><br>
																
																
																
																
																
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
												
												</tbody>
                                        </table>
                                    </div>
									</div>
									
                                	 </form>
									 </div>
										 <?php } ?>
											
							<?php echo generatePagination($curPageCC,ceil($cardsCount/9),$actual_link,'pageCC') ?>
		  
										
                                            </div>
										



										<div class="tab-pane fade" id="pp">
                                             <?php if($ppsCount == "0") {
											   ?>
											   <div class="alert alert-warning">
											   Tu n'as effectué aucun achats de logs paypal.
											   </div>
										   <?php }
										   foreach ($pps as $cc) {
?>			 
			 <div class="card py-2 px-3 my-4">
                                    <div class=" d-flex justify-content-between align-items-center">

                                        <a  onclick="myFunctiond<?php echo $cc["id"]; ?>()" class="flex d-flex align-items-center text-body text-underline-0">
                                            <span class="avatar avatar-sm mr-2">
                                                <img src="assets/pp.png" style="width:25px;height:25px;border-radius:4px;margin-top:9px" class="shadow">
                                            </span>
                                            <span class="flex d-flex flex-column">
                                                <strong>#<?php echo $cc["id"]; ?>
												<span class="badge badge-pill badge-soft-info"><?php 
												list($mailun, $domain) = explode("@", $cc["email"]);
												echo substr($cc["email"], 0, 6).'***@'.$domain; ?></span>
												<?php if ($cc['checker'] == "2") { ?>
					 
					  <span class="badge badge-pill badge-soft-danger ml-auto">DEAD (refund)</span>
                      <?php } ?>
												</strong><small class="text-muted text-uppercase"><i class="material-icons icon-16pt mr-1 text-muted">alarm</i> 
												Achetée le <?php echo date("d/m/Y à H:i", $cc["dateAchat"]); ?></small>
                                            </span>
                                        </a>
                                        <div>
                                            <a id="myDIV1<?php echo $cc["id"]; ?>" onclick="myFunctiond<?php echo $cc["id"]; ?>()" class="btn btn-soft-info btn-rounded-social">
                                                <i class="material-icons">add_circle_outline</i>
                                            </a>
											
											<a id="myDIV32<?php echo $cc["id"]; ?>" onclick="myFunctiond<?php echo $cc["id"]; ?>()" style="display:none" class="btn btn-soft-info btn-rounded-social">
                                                <i class="material-icons">close</i>
                                            </a>
                                        </div>
										<script>
function myFunctiond<?php echo $cc["id"]; ?>() {
  var x = document.getElementById("myDIV1<?php echo $cc["id"]; ?>");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
  
  var x = document.getElementById("myDIV22<?php echo $cc["id"]; ?>");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
  
  var x = document.getElementById("myDIV32<?php echo $cc["id"]; ?>");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}
</script>
										
										</div>
										
										<div id="myDIV22<?php echo $cc["id"]; ?>" style="display:none">
										<hr style="opacity:.3">
								
										<div class="card card-body bg-gray shadow-none border mb-3">
                                        <ul class="list-unstyled ml-1 mb-0">
                                            <li class="d-flex mesrine align-items-center pb-1">
											<i class="material-icons icon-24pt mr-2">email</i> 
											<?php echo $cc["email"]; ?>
											</li>
                                            <li class="d-flex mesrine align-items-center pb-1">
											<i class="material-icons icon-24pt mr-2">lock</i>
											<?php echo $cc["pass"]; ?></li>
											
                                        </ul>
                                    </div>
<small><span style="float:right;margin-bottom:-12px" class="badge badge-soft-primary">
																Base #<?php echo $cc["base"]; ?>
																</span></small>

									<div style="padding:none" class="table-responsive">
                                        <table class="table mb-0 thead-border-top-0">
                                            <thead class="bg-white">
                                                <tr>
                                                    <th colspan="2">
                                                        <a >Informations personnelles</a>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="list">
											<tr>
                                                    <td>
                                                        <div class="media align-items-center">
                                                            
                                                            <div class="media-body">
                                                                <strong><?php echo $cc["titulaire"]; ?></strong><br>
                                                                <span class="text-muted">
																<?php if($cc["adresse"] == "0") echo "Inconnu"; else echo $cc["adresse"]; ?><br>
																<?php if($cc["cp"] == "0") echo "Inconnu"; else echo $cc["cp"]; ?>, <?php if($cc["ville"] == "0") echo "Inconnu"; else echo $cc["ville"]; ?><br>
																<?php if($cc["pays"] == "0") echo "Inconnu"; else echo $cc["pays"]; ?>
																</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
												<tr>
                                                    <td>
                                                        <div class="media align-items-center">
                                                            
                                                            <div class="media-body">
                                                               <span class="text-muted">
																<i class="material-icons mr-1 icon-16pt">phone</i>
																<?php if($cc["num"] == "0") echo "Inconnu"; else echo $cc["num"]; ?><br>
																<i class="material-icons mr-1 icon-16pt">cake</i>
																Née le <?php if($cc["dob"] == "0") echo "Inconnu"; else echo $cc["dob"]; ?>
																</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
												
												</tbody>
                                        </table>
                                    </div>
									</div>
									
                                	 </form>
									 </div>
										 <?php } ?>
											
					
							<?php echo generatePagination($curPagePP,ceil($ppsCount/9),$actual_link,'pagePP') ?>
		  
								
											</div>
										
										<div class="tab-pane fade" id="aide">
                                          <div class="card">
                                            <div class="px-4 py-3">
                                                <div class="d-flex mb-1">
                                                    
                                                    <div class="flex">
                                                        <div class="d-flex align-items-center mb-1">
                                                            <strong class="text-15pt">Comment me faire rembourser une carte qui ne fonctionne pas ?</strong>
                                                        </div>
                                                        <div>
                                                            <p>C'est simple jeune cardeur! Il te suffit simplement d'utiliser le checker automatique de notre site dans le temps imparti (5 min après l'achat) après ce délai tu ne pourras plus utiliser le checker donc tu ne pourras pas te faire rembourser. Inutile de contacter le support si ta carte est "HORS DÉLAIS", pas de refund possible.
															</p></div>

                                                       
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
										
										 <div class="card">
                                            <div class="px-4 py-3">
                                                <div class="d-flex mb-1">
                                                    
                                                    <div class="flex">
                                                        <div class="d-flex align-items-center mb-1">
                                                            <strong class="text-15pt">Comment bien utiliser le checker ?</strong>
                                                        </div>
                                                        <div>
                                                            <p>Si tu ne sait pas utiliser un checker, suit bien cette astuce. Une fois que t'achète ta carte bancaire utilise la directement sur le site que tu veux passed. Si le paiement est refusé essaye un montant plus faible, si c'est le même résultat alors la clique sur "Checker la CC" pour te faire refund automatiquement.
															</div>

                                                       
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
										
										 <div class="card">
                                            <div class="px-4 py-3">
                                                <div class="d-flex mb-1">
                                                    
                                                    <div class="flex">
                                                        <div class="d-flex align-items-center mb-1">
                                                            <strong class="text-15pt">Checker désactivé que faire ?</strong>
                                                        </div>
                                                        <div>
                                                            <p>Il semblerait que tu abuses de l'utilisation de notre checker et que notre système ta détecté en tant que "Killeur" (ceux qui tue de manière volontaire une cc dans le but de se faire refund abusivement). Nous surveillons de très près notre validrate et le choix de la qualité de nos CC il est impossible d'avoir plus de 3 CC dead d'affilée, si tu pense que s'est une erreur contact <a href="./support">le support</a> qui examinera manuellement des vérifications et décidera oui ou non de te remettre l'accès au checker.
															</p></div>

                                                       
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
										
										<div class="card">
                                            <div class="px-4 py-3">
                                                <div class="d-flex mb-1">
                                                    
                                                    <div class="flex">
                                                        <div class="d-flex align-items-center mb-1">
                                                            <strong class="text-15pt">Compte PayPal invalide</strong>
                                                        </div>
                                                        <div>
                                                            <p>Nous remboursons uniquement les comptes PayPal ayant un email ou mot de passe invalide, en revanches les comptes INC nous ne sommes pas responsable (nous n'utilisons en aucuns cas, ni vérifions les comptes avant leurs mise en vente). Pour te faire rembourser contact le support avec une capture d'écran du message, <b>attention</b> ceux qui tentent de se faire refund abusivement (en changeant le mot de passe ou autre) votre compte se verra bannir.
															</p>

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