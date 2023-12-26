<?php
$banPage = true;
$page = "Visualisation utilisateur";
require '!#/init.php';
require '!#/header1.php';

$adminpage = true;
if($user["admin"] < "1") {
	redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue. (accès non autorisée)','dashboard');
    exit;
}

if ( !checkArray($_GET,['id']) or !checkIfEmptyArray($_GET,['id']) ) {
    header('location: adm_users'); exit();
}

$userQ = getUserFromId($odb, $_GET['id']);

if($user["admin"] == "2") {
if ( checkArray($_POST,['admin','password','banRaison','balance'])) {
    $data = protectArray($_POST);
    
	if ( !empty($data['password']) ) {
        $queryUpdatePassword = $odb->prepare('UPDATE users SET password = :password WHERE id = :idUser');
        $queryUpdatePassword->execute([
            'password' => crypt($data['password'],'niqueletatniquelesbanquesjevousbz'),
            'idUser' => $userQ['id']
        ]);
		addLog($odb, $user["id"], "Admin User - Modification mdp (user : ".$userQ["username"].")");             
    }
	if ( !empty($data['banRaison']) ) {
        $queryUpdatePassword = $odb->prepare('UPDATE users SET banRaison = :password WHERE id = :idUser');
        $queryUpdatePassword->execute([
            'password' => $data['banRaison'],
            'idUser' => $userQ['id']
        ]);
		addLog($odb, $user["id"], "Admin User - Ban (user : ".$userQ["username"].")");
        
    }
	    $queryUpdatePasswor = $odb->prepare('UPDATE users SET admin = :password, balance = :balance WHERE id = :idUser');
        $queryUpdatePasswor->execute([
            'password' => $data['admin'],
            'balance' => $data['balance'],
            'idUser' => $userQ['id']
        ]);
		addLog($odb, $user["id"], "Admin User - Modif (user : ".$userQ["username"]." , balance : ".$userQ["balance"]." , admin : ".$userQ["admin"].")");
		redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Utilisateur modifié avec succès.',$actual_link);
}
}

if ( checkArray($_POST,['reachecker'])) {
	$data = protectArray($_POST);
	if($userQ["checkLock"] == "0") {
		redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Cet utilisateur a déjà accès au checker.',$actual_link);
	    exit;
	}
	$query = $odb->prepare("UPDATE `users` SET `checkLock` = '0' WHERE `id` = ?");
	$query->execute(array($userQ["id"]));
	$queryk = $odb->prepare("UPDATE `cards` SET `checker` = '0' WHERE `user` = ? ORDER BY dateCheck DESC LIMIT 5");
	$queryk->execute(array($userQ["id"]));
	$idmp = $userQ["username"];
	addLog($odb, $user["id"], "Checker - Unlock (USER $idmp)");    
	redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, L\'accès au checker pour cet utilisateur vient d\'être réactivée..',$actual_link);	
}  

if ( checkArray($_POST,['desachecker'])) {
	$data = protectArray($_POST);
	if($userQ["checkLock"] == "1") {
		redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Cet utilisateur a déjà l\'accès au checker désactivé.',$actual_link);
	    exit;
	}
	$query = $odb->prepare("UPDATE `users` SET `checkLock` = '1' WHERE `id` = ?");
	$query->execute(array($userQ["id"]));
	$idmp = $userQ["username"];
	addLog($odb, $user["id"], "Checker - Lock (USER $idmp)");    
	redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, L\'accès au checker pour cet utilisateur vient d\'être désactivée..',$actual_link);	
}  


$querySommeBtc = $odb->prepare("SELECT SUM(amount) FROM `DepotBTC` WHERE `user` = ?");
$querySommeBtc->execute(array($userQ['id']));
$sommetot = $querySommeBtc->fetchColumn();

if(empty($sommetot))
	$sommetot = 0;

$_GET = protectArray($_GET);

$curPageCC = isset($_GET['pageCC'])?$_GET['pageCC']:1;
$curPagePP = isset($_GET['pagePP'])?$_GET['pagePP']:1;
$curPageSMS = isset($_GET['pageSMS'])?$_GET['pageSMS']:1;
$curPageINC = isset($_GET['pageINC'])?$_GET['pageINC']:1;
$curPageBTC = isset($_GET['pageBTC'])?$_GET['pageBTC']:1;
$ccCount = getCount($odb,'cards',['user'=>$userQ["id"]]);
$ppCount = getCount($odb,'paypal',['user'=>$userQ["id"]]);
$smsCount = getCount($odb,'sms_histo',['user'=>$userQ["id"]]);
$incCount = getCount($odb,'paypal_inc',['user'=>$userQ["id"]]);
$btcCount = getCount($odb,'DepotBTC',['user'=>$userQ["id"]]);

