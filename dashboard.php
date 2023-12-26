<?php 
$banPage = true;
$page = "Tableau de bord";
require '!#/init.php';
require '!#/header1.php';

$news = $odb->query('SELECT * FROM news ORDER by id DESC LIMIT 5')->fetchAll();
$feeds = $odb->query('SELECT * FROM feedbacks WHERE status = 1 ORDER by id DESC LIMIT 5')->fetchAll();
$hier = new DateTime();
$hier->modify('-1 days');

$ccPerLevel = $odb->query('SELECT COUNT(*) as count, level FROM cards WHERE user=\'0\' GROUP BY level ORDER BY count DESC LIMIT 4')->fetchAll();


if ( checkArray($_POST,['type'],['msg'])) {
    if ( checkIfEmptyArray($_POST,['type'],['msg'])) {
        $data = protectArray($_POST);
				if( longueurEntre($data['msg'],2,160)) {
				if( longueurEntre($data['type'],1,1)) {
                    $query = $odb->prepare("INSERT INTO feedbacks VALUES (NULL, ?, ?, ?, UNIX_TIMESTAMP(), 0)");
					$query->execute(array($user['id'], $data['msg'], $data['type']));
	
					redirectWithMessage('soft-success','<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Ton feedback a été envoyé avec succès.',$actual_link);
               
				
			  }
                else {
                    redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Erreur interne.',$actual_link);
                }
			  }
                else {
                    redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Le format du message est invalide, il doit comprendre entre 4-160 caractères alphanumerique (exemple : MesrineCC).',$actual_link);
                }
    }
    else {
        redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue.',$actual_link);
    }
}
?>

 
                    
                        <div class="row card-group-row">
                            <div class="col-lg-4 col-md-6 card-group-row__col">
                                <div class="card card-group-row__card card-body card-body-x-lg" style="position: relative; padding-bottom: calc(80px - 1.25rem); overflow: hidden; z-index: 0;">
                                            <div class="card-header__title text-muted mb-2">Cartes bancaire</div>
											<div class="text-amount"><i class="material-icons icon-muted icon-40pt">credit_card</i> <?php echo countCards($odb); ?></div>
											
                                            <div class="chart" style="height: 80px; position: absolute; left: 0; right: 0; bottom: 0;"><div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
                                                <canvas id="cardChart" width="553" height="80" class="chartjs-render-monitor" style="display: block; width: 553px; height: 80px;"></canvas>
                                            </div>
                                        </div>
                            </div>
                            <div class="col-lg-4 col-md-6 card-group-row__col">
                               <div class="card card-group-row__card card-body card-body-x-lg" style="position: relative; padding-bottom: calc(80px - 1.25rem); overflow: hidden; z-index: 0;">
                                            <div class="card-header__title text-muted mb-2">Comptes PayPal</div>
											<div class="text-amount"><i class="material-icons icon-muted icon-40pt">dvr</i> <?php echo countPayPal($odb); ?></div>
											
                                            <div class="chart" style="height: 80px; position: absolute; left: 0; right: 0; bottom: 0;"><div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
                                                <canvas id="paypalChart" width="553" height="80" class="chartjs-render-monitor" style="display: block; width: 553px; height: 80px;"></canvas>
                                            </div>
                                        </div>
                            </div>
                            <div class="col-lg-4 col-md-12 card-group-row__col">
                                <div class="card card-group-row__card card-body card-body-x-lg" style="position: relative; padding-bottom: calc(80px - 1.25rem); overflow: hidden; z-index: 0;">
                                            <div class="card-header__title text-muted mb-2">Utilisateurs inscrit</div>
											<div class="text-amount"><i class="material-icons icon-muted icon-40pt">people</i> <?php echo countUsers($odb); ?></div>
											
                                            <div class="chart" style="height: 80px; position: absolute; left: 0; right: 0; bottom: 0;"><div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
                                                <canvas id="productsChart" width="553" height="80" class="chartjs-render-monitor" style="display: block; width: 553px; height: 80px;"></canvas>
                                            </div>
                                        </div>
										
                            </div>
                        </div>

                         <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Actualités et mises à jours</h4>
                                        </div>
                                      

					<div class=" projects-item mb-3">
                           <?php foreach($news as $new) { ?>
						   <div class="row ml-1 mr-1 mt-3 mb-3">
						   <div class="col-lg-12 mb-1 mb-sm-0">
                                <div class="text-dark-gray"><span class="badge badge-dark"><i style="font-size:10px" class="material-icons">event</i> &nbsp;
								<?php echo strftime('%A, %d %B %Y', $new["date"]); ?></span></div>
                            </div>
                            <div class="col-lg-12">
                                <div class="card m-0">
                                    <div class="px-4 py-3">
                                        <div class="row align-items-center">
                                            <div class="col" style="min-width: 300px">
                                                <div class="d-flex align-items-center">
                                                    <a href="#" class="text-body"><strong class="text-15pt mr-2"><i class="material-icons icon-muted icon-20pt">event_note</i>
													<?php echo htmlspecialchars($new['sujet']); ?></strong></a>
                                                    
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <small class="text-dark-gray mr-2"><?php echo htmlspecialchars($new['text']); ?></small>
                                                   
                                                </div>
                                            </div>
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
							</div>
							<?php } ?>
                        </div>
						

                                </div>
                            </div>
							
							 <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">Statistiques des cartes disponible</h4>
                                        </div>
                                      

					<div class="card-body d-flex align-items-center justify-content-center" style="height: 210px;">
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="chart" style="height: calc(210px - 1.25rem * 2);"><div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
                                                    <canvas id="locationDoughnutChart" data-chart-legend="#locationDoughnutChartLegend" width="197" height="170" class="chartjs-render-monitor" style="display: block; width: 197px; height: 170px;">
                                                        <span style="font-size: 1rem;" class="text-muted"><strong>Location</strong> doughnut chart goes here.</span>
                                                    </canvas>
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div id="locationDoughnutChartLegend" class="chart-legend chart-legend--vertical"><span class="chart-legend-item"><i class="chart-legend-indicator" style="background-color: #75d34a"></i>United States</span><span class="chart-legend-item"><i class="chart-legend-indicator" style="background-color: #ee405a"></i>United Kingdom</span><span class="chart-legend-item"><i class="chart-legend-indicator" style="background-color: #3099ff"></i>Germany</span><span class="chart-legend-item"><i class="chart-legend-indicator" style="background-color: #939fad"></i>India</span></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
								
								<div class="card mt-3">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                        <h4 class="card-header__title flex m-0">
										<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-standard" style="float:right">Ajouter un feedback</button>
										Derniers feedbacks</h4>
                                        </div>
                                      
