<?php
$banPage = true;
$page = "Comptes PayPal";
require '!#/init.php';
require '!#/header1.php';

if ( checkArray($_POST,['buy'], ['idCC']) and checkIfEmptyArray($_POST,['idCC'])) {
    $data = protectArray($_POST);
    $dataResp = acheterPP($odb, $_SESSION['id'],$data['idCC']);
    redirectWithMessage($dataResp['status'],$dataResp['message'],$actual_link);
}


$curPage = isset($_GET['page'])?$_GET['page']:1;
$_GET = protectArray($_GET);

$_GET['user']= '0';
$_GET['dob'] = substr(isset($_GET['dob'])?$_GET['dob']:'',0,4);

$cards = getData($odb, 'paypal',$_GET,isset($_GET['orderColumn'])?$_GET['orderColumn']:(isset($_GET['page'])?null:'RAND()'),isset($_GET['order'])?$_GET['order']:'');
$ccTotal = getCount($odb, 'paypal',$_GET);

if(empty($ccTotal))
	$ccTotal = 0;


?>

 
                    
                        <div class="row card-group-row">
                           
							
							 <div class="col-md-12">
							 <button data-toggle="modal" data-target="#modal-standard"  class="btn mb-2 btn-primary"><i class="material-icons">filter_list</i> Filtrer la recherche</button>
                                <div class="card">
                                   
				 <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Comptes PayPal disponible <span class="badge badge-soft-primary badge-pill mr-1"><?php echo $ccTotal; ?></span><br>
										<small class="text-muted">Parcours nos récents comptes PayPal. Car nous on va chercher l'argent la ou elle est.. C'est-à-dire dans les banques!</small></h4>
                                        
										</div>
											
					 
                    
					<div class="table-responsive" data-toggle="lists" data-lists-values="[&quot;js-lists-values-employee-name&quot;]">

                                <table class="table mb-0 thead-border-top-0 table-striped">
                                    <thead>
                                        <tr>

                                            <th style="width:20px">Adresse mail</th>
                                            
                                            <th style="width: 25px;">Pays</th>
                                            <th>Titulaire</th>
                                            <th style="width: 25px;">Code postal</th>
                                            <th style="width: 25px;">Date de naissance</th>
                                            <th style="width: 15px;">Téléphone</th>
                                            <th style="width: 15px;">Base</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                      <tbody class="list" id="companies">