$histo = getData($odb, 'cards',['user'=>$userQ["id"],'page'=>$curPageCC,'itemPerPage'=>9],'dateAchat','DESC');
$histod = getData($odb, 'paypal',['user'=>$userQ["id"],'page'=>$curPagePP,'itemPerPage'=>9],'dateAchat','DESC');
$histodd = getData($odb, 'sms_histo',['user'=>$userQ["id"],'page'=>$curPageSMS,'itemPerPage'=>9],'id','DESC');
$histoddd = getData($odb, 'paypal_inc',['user'=>$userQ["id"],'page'=>$curPageINC,'itemPerPage'=>9],'id','DESC');
$histodddd = getData($odb, 'DepotBTC',['user'=>$userQ["id"],'page'=>$curPageBTC,'itemPerPage'=>9],'id','DESC');

?>

 
                    
                        <div class="row card-group-row">
                           
							
							 <div class="col-md-6">

								<div class="card">
                            <div class="card-header card-header-large bg-white">
                                <h4 class="card-header__title">Utilisateur #<?php echo $userQ["id"]; ?> - <?php echo $userQ["username"]; ?>
								
								<br><span class="badge badge-pill badge-soft-primary">Inscrit le <?php echo date("d/m/Y à H:i", $userQ["register"]); ?></span>
<span class="badge badge-pill badge-soft-success">Dernière connexion le <?php echo date("d/m/Y à H:i", $userQ["last"]); ?></span>

</h4>
                            </div>
                        	
                         						   