<div class="list-group tab-content list-group-flush">

<?php foreach($feeds as $feed) { 

switch($feed["type"]) {
	case '1':
	        $sti = "credit_card";
			break;
	case '2':
	        $sti = "local_parking";
			break;
    default:
	        $sti = "help";
			break;
}
?>
                                            <div class="list-group-item list-group-item-action d-flex align-items-center ">
                                                <div class="avatar avatar-xs mr-3">
                                                    <span class="avatar-title rounded-circle  bg-purple">
                                                        <i class="material-icons"><?php echo $sti; ?></i>
                                                    </span>
                                                </div>


                                                <div class="flex">
                                                    <div class="d-flex align-items-middle">
                                  
                                                        <strong class="text-15pt mr-1"><?php echo getUserFromId($odb, $feed["user"])["username"]; ?>
														</strong>
														
														<small class="text-muted"><span class="badge badge-light"><?php echo date("d/m/Y", $feed["date"]); ?></span></small>
                                                    </div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($feed["feedback"]); ?></small>
                                                
												
												</div>
										</div>
										
<?php } ?>

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


<script>
/******/ (function(modules) { // webpackBootstrap
/******/ 	var installedModules = {};
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/ 		module.l = true;
/******/ 		return module.exports;
/******/ 	}
/******/
/******/ 	__webpack_require__.m = modules;
/******/ 	__webpack_require__.c = installedModules;
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/

/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 5);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/page.dashboard.js":