<?php foreach ($cards as $cc ) {

list($mail1, $mail2) = explode("@", $cc["email"]);	?>
                                        <tr><form action="" method="POST">
                                            <td><span class="badge badge-soft-primary">
											<?php echo substr($cc["email"], 0, 5).'***@'.$mail2; ?></span></td>
                                         <td><?php  if($cc["pays"] == "0") echo '<i class="icon-16pt material-icons">block</i>'; else echo $cc["pays"]; ?></td>
                                            <td><small class="text-muted"><?php  if($cc["titulaire"] == "0") echo '<i class="icon-16pt material-icons">block</i>'; else echo $cc["titulaire"]; ?></small></td>
                                            <td><?php  if($cc["cp"] == "0") echo '<i class="icon-16pt material-icons">block</i>'; else echo $cc["cp"]; ?></td>
                                            <td><?php if($cc["dob"] == "0") echo '<i class="icon-16pt material-icons">block</i>'; else echo substr($cc["dob"], 6, 10); ?></td>
                                            <td><?php if($cc["num"] == "0") echo '<i class="icon-16pt material-icons">block</i>'; else echo substr($cc["num"], 0, 5).'****'; ?></td>
                                           <td><span class="badge badge-soft-secondary">
											#<?php echo $cc["base"]; ?></span></td>
                                            <td><button name="buy" class="btn btn-sm btn-primary">Acheter (<b><?php echo $cc["prix"]; ?>€</b>)</button></td>
                                            <input type="hidden" name="idCC" value="<?php echo $cc["id"]; ?>">
									        <input type="hidden" name="csrf" value="<?php echo $token ?>">
</form>
										</tr>
<?php } ?>

                                    </tbody>
                                </table>
                            </div>
							
							 
                                </div>
								<?php
        echo generatePagination($curPage,ceil($ccTotal/(isset($_GET['itemPerPage']) && is_numeric($_GET['itemPerPage'])?$_GET['itemPerPage']:10)),$actual_link) ?>
    
			</form>
							

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
<style>
.mesrine {
    font-size: .8rem;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: rgba(147,159,173,.84);
    line-height: .9375rem;
    margin-bottom: .5rem;
    font-weight: 700;
}
</style>
<form action="" method="GET">
<div id="modal-standard" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-standard-title">Filtre de recherche</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="col-lg-12 card-form_body card-body">
            
				  <div class="form-row">
				    <div class="col-12 mb-3">
                             <label class="mesrine">Adresse mail</label>
                    <input name="email" placeholder="@exemple.fr" class="form-control"  value="<?php echo isset($_GET['dob'])?$_GET['dob']:'' ?>"></div>
                  
					 
                  </div><!-- col -->
				  
				  <div class="form-row">
				  
                        <div class="col-6 mb-3">
                             <label class="mesrine">Pays</label>
                     <select name="pays" class="form-control tx-color-03">
                        <option value="">Tous</option>
                        <?php foreach(getDataWithoutPage($odb, 'paypal',['user'=>'0'],null,null,'pays') as $country) {
                            echo '<option '.(isset($_GET['pays']) && $_GET['pays'] == $country['pays']?'selected':'').' value="'.$country['pays'].'">'.$country['pays'].'</option>';
                        } ?>
                        </select>
					 </div>
					 
					  <div class="col-6 mb-3">
                             <label class="mesrine">Date de naissance</label>
                    <input name="dob" placeholder="1987" class="form-control"  value="<?php echo isset($_GET['dob'])?$_GET['dob']:'' ?>"></div>
                  
					 
                  </div><!-- col -->
				  
				    <div class="form-row">
                        <div class="col-6 mb-3">
                             <label class="mesrine">Code postal</label>
                     <input name="cp" placeholder="75015" class="form-control"  value="<?php echo isset($_GET['cp'])?$_GET['cp']:'' ?>"></div>
                  
					
					 
					  <div class="col-6 mb-3">
                             <label class="mesrine">Opérateur tel</label>
                    <select name="num" class="form-control tx-color-03">
                        <option value="">Tous</option>
                        <?php $arrayNums = [
                            'SFR' => '[0603,0609,0610,0611,0612,0613,0614,0615,0616,0617,0618,0619,0620,0621,0622,0623,0624,0625,0626,0627]',
                            'NRJ' => '[0606]',
                            'Orange' => '[0607,0608,0630,0632,0633,0634,0670,0671,0672,0673,0674,0675,0676,0677,0678,0679,0680,0681,0682,0683,0684,0685,0687,0688,0689]',
                            'Free' => '[0651]',
                            'La Banque Postal' => '[0641, 0644, 0656, 0657, 0658]',
                            'Bouygues Telecom' => '[0653,0660,0661,0662,0663,0664,0665,0666,0667,0668,0698,0699]'
                        ];
                        foreach ($arrayNums as $operator => $val ) {
                            echo '<option value="'.$val.'" '.(isset($_GET['num'])&&$_GET['num']==$val?'selected':'').'>'.$operator.'</option>';
                        }
                        ?>
					 </select>
					 </div>
                  </div><!-- col -->
				  
				   <div class="form-row">
				   <div class="col-6 mb-3">
                             <label class="mesrine">Base</label>
                     <select name="base" class="form-control tx-color-03">
                        <option value="">Toutes</option>
                        <?php foreach(getDataWithoutPage($odb, 'paypal',['user'=>'0'],null,null,'base') as $country) {
                            echo '<option '.(isset($_GET['base']) && $_GET['base'] == $country['base']?'selected':'').' value="'.$country['base'].'">'.$country['base'].'</option>';
                        } ?>
                        </select>
					 </div>
					 
					 
                        <div class="col-6 mb-3">
                             <label class="mesrine">Comptes par page</label>
							 <select name="itemPerPage" class="form-control tx-color-03">
                        <?php $arrayIPP = [
                            '10' => '10',
                            '25' => '25',
                            '50' => '50',
                            '75' => '75',
                            '100' => '100'
                        ];
                        foreach ($arrayIPP as $n ) {
                            echo '<option value="'.$n.'" '.(isset($_GET['itemPerPage'])&&$_GET['itemPerPage']==$n?'selected':'').'>'.$n.'</option>';
                        }
                        ?>
                    </select>
							 </div>
							 </div>
				  
				  
				  
				  
					
                </div> <!-- // END .modal-body -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Appliquer les filtres</button>
                </div> <!-- // END .modal-footer --></form>
            </div> <!-- // END .modal-content -->
        </div> <!-- // END .modal-dialog -->
    </div> <!-- // END .modal -->

</body>

</html>