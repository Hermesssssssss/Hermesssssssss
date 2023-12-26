<?php
$banPage = true;
$page = "Tableau de bord administration";
require '!#/init.php';
require '!#/header1.php';

$adminpage = true;
if($user["admin"] < "1") {
	redirectWithMessage('soft-danger','<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur est survenue. (accès non autorisée)','dashboard');
    exit;
}


?>

 
                    
                        <div class="row card-group-row">
                           
							
							 <div class="col-md-12">
                                
								<div class="card">
                            <div class="card-header card-header-large bg-white">
                                <h4 class="card-header__title">Statistiques des cartes vendues
								<br><small><span class="badge badge-soft-primary"><b><?php
								
								
      $datei = strtotime("-1 day", time());
      $datei2 = strtotime('-0 days', time());
      echo count(getDataBetweenDate($odb, 'cards',['status'=>'1'], 'dateAchat',$datei,$datei2));
	  ?></b> &nbsp; cartes vendues aujourd'hui</span>
	  
	  <span class="badge badge-soft-success">Bénéfices : <?php echo getBenef($odb, null,strtotime("-1 day", time())); ?>€
	  </span>
	  
	  <span class="badge badge-soft-secondary">Validrate : <?php echo getValidRate($odb, null,strtotime("-1 day", time())); ?>%
	  </span>
	  
	  </small></h4>
                            </div>
                            <div class="card-body">

                                <div class="chart"><div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
                                    <canvas id="performanceAreaChart" class="chart-canvas chartjs-render-monitor" width="513" height="300" style="display: block; width: 513px; height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
							

                                </div>
                            </div>
							
							<!-- end row-->
							
							 <div class="row card-group-row">
                           
							
							 <div class="col-md-6">
                                
								<div class="card">
                            <div class="card-header card-header-large bg-white">
                                <h4 class="card-header__title">Statistiques des comptes PayPal vendus
								<br><small><span class="badge badge-soft-primary"><b><?php
								
							
      echo count(getDataBetweenDate($odb, 'paypal',['status'=>'1'], 'dateAchat',$datei,$datei2));
	  ?></b> &nbsp; comptes vendus aujourd'hui</span>
	  
	  <span class="badge badge-soft-success">Bénéfices : <?php echo getBenefPP($odb, null,strtotime("-1 day", time())); ?>€
	  </span>
	  
	  <span class="badge badge-soft-secondary">Validrate : <?php echo getValidRatePP($odb, null,strtotime("-1 day", time())); ?>%
	  </span>
	  
	  </small></h4>
                            </div>
                            <div class="card-body">

                                <div class="chart"><div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
                                    <canvas id="performanceAreaChartd" class="chart-canvas chartjs-render-monitor" width="513" height="300" style="display: block; width: 513px; height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
							

                                </div>
								
								<div class="col-md-6">
								<div class="card">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                          <h4 class="card-header__title">Statistiques générales</h4>
                                              
											  </div>
                                    <div class="card-body py-0">
                                        <div class="list-group list-group-small list-group-flush">

                                            <div class="list-group-item d-flex align-items-center px-0">
                                                <div class="mr-3 flex"> <i class="material-icons icon-18pt icon-muted">assessment</i> 
												Utilisateurs inscrits</div>
                                                <div><span class="badge badge-pill badge-soft-primary"><?php echo getCount($odb, 'users'); ?></span>
												<span class="badge badge-pill badge-soft-success"><a href=""  data-toggle="modal" data-target="#modal-standard"><?php echo count(getDataBetweenDate($odb,'users',[],'last',strtotime('-15 minutes', time()))); ?> en ligne</a></span></div>
                                            </div>
											
											<div class="list-group-item d-flex align-items-center px-0">
                                                <div class="mr-3 flex"> <i class="material-icons icon-18pt icon-muted">assessment</i> 
												Produits en vente</div>
                                                <div><span class="badge badge-pill badge-soft-primary"><?php echo getCount($odb, 'cards',['status'=>'0']); ?> CC</span>
												<span class="badge badge-pill badge-soft-success"><?php echo getCount($odb, 'paypal',['status'=>'0']); ?> PP</span></div>
                                            </div>
											
											<div class="list-group-item d-flex align-items-center px-0">
                                                <div class="mr-3 flex"> <i class="material-icons icon-18pt icon-muted">assessment</i> 
												Produits vendus</div>
                                                <div><span class="badge badge-pill badge-soft-primary"><?php echo getCount($odb, 'cards',['status'=>'1']); ?> CC</span>
												<span class="badge badge-pill badge-soft-success"><?php echo getCount($odb, 'paypal',['status'=>'1']); ?> PP</span></div>
                                            </div>
											
											<div class="list-group-item d-flex align-items-center px-0">
                                                <div class="mr-3 flex"> <i class="material-icons icon-18pt icon-muted">assessment</i> 
												Support tickets</div>
                                                <div><span class="badge badge-pill badge-soft-primary"><a href="./adm_support"><?php echo getCount($odb, 'tickets',['last'=>'0', 'close'=>'0']); ?> en attente</a></span></div>
                                            </div>
											
											<div class="list-group-item d-flex align-items-center px-0">
                                                <div class="mr-3 flex"> <i class="material-icons icon-18pt icon-muted">assessment</i> 
												Feedbacks</div>
                                                <div><span class="badge badge-pill badge-soft-primary"><?php echo getCount($odb, 'feedbacks',['status'=>'1']); ?> validées</span>
												<span class="badge badge-pill badge-soft-success"><a href="./adm_feedbacks"><?php echo getCount($odb, 'feedbacks',['status'=>'0']); ?> en attente</a></span></div>
                                            </div>
											
											<div class="list-group-item d-flex align-items-center px-0">
                                                <div class="mr-3 flex"> <i class="material-icons icon-18pt icon-muted">assessment</i> 
												Bénéfices totale</div>
                                                <div><span class="badge badge-pill badge-soft-primary"><?php echo getBenef($odb, null); ?>€</span></div>
                                            </div>

                                           

                                        </div>
                                    </div>
                                    
                                </div>
								</div>
                            </div>
							
					
							<!-- end row-->
							
							<div class="row card-group-row">
							<div class="col-md-6">
								<div class="card">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                          <h4 class="card-header__title">Statistiques générales</h4>
                                              
											  </div>
                                    <div class="card-body py-0">
                                        <div class="list-group list-group-small list-group-flush">

                                            <div class="list-group-item d-flex align-items-center px-0">
                                                <div class="mr-3 flex"> <i class="material-icons icon-18pt icon-muted">assessment</i> 
												Dépôts aujourd'hui</div>
                                                <div><span class="badge badge-pill badge-soft-primary"
												><?php echo getDataBetweenDate($odb, 'DepotBTC',[],'date',strtotime("-1 day", time()),null, 'amount')[0]['somme']; ?>€</span>
												</div>
                                            </div>
											
											<div class="list-group-item d-flex align-items-center px-0">
                                                <div class="mr-3 flex"> <i class="material-icons icon-18pt icon-muted">assessment</i> 
												Dépôts cette semaine</div>
                                                <div><span class="badge badge-pill badge-soft-primary"
												><?php echo getDataBetweenDate($odb, 'DepotBTC',[],'date',strtotime("-1 week", time()),null, 'amount')[0]['somme']; ?>€</span>
												</div>
                                            </div>
											
											<div class="list-group-item d-flex align-items-center px-0">
                                                <div class="mr-3 flex"> <i class="material-icons icon-18pt icon-muted">assessment</i> 
												Dépôts ce mois-ci</div>
                                                <div><span class="badge badge-pill badge-soft-primary"
												><?php echo getDataBetweenDate($odb, 'DepotBTC',[],'date',strtotime("-1 month", time()),null, 'amount')[0]['somme']; ?>€</span>
												</div>
                                            </div>
											
											<div class="list-group-item d-flex align-items-center px-0">
                                                <div class="mr-3 flex"> <i class="material-icons icon-18pt icon-muted">assessment</i> 
												Dépôts au total</div>
                                                <div><span class="badge badge-pill badge-soft-primary"
												><?php echo getDataBetweenDate($odb, 'DepotBTC',[],'date',null,null, 'amount')[0]['somme']; ?>€</span>
												</div>
                                            </div>
											
											

                                          
                                    </div></div></div>
                                    
                                </div>
								
								<div class="col-md-6">
								<div class="card">
                                    <div class="card-header card-header-large bg-white d-flex align-items-center">
                                          <h4 class="card-header__title">Bases validrate</h4>
                                              
											  </div>
                                    <div class="card-body py-0">
                                        <div class="list-group list-group-small list-group-flush">

                                            <ul class="list-unstyled mt-3 dashboard-location-tabs nav flex-column m-0" role="tablist">
                                            <?php foreach(getDataWithoutPage($odb, 'cards',[],null,null,'base') as $base) { ?>
				   
				   <li data-toggle="vector-map-focus" data-target="#vector-map-revenue" data-focus="us" data-animate="true">
                                                <div class="dashboard-location-tabs__tab active" data-toggle="tab" role="tab" aria-selected="true">
                                                    <div><strong><?php echo $base['base'] ?></strong>
													<span class="badge badge-pill badge-soft-success">Bénéfices : <?php echo getBenef($odb, $base['base']) ?>€<span></div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex mr-2">
                                                            <div class="progress" style="height: 6px;">
                                                                <div class="progress-bar" role="progressbar" style="width: <?php echo getValidRate($odb, $base['base']); ?>%;" aria-valuenow="<?php echo getValidRate($odb, $base['base']); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                        <div><?php echo getValidRate($odb, $base['base']); ?>%</div>
                                                    </div>
                                                </div>
                                            </li><?php } ?>
                                           
                                        </ul>
											 
                    
											
											
											

                                          
                                    </div></div></div>
                                    
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
 /******/ (function(modules) { 
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
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/ 	__webpack_require__.p = "/";
/******/ 	return __webpack_require__(__webpack_require__.s = 7);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/page.ui-charts.js":

/***/ (function(module, exports) {

(function () {
  'use strict';

  Charts.init();

  var Performance = function Performance(id) {
    var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'line';
    var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
    options = Chart.helpers.merge({
      scales: {
        yAxes: [{
          ticks: {
            callback: function callback(a) {
              if (!(a % 1)) return "" + a + "";
            }
          }
        }]
      },
      tooltips: {
        callbacks: {
          label: function label(a, e) {
            var t = e.datasets[a.datasetIndex].label || "",
                o = a.yLabel,
                r = "";
            return 1 < e.datasets.length && (r += '<span class="popover-body-label mr-auto">' + t + "</span>"), r += '<span class="popover-body-value">Cartes vendues : ' + o + "</span>";
          }
        }
      }
    }, options);
    var data = {
      labels: [<?php for ( $i = 7 ; $i >=0 ; $i--) { echo '\''.date("d/m", strtotime('-'.$i.' days', time())).'\',';  } ?>],
      datasets: [{
        label: "Cartes vendues",
        data: [<?php for ( $i = 8 ; $i >=0 ; $i--) {
      $date = strtotime("- ".$i." day", time());
      $date2 = strtotime('-'.($i-1).' days', time());
      echo count(getDataBetweenDate($odb, 'cards',['status'=>'1'], 'dateAchat',$date,$date2));
	  echo ',';
	  } ?>]
      }]
    };
    Charts.create(id, type, options, data);
  };
  
   var Performanced = function Performanced(id) {
    var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'line';
    var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
    options = Chart.helpers.merge({
      scales: {
        yAxes: [{
          ticks: {
            callback: function callback(a) {
              if (!(a % 1)) return "" + a + "";
            }
          }
        }]
      },
      tooltips: {
        callbacks: {
          label: function label(a, e) {
            var t = e.datasets[a.datasetIndex].label || "",
                o = a.yLabel,
                r = "";
            return 1 < e.datasets.length && (r += '<span class="popover-body-label mr-auto">' + t + "</span>"), r += '<span class="popover-body-value">Comptes vendus : ' + o + "</span>";
          }
        }
      }
    }, options);
    var data = {
      labels: [<?php for ( $i = 7 ; $i >=0 ; $i--) { echo '\''.date("d/m", strtotime('-'.$i.' days', time())).'\',';  } ?>],
      datasets: [{
        label: "Comptes vendus",
        data: [<?php for ( $i = 8 ; $i >=0 ; $i--) {
      $date = strtotime("- ".$i." day", time());
      $date2 = strtotime('-'.($i-1).' days', time());
      echo count(getDataBetweenDate($odb, 'paypal',['status'=>'1'], 'dateAchat',$date,$date2));
	  echo ',';
	  } ?>]
      }]
    };
    Charts.create(id, type, options, data);
  };

  

  Performance('#performanceChart');
  Performance('#performanceAreaChart', 'line', {
    elements: {
      line: {
        fill: 'start',
        backgroundColor: settings.charts.colors.area
      }
    }
  });
  
  Performanced('#performanceChartd');
  Performanced('#performanceAreaChartd', 'line', {
    elements: {
      line: {
        fill: 'start',
        backgroundColor: settings.charts.colors.area
      }
    }
  });
  
  $('[data-toggle="chart"]:checked').each(function (index, el) {
    Charts.add($(el));
  });
})();

/***/ }),

/***/ 7:

/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/demi/Documents/GitHub/stack/src/js/page.ui-charts.js */"./src/js/page.ui-charts.js");


/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vc3JjL2pzL3BhZ2UudWktY2hhcnRzLmpzIl0sIm5hbWVzIjpbIkNoYXJ0cyIsIlBlcmZvcm1hbmNlIiwidHlwZSIsIm9wdGlvbnMiLCJzY2FsZXMiLCJ5QXhlcyIsInRpY2tzIiwiY2FsbGJhY2siLCJhIiwidG9vbHRpcHMiLCJjYWxsYmFja3MiLCJsYWJlbCIsInQiLCJlIiwibyIsInIiLCJkYXRhIiwibGFiZWxzIiwiZGF0YXNldHMiLCJPcmRlcnMiLCJiYXJSb3VuZG5lc3MiLCJEZXZpY2VzIiwidGl0bGUiLCJiYWNrZ3JvdW5kQ29sb3IiLCJzZXR0aW5ncyIsImhvdmVyQm9yZGVyQ29sb3IiLCJ3aGl0ZSIsImVsZW1lbnRzIiwibGluZSIsImZpbGwiLCJhcmVhIiwiJCJdLCJtYXBwaW5ncyI6IjtBQUFBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0Esa0RBQTBDLGdDQUFnQztBQUMxRTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLGdFQUF3RCxrQkFBa0I7QUFDMUU7QUFDQSx5REFBaUQsY0FBYztBQUMvRDs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsaURBQXlDLGlDQUFpQztBQUMxRSx3SEFBZ0gsbUJBQW1CLEVBQUU7QUFDckk7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxtQ0FBMkIsMEJBQTBCLEVBQUU7QUFDdkQseUNBQWlDLGVBQWU7QUFDaEQ7QUFDQTtBQUNBOztBQUVBO0FBQ0EsOERBQXNELCtEQUErRDs7QUFFckg7QUFDQTs7O0FBR0E7QUFDQTs7Ozs7Ozs7Ozs7O0FDbEZB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RUFBWSxDQUFaQTtBQURGK0I7QUE1SEYsSyIsImZpbGUiOiIvZGlzdC9hc3NldHMvanMvcGFnZS51aS1jaGFydHMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHsgZW51bWVyYWJsZTogdHJ1ZSwgZ2V0OiBnZXR0ZXIgfSk7XG4gXHRcdH1cbiBcdH07XG5cbiBcdC8vIGRlZmluZSBfX2VzTW9kdWxlIG9uIGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uciA9IGZ1bmN0aW9uKGV4cG9ydHMpIHtcbiBcdFx0aWYodHlwZW9mIFN5bWJvbCAhPT0gJ3VuZGVmaW5lZCcgJiYgU3ltYm9sLnRvU3RyaW5nVGFnKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFN5bWJvbC50b1N0cmluZ1RhZywgeyB2YWx1ZTogJ01vZHVsZScgfSk7XG4gXHRcdH1cbiBcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsICdfX2VzTW9kdWxlJywgeyB2YWx1ZTogdHJ1ZSB9KTtcbiBcdH07XG5cbiBcdC8vIGNyZWF0ZSBhIGZha2UgbmFtZXNwYWNlIG9iamVjdFxuIFx0Ly8gbW9kZSAmIDE6IHZhbHVlIGlzIGEgbW9kdWxlIGlkLCByZXF1aXJlIGl0XG4gXHQvLyBtb2RlICYgMjogbWVyZ2UgYWxsIHByb3BlcnRpZXMgb2YgdmFsdWUgaW50byB0aGUgbnNcbiBcdC8vIG1vZGUgJiA0OiByZXR1cm4gdmFsdWUgd2hlbiBhbHJlYWR5IG5zIG9iamVjdFxuIFx0Ly8gbW9kZSAmIDh8MTogYmVoYXZlIGxpa2UgcmVxdWlyZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy50ID0gZnVuY3Rpb24odmFsdWUsIG1vZGUpIHtcbiBcdFx0aWYobW9kZSAmIDEpIHZhbHVlID0gX193ZWJwYWNrX3JlcXVpcmVfXyh2YWx1ZSk7XG4gXHRcdGlmKG1vZGUgJiA4KSByZXR1cm4gdmFsdWU7XG4gXHRcdGlmKChtb2RlICYgNCkgJiYgdHlwZW9mIHZhbHVlID09PSAnb2JqZWN0JyAmJiB2YWx1ZSAmJiB2YWx1ZS5fX2VzTW9kdWxlKSByZXR1cm4gdmFsdWU7XG4gXHRcdHZhciBucyA9IE9iamVjdC5jcmVhdGUobnVsbCk7XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18ucihucyk7XG4gXHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShucywgJ2RlZmF1bHQnLCB7IGVudW1lcmFibGU6IHRydWUsIHZhbHVlOiB2YWx1ZSB9KTtcbiBcdFx0aWYobW9kZSAmIDIgJiYgdHlwZW9mIHZhbHVlICE9ICdzdHJpbmcnKSBmb3IodmFyIGtleSBpbiB2YWx1ZSkgX193ZWJwYWNrX3JlcXVpcmVfXy5kKG5zLCBrZXksIGZ1bmN0aW9uKGtleSkgeyByZXR1cm4gdmFsdWVba2V5XTsgfS5iaW5kKG51bGwsIGtleSkpO1xuIFx0XHRyZXR1cm4gbnM7XG4gXHR9O1xuXG4gXHQvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5uID0gZnVuY3Rpb24obW9kdWxlKSB7XG4gXHRcdHZhciBnZXR0ZXIgPSBtb2R1bGUgJiYgbW9kdWxlLl9fZXNNb2R1bGUgP1xuIFx0XHRcdGZ1bmN0aW9uIGdldERlZmF1bHQoKSB7IHJldHVybiBtb2R1bGVbJ2RlZmF1bHQnXTsgfSA6XG4gXHRcdFx0ZnVuY3Rpb24gZ2V0TW9kdWxlRXhwb3J0cygpIHsgcmV0dXJuIG1vZHVsZTsgfTtcbiBcdFx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgJ2EnLCBnZXR0ZXIpO1xuIFx0XHRyZXR1cm4gZ2V0dGVyO1xuIFx0fTtcblxuIFx0Ly8gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSBmdW5jdGlvbihvYmplY3QsIHByb3BlcnR5KSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqZWN0LCBwcm9wZXJ0eSk7IH07XG5cbiBcdC8vIF9fd2VicGFja19wdWJsaWNfcGF0aF9fXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnAgPSBcIi9cIjtcblxuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDcpO1xuIiwiKGZ1bmN0aW9uKCl7XG4gICd1c2Ugc3RyaWN0JztcblxuICBDaGFydHMuaW5pdCgpXG4gIFxuICB2YXIgUGVyZm9ybWFuY2UgPSBmdW5jdGlvbihpZCwgdHlwZSA9ICdsaW5lJywgb3B0aW9ucyA9IHt9KSB7XG4gICAgb3B0aW9ucyA9IENoYXJ0LmhlbHBlcnMubWVyZ2Uoe1xuICAgICAgc2NhbGVzOiB7XG4gICAgICAgIHlBeGVzOiBbe1xuICAgICAgICAgIHRpY2tzOiB7XG4gICAgICAgICAgICBjYWxsYmFjazogZnVuY3Rpb24oYSkge1xuICAgICAgICAgICAgICBpZiAoIShhICUgMTApKVxuICAgICAgICAgICAgICAgIHJldHVybiBcIiRcIiArIGEgKyBcImtcIlxuICAgICAgICAgICAgfVxuICAgICAgICAgIH1cbiAgICAgICAgfV1cbiAgICAgIH0sXG4gICAgICB0b29sdGlwczoge1xuICAgICAgICBjYWxsYmFja3M6IHtcbiAgICAgICAgICBsYWJlbDogZnVuY3Rpb24oYSwgZSkge1xuICAgICAgICAgICAgdmFyIHQgPSBlLmRhdGFzZXRzW2EuZGF0YXNldEluZGV4XS5sYWJlbCB8fCBcIlwiLFxuICAgICAgICAgICAgICAgIG8gPSBhLnlMYWJlbCxcbiAgICAgICAgICAgICAgICByID0gXCJcIjtcbiAgICAgICAgICAgIHJldHVybiAxIDwgZS5kYXRhc2V0cy5sZW5ndGggJiYgKHIgKz0gJzxzcGFuIGNsYXNzPVwicG9wb3Zlci1ib2R5LWxhYmVsIG1yLWF1dG9cIj4nICsgdCArIFwiPC9zcGFuPlwiKSwgciArPSAnPHNwYW4gY2xhc3M9XCJwb3BvdmVyLWJvZHktdmFsdWVcIj4kJyArIG8gKyBcIms8L3NwYW4+XCJcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9LCBvcHRpb25zKVxuXG4gICAgdmFyIGRhdGEgPSB7XG4gICAgICBsYWJlbHM6IFtcIkphblwiLCBcIkZlYlwiLCBcIk1hclwiLCBcIkFwclwiLCBcIk1heVwiLCBcIkp1blwiLCBcIkp1bFwiLCBcIkF1Z1wiLCBcIlNlcFwiLCBcIk9jdFwiLCBcIk5vdlwiLCBcIkRlY1wiXSxcbiAgICAgIGRhdGFzZXRzOiBbe1xuICAgICAgICBsYWJlbDogXCJQZXJmb3JtYW5jZVwiLFxuICAgICAgICBkYXRhOiBbMCwgMTAsIDUsIDE1LCAxMCwgMjAsIDE1LCAyNSwgMjAsIDMwLCAyNSwgNDBdXG4gICAgICB9XVxuICAgIH1cblxuICAgIENoYXJ0cy5jcmVhdGUoaWQsIHR5cGUsIG9wdGlvbnMsIGRhdGEpXG4gIH1cblxuICB2YXIgT3JkZXJzID0gZnVuY3Rpb24oaWQsIHR5cGUgPSAncm91bmRlZEJhcicsIG9wdGlvbnMgPSB7fSkge1xuICAgIG9wdGlvbnMgPSBDaGFydC5oZWxwZXJzLm1lcmdlKHtcbiAgICAgIGJhclJvdW5kbmVzczogMS4yLFxuICAgICAgc2NhbGVzOiB7XG4gICAgICAgIHlBeGVzOiBbe1xuICAgICAgICAgIHRpY2tzOiB7XG4gICAgICAgICAgICBjYWxsYmFjazogZnVuY3Rpb24oYSkge1xuICAgICAgICAgICAgICBpZiAoIShhICUgMTApKVxuICAgICAgICAgICAgICAgIHJldHVybiBcIiRcIiArIGEgKyBcImtcIlxuICAgICAgICAgICAgfVxuICAgICAgICAgIH1cbiAgICAgICAgfV1cbiAgICAgIH0sXG4gICAgICB0b29sdGlwczoge1xuICAgICAgICBjYWxsYmFja3M6IHtcbiAgICAgICAgICBsYWJlbDogZnVuY3Rpb24oYSwgZSkge1xuICAgICAgICAgICAgdmFyIHQgPSBlLmRhdGFzZXRzW2EuZGF0YXNldEluZGV4XS5sYWJlbCB8fCBcIlwiLFxuICAgICAgICAgICAgICAgIG8gPSBhLnlMYWJlbCxcbiAgICAgICAgICAgICAgICByID0gXCJcIjtcbiAgICAgICAgICAgIHJldHVybiAxIDwgZS5kYXRhc2V0cy5sZW5ndGggJiYgKHIgKz0gJzxzcGFuIGNsYXNzPVwicG9wb3Zlci1ib2R5LWxhYmVsIG1yLWF1dG9cIj4nICsgdCArIFwiPC9zcGFuPlwiKSwgciArPSAnPHNwYW4gY2xhc3M9XCJwb3BvdmVyLWJvZHktdmFsdWVcIj4kJyArIG8gKyBcIms8L3NwYW4+XCJcbiAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICAgIH1cbiAgICB9LCBvcHRpb25zKVxuXG4gICAgdmFyIGRhdGEgPSB7XG4gICAgICBsYWJlbHM6IFtcIkphblwiLCBcIkZlYlwiLCBcIk1hclwiLCBcIkFwclwiLCBcIk1heVwiLCBcIkp1blwiLCBcIkp1bFwiLCBcIkF1Z1wiLCBcIlNlcFwiLCBcIk9jdFwiLCBcIk5vdlwiLCBcIkRlY1wiXSxcbiAgICAgIGRhdGFzZXRzOiBbe1xuICAgICAgICBsYWJlbDogXCJTYWxlc1wiLFxuICAgICAgICBkYXRhOiBbMjUsIDIwLCAzMCwgMjIsIDE3LCAxMCwgMTgsIDI2LCAyOCwgMjYsIDIwLCAzMl1cbiAgICAgIH1dXG4gICAgfVxuXG4gICAgQ2hhcnRzLmNyZWF0ZShpZCwgdHlwZSwgb3B0aW9ucywgZGF0YSlcbiAgfVxuXG4gIHZhciBEZXZpY2VzID0gZnVuY3Rpb24oaWQsIHR5cGUgPSAnZG91Z2hudXQnLCBvcHRpb25zID0ge30pIHtcbiAgICBvcHRpb25zID0gQ2hhcnQuaGVscGVycy5tZXJnZSh7XG4gICAgICB0b29sdGlwczoge1xuICAgICAgICBjYWxsYmFja3M6IHtcbiAgICAgICAgICB0aXRsZTogZnVuY3Rpb24oYSwgZSkge1xuICAgICAgICAgICAgcmV0dXJuIGUubGFiZWxzW2FbMF0uaW5kZXhdXG4gICAgICAgICAgfSxcbiAgICAgICAgICBsYWJlbDogZnVuY3Rpb24oYSwgZSkge1xuICAgICAgICAgICAgdmFyIHQgPSBcIlwiO1xuICAgICAgICAgICAgcmV0dXJuIHQgKz0gJzxzcGFuIGNsYXNzPVwicG9wb3Zlci1ib2R5LXZhbHVlXCI+JyArIGUuZGF0YXNldHNbMF0uZGF0YVthLmluZGV4XSArIFwiJTwvc3Bhbj5cIlxuICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgfVxuICAgIH0sIG9wdGlvbnMpXG5cbiAgICB2YXIgZGF0YSA9IHtcbiAgICAgIGxhYmVsczogW1wiRGVza3RvcFwiLCBcIlRhYmxldFwiLCBcIk1vYmlsZVwiXSxcbiAgICAgIGRhdGFzZXRzOiBbe1xuICAgICAgICBkYXRhOiBbNjAsIDI1LCAxNV0sXG4gICAgICAgIGJhY2tncm91bmRDb2xvcjogW3NldHRpbmdzLmNvbG9ycy5wcmltYXJ5WzcwMF0sIHNldHRpbmdzLmNvbG9ycy5zdWNjZXNzWzMwMF0sIHNldHRpbmdzLmNvbG9ycy5zdWNjZXNzWzEwMF1dLFxuICAgICAgICBob3ZlckJvcmRlckNvbG9yOiBcImRhcmtcIiA9PSBzZXR0aW5ncy5jaGFydHMuY29sb3JTY2hlbWUgPyBzZXR0aW5ncy5jb2xvcnMuZ3JheVs4MDBdIDogc2V0dGluZ3MuY29sb3JzLndoaXRlXG4gICAgICB9XVxuICAgIH1cblxuICAgIENoYXJ0cy5jcmVhdGUoaWQsIHR5cGUsIG9wdGlvbnMsIGRhdGEpXG4gIH1cblxuICAvLy8vLy8vLy8vLy8vLy8vLy8vXG4gIC8vIENyZWF0ZSBDaGFydHMgLy9cbiAgLy8vLy8vLy8vLy8vLy8vLy8vL1xuXG4gIFBlcmZvcm1hbmNlKCcjcGVyZm9ybWFuY2VDaGFydCcpXG4gIFxuICBQZXJmb3JtYW5jZSgnI3BlcmZvcm1hbmNlQXJlYUNoYXJ0JywgJ2xpbmUnLCB7XG4gICAgZWxlbWVudHM6IHtcbiAgICAgIGxpbmU6IHtcbiAgICAgICAgZmlsbDogJ3N0YXJ0JyxcbiAgICAgICAgYmFja2dyb3VuZENvbG9yOiBzZXR0aW5ncy5jaGFydHMuY29sb3JzLmFyZWFcbiAgICAgIH1cbiAgICB9XG4gIH0pXG5cbiAgT3JkZXJzKCcjb3JkZXJzQ2hhcnQnKVxuXG4gIE9yZGVycygnI29yZGVyc0NoYXJ0U3dpdGNoJylcblxuICBEZXZpY2VzKCcjZGV2aWNlc0NoYXJ0JylcblxuICAkKCdbZGF0YS10b2dnbGU9XCJjaGFydFwiXTpjaGVja2VkJykuZWFjaChmdW5jdGlvbiAoaW5kZXgsIGVsKSB7XG4gICAgQ2hhcnRzLmFkZCgkKGVsKSlcbiAgfSlcblxufSkoKSJdLCJzb3VyY2VSb290IjoiIn0=
</script>


<div id="modal-standard" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-standard-title">Utilisateurs en ligne</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="modal-body">
                    <p>Voici la liste des utilisateurs en ligne depuis les 15 dernières minutes.
					<br><br>
					<ul>
					 
					<?php $deb = getDataBetweenDate($odb,'users',[],'last',strtotime('-15 minutes', time())); 
					
					
					foreach($deb as $bro) { 
					
					switch($bro["admin"]) {
						case '1':
						     $admst = '<span class="badge badge-soft-warning">Support Manager</span>';
							 break;
						case '2':
						     $admst = '<span class="badge badge-soft-danger">Admin</span>';
							 break;
						default:
						     $admst = false;
							 break;
					}
						
						
					?>
					
					<li><?php echo $admst; ?> <a href="./adm_user?id=<?php echo $bro["id"]; ?>"><?php echo $bro["username"]; ?></a> - Balance : <b><?php echo $bro["balance"]; ?>€</b> </li>
					<?php } ?>
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