/***/ (function(module, exports) {

(function () {
  'use strict';

  $('[data-toggle="tab"]').on('hide.bs.tab', function (e) {
    $(e.target).removeClass('active');
  });
  Charts.init();

  var Products = function Products(id) {
    var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'line';
    var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
    var data = arguments.length > 3 ? arguments[3] : undefined;
    options = Chart.helpers.merge({
      elements: {
        line: {
          fill: 'start',
          backgroundColor: settings.charts.colors.area,
          tension: 0,
          borderWidth: 1
        },
        point: {
          pointStyle: 'circle',
          radius: 5,
          hoverRadius: 5,
          backgroundColor: settings.colors.white,
          borderColor: settings.colors.primary[700],
          borderWidth: 2
        }
      },
      scales: {
        yAxes: [{
          display: false
        }],
        xAxes: [{
          display: false
        }]
      }
    }, options);
    data = data || {
      labels: [<?php for ( $i = 7 ; $i >=0 ; $i--) { echo '\'Inscrits le '.date("d/m", strtotime('-'.$i.' days', time())).'\',';  } ?>],
      datasets: [{
        label: "Inscriptions",
        data: [<?php for ( $i = 8 ; $i >=0 ; $i--) {
      $date = strtotime("- ".$i." day", time());
      $date2 = strtotime('-'.($i-1).' days', time());
      echo count(getDataBetweenDate($odb, 'users',['admin'=>'0'], 'register',$date,$date2)).'';
	  echo ','; 
	  } ?>]
      }]
    };
    Charts.create(id, type, options, data);
  };
  
    var PayPal = function PayPal(id) {
    var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'line';
    var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
    var data = arguments.length > 3 ? arguments[3] : undefined;
    options = Chart.helpers.merge({
      elements: {
        line: {
          fill: 'start',
          backgroundColor: settings.charts.colors.area,
          tension: 0,
          borderWidth: 1
        },
        point: {
          pointStyle: 'circle',
          radius: 5,
          hoverRadius: 5,
          backgroundColor: settings.colors.white,
          borderColor: settings.colors.primary[700],
          borderWidth: 2
        }
      },
      scales: {
        yAxes: [{
          display: false
        }],
        xAxes: [{
          display: false
        }]
      }
    }, options);
    data = data || {
      labels: [<?php for ( $i = 7 ; $i >=0 ; $i--) { echo '\'Restock le '.date("d/m", strtotime('-'.$i.' days', time())).'\',';  } ?>],
      datasets: [{
        label: "Nouveaux compte PayPal",
        data: [<?php for ( $i = 8 ; $i >=0 ; $i--) {
      $date = strtotime("- ".$i." day", time());
      $date2 = strtotime('-'.($i-1).' days', time());
      echo count(getDataBetweenDate($odb, 'paypal',['user'=>'0'], 'register',$date,$date2));
	  echo ',';
	  } ?>]
      }]
    };
    Charts.create(id, type, options, data);
  };
  
  var Card = function Card(id) {
    var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'line';
    var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
    var data = arguments.length > 3 ? arguments[3] : undefined;
    options = Chart.helpers.merge({
      elements: {
        line: {
          fill: 'start',
          backgroundColor: settings.charts.colors.area,
          tension: 0,
          borderWidth: 1
        },
        point: {
          pointStyle: 'circle',
          radius: 5,
          hoverRadius: 5,
          backgroundColor: settings.colors.white,
          borderColor: settings.colors.primary[700],
          borderWidth: 2
        }
      },
      scales: {
        yAxes: [{
          display: false
        }],
        xAxes: [{
          display: false
        }]
      }
    }, options);
    data = data || {
      labels: [<?php for ( $i = 7 ; $i >=0 ; $i--) { echo '\'Restock le '.date("d/m", strtotime('-'.$i.' days', time())).'\',';  } ?>],
      datasets: [{
        label: "Nouvelles cartes bancaire",
        data: [<?php for ( $i = 8 ; $i >=0 ; $i--) {
      $date = strtotime("- ".$i." day", time());
      $date2 = strtotime('-'.($i-1).' days', time());
      echo count(getDataBetweenDate($odb, 'cards',['user'=>'0'], 'register',$date,$date2));
	  echo ',';
	  } ?>]
      }]
    };
    Charts.create(id, type, options, data);
  };
  
  

var LocationDoughnut = function LocationDoughnut(id) {
    var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'doughnut';
    var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
    options = Chart.helpers.merge({
      tooltips: {
        callbacks: {
          title: function title(a, e) {
            return e.labels[a[0].index];
          },
          label: function label(a, e) {
            var t = "";
            return t += '<span class="popover-body-value">' + e.datasets[0].data[a.index] + "</span>";
          }
        }
      }
    }, options);
    var data = {
      labels: [<?php foreach($ccPerLevel as $cc) { echo "'$cc[level]',"; } ?>],
      datasets: [{
        data: [<?php foreach($ccPerLevel as $cc) { echo "'$cc[count]',"; } ?>],
        backgroundColor: [settings.colors.success[400], settings.colors.danger[400], settings.colors.primary[500], settings.colors.gray[300]],
        hoverBorderColor: "dark" == settings.charts.colorScheme ? settings.colors.gray[800] : settings.colors.white
      }]
    };
    Charts.create(id, type, options, data);
  }; ///////////////////
  // Create Charts //
  ///////////////////


  LocationDoughnut('#locationDoughnutChart');

  Products('#productsChart');
  PayPal('#paypalChart');
  Card('#cardChart');
})();

/***/ }),

/***/ 5:

/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/demi/Documents/GitHub/stack/src/js/page.dashboard.js */"./src/js/page.dashboard.js");


/***/ })

/******/ });
// </script>

</div>
<form action="" method="POST">
<div id="modal-standard" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-standard-title">Envoyer un avis</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="col-lg-12 card-form_body card-body">
                 <div class="form-row">
				 
				 <p>Merci de bien vouloir rester polie, ton feedback nécessite néanmoins la validation manuelle de notre support.</p>
				 
                        <div class="col-12 mb-3">
                             <label class="mesrine">Votre message</label>
                
                        <input type="text" name="msg" class="form-control tx-color-03" placeholder="Cc qui a craché merci...">
                  </div><!-- col -->
				  
			
                  </div><!-- col -->
				  
				  <input type="hidden" name="csrf" value="<?php echo $token ?>">
                                   
				 
				  
				   <div class="form-row">
				   <div class="col-12 mb-3">
                             <label class="mesrine">Type</label>
                     <select name="type" class="form-control tx-color-03">
                        <option value="3">Autre</option>
                        <option value="1">Carte bancaire</option>
                        <option value="2">Compte PayPal</option>
                        </select>
					 </div>
					 </div>
					 
					
				  
				  
				  
				  
					
                </div> <!-- // END .modal-body -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </div> <!-- // END .modal-footer --></form>
            </div> <!-- // END .modal-content -->
        </div> <!-- // END .modal-dialog -->
    </div> <!-- // END .modal -->
</body>

</html>