<form action="" method="POST">
					<div class="col-lg-8 card-form__body card-body">
					<div class="form-row">
				   <div class="col-6 mb-3">
                                    <div class="form-group">
                                        <label for="opass">Mot de passe</label>
                                        <input name="password" id="opass" type="password" class="form-control" placeholder="" <?php if($user["admin"] != "2") echo 'disabled'; ?>>
                                    </div>
									</div>
									
								<div class="col-6 mb-3">
                                    <div class="form-group">
                                        <label for="npass">Grade</label>
                                        <select  name="admin" id="npass" class="form-control" <?php if($user["admin"] != "2") echo 'disabled'; ?>>
										 <option value="0" <?php echo $userQ['admin'] == 0?'selected':'' ?>>Membre</option>
                                         <option value="1" <?php echo $userQ['admin'] == 1?'selected':'' ?>>Support</option>
                                        <option value="2" <?php echo $userQ['admin'] == 2?'selected':'' ?>>Admin</option>
                       
										</select>
                                    </div>
									</div>
									</div>
									
									<div class="form-row">
				   <div class="col-6 mb-3">
                                    <div class="form-group">
                                        <label for="cpass">Balance <small>(en €)</small></label>
                                        <input name="balance" id="cpass" type="text"  value="<?php echo $userQ["balance"]; ?>" class="form-control" <?php if($user["admin"] != "2") echo 'disabled'; ?>>
                                    </div>
									</div>
									
									 <div class="col-6 mb-3">
                                    <div class="form-group">
                                        <label for="cpass">Bannir <small>(raison)</small></label>
                                        <input name="banRaison" type="text" class="form-control" value="<?php if($userQ["banRaison"] != "0") echo  $userQ["banRaison"]; ?>" placeholder="" <?php if($user["admin"] != "2") echo 'disabled'; ?>>
                                    </div>
									</div>
									
									</div>
									
									<div class="form-row">
				   <div class="col-6 mb-3">
                                    <div class="form-group">
                                  
			<input type="hidden" name="csrf" value="<?php echo $token ?>">
 <button type="submit" name="update" class="btn btn-primary" <?php if($user["admin"] != "2") echo 'disabled'; ?>>Modifier</button>
                       </div>
						</form>
                        </div>
                   

                                </div>
                            </div>
							
                                </div>
								</div>
								
								
								<div class="col-md-6">
								<div class="card">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title">Informations supplémentaires</h4>
                                      </div>
                                    <div class="card-body py-0">
                                        <div class="list-group list-group-small list-group-flush">

                                            <?php switch($userQ["checkLock"]) {
												case '0':
												     $clock = 'primary">Activé';
													 break;
												case '1':
												     $clock = 'danger">Désactivé';
													 break;
											}
											?><form action="" method="POST">
											<div class="list-group-item d-flex align-items-center px-0">
											
                                                <div class="mr-3 flex">Accès au checker</div>
                                                <div><span class="badge badge-soft-<?php echo $clock; ?></span>
												
												<input type="hidden" name="csrf" value="<?php echo $token ?>">
												<?php if($userQ["checkLock"] == "0") { ?>
												<button type="submit" class="btn btn-sm ml-2 btn-danger" name="desachecker">Désactiver</button></div>
                                                <?php } else { ?>
												<button type="submit" class="btn btn-sm ml-2 btn-success" name="reachecker">Activer</button></div>
                                                <?php } ?>
											</div></form>
											
											<div class="list-group-item d-flex align-items-center px-0">
                                                <div class="mr-3 flex">Dépôts total</div>
                                                <div>
												<span class="badge badge-soft-primary"><?php echo $sommetot; ?>€</span>
												<span class="badge badge-soft-secondary"><?php echo getCount($odb, 'DepotBTC',['user'=>$userQ["id"]]); ?> dépôts</span></div>
                                            </div>
											<div class="list-group-item d-flex align-items-center px-0">
                                                <div class="mr-3 flex">Cartes achetées</div>
                                                <div>
												<span class="badge badge-soft-primary"><?php echo getCount($odb, 'cards',['user'=>$userQ["id"]]); ?></span>
												<span class="badge badge-soft-success"><?php echo getCount($odb, 'cards',['checker'=>'1', 'user'=>$userQ["id"]])+getCount($odb, 'cards',['checker'=>'0', 'user'=>$userQ["id"]]); ?>  LIVE</span>
												<span class="badge badge-soft-danger"><?php echo getCount($odb, 'cards',['checker'=>'2', 'user'=>$userQ["id"]]); ?> DEAD</span>
												
												</div>
                                            </div>
											
											<div class="list-group-item d-flex align-items-center px-0">
                                                <div class="mr-3 flex">Logs PPL achetées</div>
                                                <div>
												<span class="badge badge-soft-primary"><?php echo getCount($odb, 'paypal',['user'=>$userQ["id"]]); ?></span>
												<span class="badge badge-soft-danger"><?php echo getCount($odb, 'paypal',['checker'=>'2','user'=>$userQ["id"]]); ?> DEAD</span>
												</div>
                                            </div>
											
											<div class="list-group-item d-flex align-items-center px-0">
                                                <div class="mr-3 flex">Refund</div>
                                                <div>
												<span class="badge badge-soft-primary"><?php echo getCount($odb, 'cards',['refund'=>'1', 'user'=>$userQ["id"]]); ?> CC</span>
												<span class="badge badge-soft-primary"><?php echo getCount($odb, 'paypal',['refund'=>'1', 'user'=>$userQ["id"]]); ?> PP</span>
												</div>
                                            </div>
											
											<div class="list-group-item d-flex align-items-center px-0">
                                                <div class="mr-3 flex">Outils utilisés</div>
                                                <div>
												<span class="badge badge-soft-primary"><?php echo getCount($odb, 'sms_histo',['user'=>$userQ["id"]]); ?> sms envoyé</span>
												<span class="badge badge-soft-primary"><?php echo getCount($odb, 'paypal_inc',['user'=>$userQ["id"]]); ?> inc-bypass</span>
												</div>
                                            </div>
											


                                        </div>
                                    </div>
                                   
                                </div>
								</div>
								
							
                           
							
							 <div class="col-md-12">
							 	<div class="card card-form">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Historique des achats</h4>
                                        
										</div>
                              <div class="card-header card-header-tabs-basic nav" role="tablist">
                                        <a href="#cc" class="active" data-toggle="tab" role="tab" aria-controls="cc" aria-selected="true">Achats CC</a>
                                        <a href="#pp" data-toggle="tab" role="tab" aria-controls="pp" aria-selected="false" class="">Achats PP </a>
                                        <a href="#sms" data-toggle="tab" role="tab" aria-controls="sms" aria-selected="false" class="">SMS </a>
                                        <a href="#inc" data-toggle="tab" role="tab" aria-controls="inc" aria-selected="false" class="">PP-INC</a>
                                    </div>
                                    <div class="list-group tab-content list-group-flush">
                                        <div class="tab-pane fade active show" id="cc">
                                    <div  class="table-responsive border-bottom" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                              
                                        <table class="table mb-0 table-striped thead-border-top-0">
                                            <thead>
                                                <tr>
                                                    
                                                    <th style="width: 10px;">ID</th>
                                                    <th style="width: 100px;">Bin</th>
                                                    <th style="width: 100px;">Base</th>
                                                    <th style="width: 37px;">Date d'achat</th>
                                                    <th style="width: 37px;">Date checker</th>
                                                    <th style="width: 37px;">Status</th>
                                                    <th style="width: 37px;">Checker debug</th>
                                                    <th style="width: 51px;">Prix </th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody class="list" id="staff02">
											<?php 
											foreach($histo as $log) { 
											switch($log["checker"]) {
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
											<tr>

                                                    <td><small class="text-muted"><a href="./adm_card?id=<?php echo $log["id"]; ?>">#<?php echo $log["id"]; ?></a></small></td>
                                                    <td><small class="text-muted"><?php echo substr($log["ccnum"], 0, 6); ?></small></td>
                                                    <td><span class="badge badge-soft-secondary">#<?php echo $log["base"]; ?></span></td>
                                                    <td><small class="text-muted"><?php echo date("d/m H:i:s", $log["dateAchat"]); ?></small></td>
                                                    <td><small class="text-muted"><?php echo @date("d/m H:i:s", $log["dateCheck"]); ?></small></td>
                                                    <td><span class="badge badge-<?php echo $chkr; ?></span></td>
                                                    <td><small class="text-muted"><?php echo $log["check_debug"]; ?></small></td>
                                                    
                                                    <td><?php echo $log["prix"]; ?>€</td>
                                                    
                                                </tr>
												
			
											<?php } ?>
												</tbody>
                                        </table>
										 <div class="ml-3 mt-3"><?php echo generatePagination($curPageCC,ceil($ccCount/9),$actual_link,'pageCC') ?>
		

                                         </div>
                                    </div></div>
									
									 <div class="tab-pane fade" id="pp">
                                    <div  class="table-responsive border-bottom" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                              
                                        <table class="table mb-0 table-striped thead-border-top-0">
                                            <thead>
                                                <tr>
                                                    
                                                    <th style="width: 10px;">ID</th>
                                                    <th style="width: 100px;">Email</th>
                                                    <th style="width: 100px;">Base</th>
                                                    <th style="width: 37px;">Date d'achat</th>
                                                    <th style="width: 37px;">Date checker</th>
                                                    <th style="width: 37px;">Status</th>
                                                    <th style="width: 51px;">Prix </th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody class="list" id="staff02">
											<?php 
											foreach($histod as $log) { 
											switch($log["checker"]) {
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
											<tr>

                                                    <td><small class="text-muted"><a href="./adm_pp?id=<?php echo $log["id"]; ?>">#<?php echo $log["id"]; ?></a></small></td>
                                                    <td><small class="text-muted"><?php echo $log["email"]; ?></small></td>
                                                    <td><span class="badge badge-soft-secondary">#<?php echo $log["base"]; ?></span></td>
                                                    <td><small class="text-muted"><?php echo date("d/m H:i:s", $log["dateAchat"]); ?></small></td>
                                                    <td><small class="text-muted"><?php echo @date("d/m H:i:s", $log["dateCheck"]); ?></small></td>
                                                    <td><span class="badge badge-<?php echo $chkr; ?></span></td>
                                                    
                                                    <td><?php echo $log["prix"]; ?>€</td>
                                                    
                                                </tr>
												
			
											<?php } ?>
												</tbody>
                                        </table>
										 <div class="ml-3 mt-3"><?php echo generatePagination($curPagePP,ceil($ppCount/9),$actual_link,'pagePP') ?>
		
                                    </div>
                                    </div>
</div>


									 <div class="tab-pane fade" id="sms">
                                    <div  class="table-responsive border-bottom" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                              
                                        <table class="table mb-0 table-striped thead-border-top-0">
                                            <thead>
                                                <tr>
                                                    
                                                    <th style="width: 10px;">ID</th>
                                                    <th style="width: 100px;">Expéditeur</th>
                                                    <th style="width: 100px;">Destinataire</th>
                                                    <th>Message</th>
                                                    <th style="width: 37px;">Date</th>
                                                    <th style="width: 51px;">Prix </th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody class="list" id="staff02">
											<?php 
											foreach($histodd as $log) { 
													
											
											
										
											?>
											<tr>

                                                    <td><small class="text-muted">#<?php echo $log["id"]; ?></small></td>
                                                    <td><small class="text-muted"><?php echo $log["expediteur"]; ?></small></td>
                                                    <td><small class="text-muted"><?php echo $log["destinataire"]; ?></small></td>
                                                    <td><small class="text-muted"><?php if($user["admin"] == "1") echo "caché"; else echo $log["msg"]; ?></small></td>
                                                    <td><small class="text-muted"><?php echo @date("d/m H:i:s", $log["date"]); ?></small></td>
                                                    
                                                    <td><?php echo $log["prix"]; ?>€</td>
                                                    
                                                </tr>
												
			
											<?php } ?>
												</tbody>
                                        </table>
										 <div class="ml-3 mt-3"><?php echo generatePagination($curPageSMS,ceil($smsCount/9),$actual_link,'pageSMS') ?>
		
                                    </div>
                                    </div>


                                </div>
								
								
									 <div class="tab-pane fade" id="inc">
                                    <div  class="table-responsive border-bottom" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                              
                                        <table class="table mb-0 table-striped thead-border-top-0">
                                            <thead>
                                                <tr>
                                                    
                                                    <th style="width: 10px;">ID</th>
                                                    <th style="width: 100px;">Numéro</th>
                                                    <th style="width: 100px;">Status</th>
                                                    <th style="width: 40px;">Code</th>
                                                    <th style="width: 37px;">Date</th>
                                                    <th style="width: 51px;">Prix </th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody class="list" id="staff02">
											<?php 
											foreach($histoddd as $log) { 
											
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

                                                    <td><small class="text-muted">#<?php echo $log["id"]; ?></small></td>
                                                    <td><small class="text-muted"><?php echo $log["target"]; ?></small></td>
                                                    <td><span class="badge badge-soft-<?php echo $status; ?></span></td>
                                                    <td><small class="text-muted"><?php echo $code; ?></small></td>
                                                    <td><small class="text-muted"><?php echo @date("d/m H:i:s", $log["date"]); ?></small></td>
                                                    
                                                    <td><?php echo $log["prix"]; ?>€</td>
                                                    
                                                </tr>
												
			
											<?php } ?>
												</tbody>
                                        </table>
										 <div class="ml-3 mt-3"><?php echo generatePagination($curPageINC,ceil($incCount/9),$actual_link,'pageINC') ?>
		
                                    </div>
                                    </div>

                                </div>
							
								
								</div>
							 </div>
							 
							 <div class="card">
							 
							 <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Historique des dépôts</h4>
                                        
										</div>
							 <div  class="table-responsive border-bottom" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                              
                                        <table class="table mb-0 table-striped thead-border-top-0">
                                            <thead>
                                                <tr>
                                                    
                                                    <th style="width: 10px;">ID</th>
                                                    <th style="width: 100px;">Adresse BTC</th>
                                                    <th style="width: 100px;">Status</th>
                                                    <th style="width: 37px;">Date</th>
                                                    <th style="width: 37px;">Montant</th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody class="list" id="staff02">
											<?php 
											foreach($histodddd as $log) { 
															switch($log["status"]) {
	case '0':
	      $status = 'secondary">En attente de paiement';
		  break;
	case '1':
	      $status = 'warning">En attente de confirmation (0/1)';
		  break;
	case '2':
	      $status = 'success">Credité';
		  break;
	case '3':
	      $status = 'danger">Expiré';
		  break;
	default:
	      $status = 'danger">Erreur';
		  break;
}
											
										
											?>
											<tr>

                                                    <td><small class="text-muted">#<?php echo $log["id"]; ?></small></td>
                                                    <td><small class="text-muted"><a href="<?php echo $log["txid"]; ?>"><?php echo $log["address"]; ?></a></small></td>
                                                    <td><span class="badge badge-soft-<?php echo $status; ?></span></td>
                                                    <td><small class="text-muted"><?php echo @date("d/m H:i:s", $log["date"]); ?></small></td>
                                                    
                                                    <td><?php echo $log["amount"]; ?>€</td>
                                                    
                                                </tr>
												
			
											<?php } ?>
												</tbody>
                                        </table>
										 <div class="ml-3 mt-3"><?php echo generatePagination($curPageBTC,ceil($btcCount/9),$actual_link,'pageBTC') ?>
		
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