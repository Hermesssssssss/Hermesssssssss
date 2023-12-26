<?php
$banPage = true;
$page = "Visualisation log ppl";
require '!#/init.php';
require '!#/header1.php';

$adminpage = true;
if($user["admin"] < "2") {
	redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue. (accès non autorisée)','dashboard');
    exit;
}

if ( !checkArray($_GET,['id']) or !checkIfEmptyArray($_GET,['id']) ) {
    redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue. (accès non autorisée)','dashboard');
    exit;
}

$idCC = htmlspecialchars($_GET['id']);
$ccQuery = $odb->prepare('SELECT * FROM paypal WHERE id = :idCC');
$ccQuery->execute(['idCC'=>$idCC]);
$cc = $ccQuery->fetch();

$ccQuery2 = getData($odb, 'paypal',['id'=>intval($idCC)]);
        if ( count($ccQuery2) == 0 )
			redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue. (accès non autorisée)','dashboard');

if ( checkArray($_POST,['revendre'])) {
        $data = protectArray($_POST);
					addLog($odb, $user["id"], "PP - Remise en vente ID : $idCC");
                    
                    $query = $odb->prepare("UPDATE paypal SET status = 0, user = 0, checker = 0, refund = 0 WHERE id = ?");
					$query->execute(array(intval($idCC)));
					redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Carte remis en vente avec succès.',$actual_link);
          
    
}

if ( checkArray($_POST,['modif'])) {
    $data = protectArray($_POST);
	$sqli = $odb->prepare("UPDATE paypal SET email = ?, pass = ?, titulaire = ?, adresse = ?, cp = ?, ville = ?, pays = ?, num = ?, dob = ?, prix = ?, base = ? WHERE id = ?");
	$sqli->execute(array($data["email"],$data["pass"],$data["titulaire"],$data["adresse"],$data["cp"],$data["ville"],$data["pays"],$data["num"],$data["dob"],$data["prix"],$data["base"], $idCC)); 
	redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Modification effectuée.',$actual_link);
          
}

if ( checkArray($_POST,['refund'])) {
	$data = protectArray($_POST);
	if($cce["refund"] != "0") {
		redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Cette carte est déjà vérifié / refund.',$actual_link);
	    exit;
	}
	$query = $odb->prepare("UPDATE `paypal` SET `checker` = '2', `dateCheck` = ?, `refund` = '1' WHERE `id` = ?");
	$query->execute(array(time(),$idCC));
	addBalance($odb,$cc['user'],$cc["prix"],'pp dead #'.$cc['id'],$user["username"]);
	addLog($odb, $user["id"], "PP - Marqué DEAD (refund auto) (ID #$idCC)");    
	redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, CC marqué comme DEAD et l\'utilisateur vient d\'être refund.',$actual_link);	
} 
?>

 
                        <div class="row card-group-row">
                           
							
							 <div class="col-md-12">
                                <form action="" method="POST">
								<div class="card">
                            <div class="card-header card-header-large bg-white">
                                <h4 class="card-header__title">Compte #<?php echo $cc["id"]; ?> <br>
								<?php if($cc["status"] == "1") { 
								
								switch($cc["checker"]) {
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
		}?>
								<span class="badge badge-soft-success">Vendu</span> 
								<span class="badge badge-soft-primary">Acheteur : <?php echo getUserFromId($odb, $cc["user"])["username"]; ?></span>
								<span class="badge badge-soft-warning">Date d'achat : <?php echo date("d/m H:i:s", $cc["dateAchat"]); ?></span>
								<br><br>
								<span class="badge badge-<?php echo $chkr; ?></span>
								<span class="badge badge-soft-primary">Date check : <?php echo @date("d/m H:i:s", $cc["dateCheck"]); ?></span>
								<span class="badge badge-soft-secondary">Refund : <?php if($cc["refund"] == 0) echo "Non"; else echo "Oui"; ?></span>
								
								<br><br>
								<input type="hidden" name="csrf" value="<?php echo $token ?>">

								<button name="revendre" type="submit" class="btn btn-sm btn-primary">Remettre en vente</button>
								<button name="refund" type="submit" class="btn btn-sm btn-warning">Marquer DEAD + REFUND</button>
								</form>
								<?php } else {  ?><span class="badge badge-soft-secondary">En vente</span><?php } ?>
								</h4>
                            </div>
                        
                                      
<form action="" method="POST">
					<div class="col-lg-8 card-form__body card-body">
					<div class="row">
					<div class="col-md-6">
                                    <div class="form-group">
                                        <label for="opass">Adresse mail</label>
                                        <input name="email" id="opass" type="text" class="form-control" value="<?php echo $cc["email"]; ?>" required>
                                    </div>
									</div>
									<div class="col-md-6">
                                    <div class="form-group">
                                        <label for="npass">Mot de passe</label>
                                        <input name="pass" id="npass" type="text" class="form-control" value="<?php echo $cc["pass"]; ?>" required">
                                    </div>
									</div>
									</div>
									
									<div class="row">
					<div class="col-md-6">
                                    <div class="form-group">
                                        <label for="opass">Pays</label>
                                        <input name="pays" id="opass" type="text" class="form-control" value="<?php echo $cc["pays"]; ?>">
                                    </div>
									</div>
									<div class="col-md-6">
                                    <div class="form-group">
                                        <label for="npass">Titulaire</label>
                                        <input name="titulaire" id="npass" type="text" class="form-control" value="<?php echo $cc["titulaire"]; ?>">
                                    </div>
									</div>
									</div>
									<div class="row">
					<div class="col-md-6">
                                    <div class="form-group">
                                        <label for="opass">Adresse</label>
                                        <input name="adresse" id="opass" type="text" class="form-control" value="<?php echo $cc["adresse"]; ?>">
                                    </div>
									</div>
									<div class="col-md-6">
                                    <div class="form-group">
                                        <label for="npass">Code postal</label>
                                        <input name="cp" id="npass" type="text" class="form-control" value="<?php echo $cc["cp"]; ?>">
                                    </div>
									</div>
									</div>
									
									<div class="row">
					<div class="col-md-6">
                                    <div class="form-group">
                                        <label for="opass">Ville</label>
                                        <input name="ville" id="opass" type="text" class="form-control" value="<?php echo $cc["ville"]; ?>">
                                    </div>
									</div>
									<div class="col-md-6">
                                    <div class="form-group">
                                        <label for="npass">Téléphone</label>
                                        <input name="num" id="npass" type="text" class="form-control" value="<?php echo $cc["num"]; ?>">
                                    </div>
									</div>
									</div>
									<div class="row">
					<div class="col-md-6">
                                    <div class="form-group">
                                        <label for="opass">Date de naissance</label>
                                        <input name="dob" id="opass" type="text" class="form-control" value="<?php echo $cc["dob"]; ?>">
                                    </div>
									</div>
									<div class="col-md-6">
                                    <div class="form-group">
                                        <label for="npass">Base</label>
                                        <input name="base" id="npass" type="text" class="form-control" value="<?php echo $cc["base"]; ?>" required">
                                    </div>
									</div>
									</div>
									<div class="row">
					<div class="col-md-12">
                                    <div class="form-group">
                                        <label for="opass">Prix <small>(en €)</small></label>
                                        <input name="prix" id="opass" type="text" class="form-control" value="<?php echo $cc["prix"]; ?>"  required>
                                    </div>
									</div>
									
									</div>
                                    <div class="form-group">
                 <button class="btn btn-primary" name="modif" type="submit">Modifier</button></div>
                                </div>
			<input type="hidden" name="csrf" value="<?php echo $token ?>">

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