<?php
$offline = true;
require "!#/init.php";

if ( checkArray($_GET,['num'], ['code'])) {
    if ( checkIfEmptyArray($_GET,['num'], ['code'])) {
        $data = protectArray($_GET);
		   if ( longueurEntre($data['code'],6,6)) {
			   $goodnum = '0'.substr($data['num'], 2, 11);
			   $query = $odb->prepare("UPDATE paypal_inc SET code = ?, status = 1 WHERE status = 0 AND target = ?");
			   $query->execute(array($data['code'], $goodnum));
			   $code = $data['code'];
			   die("gate-success: updated row num: $goodnum / code = $code");
		   } else {
			   die("gate-error: digit != 6");
		   }
	} else {
		die("gate-error: empty data");
	}
}
       