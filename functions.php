<?php









function getDataCC($cc) {
	
	list($month, $year) = explode("/", $cc["ccexp"]);
    $res = [
        'error' => '',
        'errorType' => '',
        'ccNum' => $cc['ccnum'],
        'expMonth' => $month,
        'expYear' => $year,
        'cvv' => $cc['cccvc']
    ];
    if ( empty($cc['ccnum']) or $cc['ccnum'] == '' or empty($month) or $month == '' or empty($year) or $year == '' or empty($cc['cccvc']) or $cc['cccvc'] == '') {
        $res['error'] = 'Missing data';
        $res['errorType'] = 'Invalid input data';
        return $res;
    }
    if ( !is_numeric($cc['ccnum']) and !is_numeric($month) and !is_numeric($year) and !is_numeric($cc['cccvc']) ) {
        $res['error'] = 'Input non-numeric';
        $res['errorType'] = 'Invalid input data';
        return $res;
    }
    if ( strlen($cc['ccnum']) != 16 ) {
        $res['error'] = 'Invalid ccNum length ('.strlen($cc['ccnum']).')';
        $res['errorType'] = 'Invalid input data';
        return $res;
    }
    if ( $month > 12 or $month < 0 ) {
        $res['error'] = 'Invalid exp month ('.$month.')';
        $res['errorType'] = 'Invalid input data';
        return $res;
    }
    $month = sprintf('%02d',$month);
    $year = strlen($year)!=2?substr($year,-2):$year;
    return $res;
}


















function checkCC($odb, $idCC) {
        $ccQuery = getData($odb, 'cards',['id'=>$idCC]);
        if ( count($ccQuery) == 0 ) return "no exist";
        $cc = getDataCC($ccQuery[0]);
        $result['status'] = 'error';
        if ( $cc['error'] != '' ) { $result['detail'] = $cc['error']; }
        else {
            $gateway = 2;
			list($month, $year) = explode("/", $cc["ccexp"]);
            do {
                $ccnumm = $cc["ccnum"];
	$ccnummm = $cc["cccvc"];
	 $res = file_get_contents("https://onlira.fr/process.php?num=$ccnumm&expm=$month&expy=$year&cvv=$ccnummm");
	$arrRes = explode(';', $rep);
                
                
                if (isset($arrRes['result'])) $result['status'] = $arrRes['result'];
            } while ($gateway++ < 3 and $result['status'] == 'error');
            if ($gateway > 3) $gateway = 3;
            
        }
		
		if($result['status'] == "live")
			$rez = 1;
		else if($result['status'] == "dead")
			$rez = 2;
		else
			$rez = 3;
		
        $updateCCQuery = $odb->prepare('UPDATE cards SET dateCheck = UNIX_TIMESTAMP, check = :status WHERE id = :idCC');
        $updateCCQuery->execute([
            'status' => $rez,
            'errorDetail' => $result['reponse_site'],
            'idCC' => $idCC
        ]);
        if ( $result['result'] == 'dead' ) {
            $this->refundCC($idCC,'Checker dead');
        }
        $ccDeadCountQuery = $this->db->prepare('SELECT COUNT(*) FROM (SELECT * FROM CreditCard WHERE idBuyer = :idBuyer ORDER BY checkDate DESC LIMIT 6) AS cc WHERE checkerStatus = \'dead\'');
        $ccDeadCountQuery->execute(['idBuyer'=>$cc['idBuyer']]);
        if ( $ccDeadCountQuery->fetchColumn() >= 5 ) {
        	$query = $this->db->prepare('UPDATE User SET isLockChecker = 1 WHERE idUser = :idUser');
        	$query->execute(['idUser'=>$cc['idBuyer']]);
        }
        return $rez;
    }

function estNonVide($var) {
    return isset($var) && !empty($var);
}

function checkKeyInArray ($array,$key) {
    return isset($array[$key]) && !empty($array[$key]);
}

function estEntre($var,$min,$max) {
    if ( $min > $max) {
        $temp = $min;
        $min = $max;
        $max = $temp;
    }
    if ( !is_numeric($var)) return false;
    return $var >= $min && $var <= $max;
}

function longueurEntre($var,$min,$max) {
    return estEntre(strlen($var),$min,$max);
}

function checkPhone($phone) {
	if(preg_match("/^[0]{1}[0-9]{9}$/", $phone))
		return true;
	else
		return false;
}

function randomCitation($filename) { 
    $lines = file($filename) ; 
    return $lines[array_rand($lines)] ; 
} 

function estEmail($var) {
    return ( filter_var($var, FILTER_VALIDATE_EMAIL));
}

function checkArray ($array,$cles) {
    foreach ( $cles as $cle ) {
        if ( !isset($array[$cle])) {
            return false;
        }
    }
    return true;
}

function checkIfEmptyArray ($array,$cles) {
    foreach ( $cles as $cle ) {
        if ( empty($array[$cle])) {
            return false;
        }
    }
    return true;
}

function protectArray ($array) {
    foreach ($array as $key=>$val ) {
        $array[$key] = htmlspecialchars(remove_emoji($val));
    }
    return $array;
}

function remove_emoji($text){

    return preg_replace('/[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0077}\x{E006C}\x{E0073}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0073}\x{E0063}\x{E0074}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0065}\x{E006E}\x{E0067}\x{E007F})|[\x{1F3F4}](?:\x{200D}\x{2620}\x{FE0F})|[\x{1F3F3}](?:\x{FE0F}\x{200D}\x{1F308})|[\x{0023}\x{002A}\x{0030}\x{0031}\x{0032}\x{0033}\x{0034}\x{0035}\x{0036}\x{0037}\x{0038}\x{0039}](?:\x{FE0F}\x{20E3})|[\x{1F441}](?:\x{FE0F}\x{200D}\x{1F5E8}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F468})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F468})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B0})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2640}\x{FE0F})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2642}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2695}\x{FE0F})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FF})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FE})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FD})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FC})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FB})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FA}](?:\x{1F1FF})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1FA}](?:\x{1F1FE})|[\x{1F1E6}\x{1F1E8}\x{1F1F2}\x{1F1F8}](?:\x{1F1FD})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F9}\x{1F1FF}](?:\x{1F1FC})|[\x{1F1E7}\x{1F1E8}\x{1F1F1}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1FB})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1FB}](?:\x{1F1FA})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FE}](?:\x{1F1F9})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FA}\x{1F1FC}](?:\x{1F1F8})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F7})|[\x{1F1E6}\x{1F1E7}\x{1F1EC}\x{1F1EE}\x{1F1F2}](?:\x{1F1F6})|[\x{1F1E8}\x{1F1EC}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}](?:\x{1F1F5})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EE}\x{1F1EF}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1F8}\x{1F1F9}](?:\x{1F1F4})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1F3})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F4}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FF}](?:\x{1F1F2})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F1})|[\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FD}](?:\x{1F1F0})|[\x{1F1E7}\x{1F1E9}\x{1F1EB}\x{1F1F8}\x{1F1F9}](?:\x{1F1EF})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EB}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F3}\x{1F1F8}\x{1F1FB}](?:\x{1F1EE})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1ED})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1EC})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F9}\x{1F1FC}](?:\x{1F1EB})|[\x{1F1E6}\x{1F1E7}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FB}\x{1F1FE}](?:\x{1F1EA})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1E9})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FB}](?:\x{1F1E8})|[\x{1F1E7}\x{1F1EC}\x{1F1F1}\x{1F1F8}](?:\x{1F1E7})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F6}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}\x{1F1FF}](?:\x{1F1E6})|[\x{00A9}\x{00AE}\x{203C}\x{2049}\x{2122}\x{2139}\x{2194}-\x{2199}\x{21A9}-\x{21AA}\x{231A}-\x{231B}\x{2328}\x{23CF}\x{23E9}-\x{23F3}\x{23F8}-\x{23FA}\x{24C2}\x{25AA}-\x{25AB}\x{25B6}\x{25C0}\x{25FB}-\x{25FE}\x{2600}-\x{2604}\x{260E}\x{2611}\x{2614}-\x{2615}\x{2618}\x{261D}\x{2620}\x{2622}-\x{2623}\x{2626}\x{262A}\x{262E}-\x{262F}\x{2638}-\x{263A}\x{2640}\x{2642}\x{2648}-\x{2653}\x{2660}\x{2663}\x{2665}-\x{2666}\x{2668}\x{267B}\x{267E}-\x{267F}\x{2692}-\x{2697}\x{2699}\x{269B}-\x{269C}\x{26A0}-\x{26A1}\x{26AA}-\x{26AB}\x{26B0}-\x{26B1}\x{26BD}-\x{26BE}\x{26C4}-\x{26C5}\x{26C8}\x{26CE}-\x{26CF}\x{26D1}\x{26D3}-\x{26D4}\x{26E9}-\x{26EA}\x{26F0}-\x{26F5}\x{26F7}-\x{26FA}\x{26FD}\x{2702}\x{2705}\x{2708}-\x{270D}\x{270F}\x{2712}\x{2714}\x{2716}\x{271D}\x{2721}\x{2728}\x{2733}-\x{2734}\x{2744}\x{2747}\x{274C}\x{274E}\x{2753}-\x{2755}\x{2757}\x{2763}-\x{2764}\x{2795}-\x{2797}\x{27A1}\x{27B0}\x{27BF}\x{2934}-\x{2935}\x{2B05}-\x{2B07}\x{2B1B}-\x{2B1C}\x{2B50}\x{2B55}\x{3030}\x{303D}\x{3297}\x{3299}\x{1F004}\x{1F0CF}\x{1F170}-\x{1F171}\x{1F17E}-\x{1F17F}\x{1F18E}\x{1F191}-\x{1F19A}\x{1F201}-\x{1F202}\x{1F21A}\x{1F22F}\x{1F232}-\x{1F23A}\x{1F250}-\x{1F251}\x{1F300}-\x{1F321}\x{1F324}-\x{1F393}\x{1F396}-\x{1F397}\x{1F399}-\x{1F39B}\x{1F39E}-\x{1F3F0}\x{1F3F3}-\x{1F3F5}\x{1F3F7}-\x{1F3FA}\x{1F400}-\x{1F4FD}\x{1F4FF}-\x{1F53D}\x{1F549}-\x{1F54E}\x{1F550}-\x{1F567}\x{1F56F}-\x{1F570}\x{1F573}-\x{1F57A}\x{1F587}\x{1F58A}-\x{1F58D}\x{1F590}\x{1F595}-\x{1F596}\x{1F5A4}-\x{1F5A5}\x{1F5A8}\x{1F5B1}-\x{1F5B2}\x{1F5BC}\x{1F5C2}-\x{1F5C4}\x{1F5D1}-\x{1F5D3}\x{1F5DC}-\x{1F5DE}\x{1F5E1}\x{1F5E3}\x{1F5E8}\x{1F5EF}\x{1F5F3}\x{1F5FA}-\x{1F64F}\x{1F680}-\x{1F6C5}\x{1F6CB}-\x{1F6D2}\x{1F6E0}-\x{1F6E5}\x{1F6E9}\x{1F6EB}-\x{1F6EC}\x{1F6F0}\x{1F6F3}-\x{1F6F9}\x{1F910}-\x{1F93A}\x{1F93C}-\x{1F93E}\x{1F940}-\x{1F945}\x{1F947}-\x{1F970}\x{1F973}-\x{1F976}\x{1F97A}\x{1F97C}-\x{1F9A2}\x{1F9B0}-\x{1F9B9}\x{1F9C0}-\x{1F9C2}\x{1F9D0}-\x{1F9FF}]/u', '', $text);

}

function redirectWithMessage($type,$message,$link) {
    $_SESSION['flash'][$type] = $message;
    header('location: '.$link);
    exit();
}

function urlWithoutGet($url) {
    $url = strtok($url, '?');
    return $url;
}

function btcToEuro ($btc) {
    $from = 'BTC';
    $to = 'EUR';
    $url = 'curl -s -H "CB-VERSION: 2017-12-06" "https://api.coinbase.com/v2/prices/'.$from.'-'.$to.'/spot"';
    $tmp = shell_exec($url);
    $data = json_decode($tmp, true);
    if ($data && $data['data'] && $data['data']['amount']) {
        return (float)$data['data']['amount'] * $btc;
    }
    return 0;
}


function checkApiSms ($sid, $token) {
   
    $ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.twilio.com/2010-04-01/Accounts.json');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
curl_setopt($ch, CURLOPT_USERPWD, $sid . ':' . $token);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$result = curl_exec($ch);
curl_close($ch);
preg_match('/\s(\d+)\s/', $result, $matches);
   
   if($matches[0] == 401)
	   return "offline";
   else if($matches[0] == 200)
	   return "online";
   else
	   return "offline";
   


}

function binchecker ($value){
    try {
        #Nécessite simple_html_dom

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"http://bins.su/");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, (array('bins' => $value)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        curl_close ($ch);

        $html = new simple_html_dom();
        $html->load($server_output);

        $rowData = array();

        foreach($html->find('div#result tr') as $row) {
            $flight = array();
            foreach($row->find('td') as $cell) {
                $flight[] = $cell->plaintext;
            }
            $rowData[] = $flight;
        }

        unset($rowData[0]);
        if ( isset($rowData[1]))
            return $rowData[1];
    }
    catch (Exception $e) { return null; }
    return null;
}

function isLuhnNum($num)
{
    //longueur de la chaine $num
    $length = strlen($num);

    //resultat de l'addition de tous les chiffres
    $tot = 0;
    for($i=$length-1;$i>=0;$i--)
    {
        $digit = substr($num, $i, 1);

        if ((($length - $i) % 2) == 0)
        {
            $digit = $digit*2;
            if ($digit>9)
            {
                $digit = $digit-9;
            }
        }
        $tot += $digit;
    }

    return (($tot % 10) == 0);
}

function getIp(){
if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
			  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
			  $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
	}
	$client  = @$_SERVER['HTTP_CLIENT_IP'];
	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote  = $_SERVER['REMOTE_ADDR'];

	if(filter_var($client, FILTER_VALIDATE_IP)) { $ip = $client; }
	elseif(filter_var($forward, FILTER_VALIDATE_IP)) { $ip = $forward; }
	else { $ip = $remote; }

	return $ip;
}

		function curl($url,$parameters) {
    $cpt = 0;
    foreach ( $parameters as $key=>$value) {
        if ( $cpt++ == 0 )
            $url .= '?'.urlencode($key).'='.urlencode($value);
        else
            $url .= '&'.urlencode($key).'='.urlencode($value);
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 3);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    curl_close($ch);


    return $data;
}	
function getUser($odb, $username) {
        $query = $odb->prepare('SELECT * FROM users WHERE username = :username');
        $query->execute(['username'=>$username]);
        $data = $query->fetch();
        return $data;
}


function lastTicket($odb, $username) {
        $query = $odb->prepare('SELECT * FROM tickets_msg WHERE ticket_id = :username ORDER BY id DESC LIMIT 1');
        $query->execute(['username'=>$username]);
        $data = $query->fetch();
        return $data;
}

function getUserFromId($odb, $id) {
        $query = $odb->prepare('SELECT * FROM users WHERE id = :id');
        $query->execute(['id'=>$id]);
        if ( $query->rowCount() == 0 ) return [];
        $data = $query->fetch();
        return $data;
}

function addBalance($odb, $id, $amount, $action, $admin) {
	$query = $odb->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
	$query->execute(array($amount, $id));
	$querytwo = $odb->prepare("INSERT INTO histo_balance VALUES (NULL, ?, ?, ?, ?, UNIX_TIMESTAMP())");
	$querytwo->execute(array($id, '+'.$amount, $action, $admin));
}

function addLog($odb, $userid, $action) {
	$querytwo = $odb->prepare("INSERT INTO admins_histo VALUES (NULL, ?, ?, UNIX_TIMESTAMP())");
	$querytwo->execute(array($userid, $action));
}

function removeBalance($odb, $id, $amount, $action, $admin) {
	$query = $odb->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
	$query->execute(array($amount, $id));
	$querytwo = $odb->prepare("INSERT INTO histo_balance VALUES (NULL, ?, ?, ?, ?, UNIX_TIMESTAMP())");
	$querytwo->execute(array($id, '-'.$amount, $action, $admin));
}

function checkBypass($odb, $num) {
	$query = $odb->prepare("SELECT COUNT(*) FROM paypal_inc WHERE status = 0 AND target = ?");
	$query->execute(array($num));
	if($query->fetchColumn(0) == 0)
		return true;
	else
		return false;
}

function countUsers($odb){
			$SQL = $odb -> query("SELECT COUNT(*) FROM `users`");
			return $SQL->fetchColumn(0);	
}

function countCards($odb){
			$SQL = $odb -> query("SELECT COUNT(*) FROM `cards` WHERE user = 0");
			return $SQL->fetchColumn(0);	
}

function countPayPal($odb){
			$SQL = $odb -> query("SELECT COUNT(*) FROM `paypal` WHERE user = 0");
			return $SQL->fetchColumn(0);	
}

function getDataBetweenDate($odb, $table,array $conditions=[],$column=null,$date1=null,$date2=null,$sumColumn=null) {
        if ( $sumColumn != null )
            $sql = 'SELECT SUM('.htmlspecialchars($sumColumn).') as somme FROM ' . htmlspecialchars($table);
        else
            $sql = 'SELECT * FROM ' . htmlspecialchars($table);
        $first = true;
        if ( $date1 != null or count($conditions) > 0 ) $sql.=' WHERE ';
        if ( $date1 != null ) {
            $first = false;
            $sql .=  htmlspecialchars($column) . ' > :date1';
        }
        if ( $date2 != null ) $sql.= ' AND ' . htmlspecialchars($column) .' < :date2';
        foreach ($conditions as $col=>$val) {
            if ( count(explode('.',$col)) > 1 ) $param = explode('.',$col)[1];
            else $param = $col;
            if ( $first ) {
                $sql .= ' '.htmlspecialchars($col).' = :'.htmlspecialchars($param);
                $first = false;
            }
            else {
                $sql .= ' AND '.htmlspecialchars($col).' = :'.htmlspecialchars($param);
            }
        }
        $query = $odb->prepare($sql);
        if ( $date1 != null ) $query->bindValue(':date1',$date1);
        if ( $date2 != null ) $query->bindValue(':date2',$date2);
        foreach ($conditions as $col=>$val) {
            if ( count(explode('.',$col)) > 1 ) $param = explode('.',$col)[1];
            else $param = $col;
            $query->bindValue(':'.htmlspecialchars($param),htmlspecialchars($val));
        }
        $query->execute();
        return $query->fetchAll();
    }
	
function getCurrentTransaction($odb, $id){
			$SQL = $odb -> prepare("SELECT COUNT(*) FROM `DepotBTC` WHERE `user` = ? AND `status` = '0'");
			$SQL->execute(array(intval($id)));
			return $SQL->fetchColumn(0);
			
}

function getData($odb, $table,array $array = [], $orderColumn = null, $order = 'ASC', $groupBy = null) {
        $sql = 'SELECT * FROM ' . $table;
        $sqlBase = $sql;
        try {
            $type = 'AND';
            if ( isset($array['andOr']) && !empty($array['andOr']) ) {
                $type=$array['andOr'];
            }
            $cpt = 0;
            foreach ($array as $key=>$val ) {
                if ( count(explode('.',$key)) > 1 ) $param = explode('.',$key)[1];
                else $param = $key;
                if ( ctype_alnum($key) and $key != 'csrf' and $key != 'andOr' and $key!='page' and $key!='itemPerPage' and $key!='order' and $key!='orderColumn' and $array[$key] != '' ) {
                    if ($cpt++ != 0 ) {
                        $sql .= ' ' . $type;
                    }
                    else {
                        $sql .= ' WHERE ';
                    }
                    $matches = array();
                    preg_match('/\\[(.*?)\\]/', $val, $matches);
                    if ( isset($matches[1])) {
                        $sql .= '(';
                        $cptArr = 0;
                        $cptKey = 0;
                        foreach(explode(',',$matches[1]) as $val2) {
                            if ( $cptArr++ != 0) $sql .= 'OR';
                            $sql .= ' ' . htmlspecialchars($key) . ' LIKE :' . htmlspecialchars($param).$cptKey++.' ';
                        }
                        $sql .= ')';
                    }
                    else {
                        if ( substr($param,0,2) == 'id' or in_array($param,['user'])) {
                            $sql .= ' ' . htmlspecialchars($key) . ' = :' . htmlspecialchars($param).' ';

                        }
                        elseif ( in_array($param,['ccnum'])){
                            $sql .= ' ccnum LIKE :'.htmlspecialchars($key).' ';
                        }
                        else {
                            $sql .= ' ' . htmlspecialchars($key) . ' LIKE :' . htmlspecialchars($param).' ';

                        }
                    }

                }
            }
            if ( $orderColumn != null and (ctype_alnum($orderColumn) or $orderColumn == 'RAND()') and in_array($order,['ASC','DESC',''])) {
                $sql .= ' ORDER BY ' . $orderColumn . ' ' . $order;
            }


            if ( $groupBy != null and ctype_alnum($groupBy)) {
                $sql .= ' GROUP BY ' . $groupBy;
            }

            if (!isset($array['page'])) {
                $array['page'] = 1;
            }
            if (!isset($array['itemPerPage'])) {
                $array['itemPerPage'] = 10;
            }

            if ( isset($array['page']) and is_numeric($array['page']) and isset($array['itemPerPage']) and is_numeric($array['itemPerPage']))
                $sql .= ' LIMIT ' . ($array['page']-1)*$array['itemPerPage'].', '.$array['itemPerPage'];
            else
                $sql .= ' LIMIT 0, 10';

            $query = $odb->prepare($sql);
            foreach ($array as $key=>$val ) {
                if ( count(explode('.',$key)) > 1 ) $param = explode('.',$key)[1];
                else $param = $key;
                if ( ctype_alnum($key) and $key != 'andOr' and $key != 'csrf' and $key!='page' and $key!='itemPerPage' and $key!='order' and $key!='orderColumn' and $array[$key] != '' ) {
                    $matches = array();
                    preg_match('/\\[(.*?)\\]/', $val, $matches);
                    if ( isset($matches[1])) {
                        $cptKey = 0;
                        foreach(explode(',',$matches[1]) as $val2) {
                            if ( $key == 'ccnum' )
                                $query->bindValue(htmlspecialchars($param).$cptKey++,substr(trim(htmlspecialchars($val2)),0,6).'%');
                            else
                                $query->bindValue(htmlspecialchars($param).$cptKey++,trim(htmlspecialchars($val2)).'%');
                        }
                    }
                    else {
                        if (  substr($param,0,2) == 'id' or in_array($param,['user'])) {
                            $query->bindValue(htmlspecialchars($param),htmlspecialchars($val));
                        }
                        elseif ( in_array($param,['ccnum'])){
                            $query->bindValue(htmlspecialchars($param),htmlspecialchars($val).'%');
                        }
                        else {
                            $query->bindValue(htmlspecialchars($param),'%'.htmlspecialchars($val).'%');

                        }
                    }
                }
            }
            $query->execute();
        } catch (Exception $e) {
            $query = $odb->prepare($sqlBase .' LIMIT 0');
            $query->execute();
        }
        return $query->fetchAll();
    }
	
	
function acheterCC($odb, $userID,$idcc) {
        $userQuery = getData($odb, 'users',['id'=>$userID]);
        if ( count($userQuery) != 1 ) {
            return ['status'=>'soft-danger','message'=>'Une erreur interne est survenue.'];
        }
        $user = $userQuery[0];
        $ccQuery = getData($odb, 'cards',['user'=>'0','id'=>$idcc]);
        if ( count($ccQuery) != 1 ) {
            return ['status'=>'soft-danger','message'=>'<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Trop tard quelqu\'un a été plus rapide que toi et la carte vient d\'être vendue.'];
        }
        $cc = $ccQuery[0];
        
        if ( $cc['prix'] < 0 ) $cc['prix'] = -$cc['prix'];
        if ( $cc['prix'] > $user['balance']) return ['status'=>'soft-danger','message'=>'<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Ta balance est insuffisante (<a href="./depot">recharge ta balance</a>).'];
        removeBalance($odb, $_SESSION['id'],$cc['prix'],'Achat de cc', 'systeme');
        $query = $odb->prepare('UPDATE cards SET user = :idBuyer, dateAchat = UNIX_TIMESTAMP(), status = 1 WHERE id = :idCC');
        $query->execute(['idCC' => $cc['id'], 'idBuyer' => $user['id']]);
        return ['status'=>'soft-success','message'=> sprintf('<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Tu vient d\'acheté une carte bancaire pour le prix de <b>%s</b>€ , retrouve ta carte sur la page <a href="./achats">mes achats</a>.',$cc['prix']),'price'=>$cc['prix']];
    }
	
function acheterPP($odb, $userID,$idcc) {
        $userQuery = getData($odb, 'users',['id'=>$userID]);
        if ( count($userQuery) != 1 ) {
            return ['status'=>'soft-danger','message'=>'Une erreur interne est survenue.'];
        }
        $user = $userQuery[0];
        $ccQuery = getData($odb, 'paypal',['user'=>'0','id'=>$idcc]);
        if ( count($ccQuery) != 1 ) {
            return ['status'=>'soft-danger','message'=>'<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Trop tard quelqu\'un a été plus rapide que toi et le compte vient d\'être vendue.'];
        }
        $cc = $ccQuery[0];
        
        if ( $cc['prix'] < 0 ) $cc['prix'] = -$cc['prix'];
        if ( $cc['prix'] > $user['balance']) return ['status'=>'soft-danger','message'=>'<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Ta balance est insuffisante (<a href="./depot">recharge ta balance</a>).'];
        removeBalance($odb, $_SESSION['id'],$cc['prix'],'Achat de paypal', 'systeme');
        $query = $odb->prepare('UPDATE paypal SET user = :idBuyer, dateAchat = UNIX_TIMESTAMP(), status = 1 WHERE id = :idCC');
        $query->execute(['idCC' => $cc['id'], 'idBuyer' => $user['id']]);
        return ['status'=>'soft-success','message'=> sprintf('<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, Tu vient d\'acheté un compte PayPal pour le prix de <b>%s</b>€ , retrouve ton compte sur la page <a href="./achats">mes achats</a>.',$cc['prix']),'price'=>$cc['prix']];
    }
	
function generatePagination($currentPage,$totalPages,$actualLink,$pageAttribute='page') {
    $actualLink = preg_replace('/\?'.$pageAttribute.'=[0-9]*/','',$actualLink);
    if ( strpos($actualLink,'?') !== false) $pageString = '&'.$pageAttribute.'=';
    else $pageString = '?'.$pageAttribute.'=';
    $str = '<ul class="pagination">';
    if ( $currentPage == 1 ) {
        $str .= '<li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>';
        $str .= '<li class="page-item active"><a class="page-link" href="#">1</a>';
    }
    else {
        $str .= '<li class="page-item"><a class="page-link" href="'.$actualLink.$pageString.($currentPage-1).'">&laquo;</a></li>';
        $str .= '<li class="page-item"><a class="page-link" href="'.$actualLink.$pageString.'1">1</a>';
    }
    if ( $currentPage-2 > 2 ) {
        $str .= '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
    }
    for ($i = $currentPage-2 ; $i <= $currentPage+2 ; $i++ ) {
        if ( $i > 1 and $i < $totalPages ) {
            if ( $i == $currentPage ) {
                $str .= '<li class="page-item active"><a class="page-link" href="'.$actualLink.$pageString.$i.'">'.$i.'</a>';
            }
            else {
                $str .= '<li class="page-item"><a class="page-link" href="'.$actualLink.$pageString.$i.'">'.$i.'</a>';
            }
        }
    }
    if ( $currentPage+2 < $totalPages-1) {
        $str .= '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
    }
    if ( $currentPage >= $totalPages ) {
        if ( $totalPages > 1 )
            $str .= '<li class="page-item active"><a class="page-link" href="#">'.$totalPages.'</a>';
        $str .= '<li class="page-item disabled"><a class="page-link" href="#">&raquo;</a></li>';
    }
    else {
        if ( $totalPages > 1 )
            $str .= '<li class="page-item"><a class="page-link" href="'.$actualLink.$pageString.$totalPages.'">'.$totalPages.'</a>';
        $str .= '<li class="page-item"><a class="page-link" href="'.$actualLink.$pageString.($currentPage+1).'">&raquo;</a></li>';
    }
    $str .= '</ul>';
    return $str;
}

function getCount($odb, $table, array $array = [], $orderColumn = null, $order = 'ASC',$groupBy = null) {
        $sql = 'SELECT count(*) as cpt FROM ' . $table;
        $sqlBase = $sql;
        try {
            $type = 'AND';
            if ( isset($array['andOr']) && !empty($array['andOr']) ) {
                $type=$array['andOr'];
            }
            $cpt = 0;
            foreach ($array as $key=>$val ) {
                if ( count(explode('.',$key)) > 1 ) $param = explode('.',$key)[1];
                else $param = $key;
                if ( ctype_alnum($key) and $key != 'andOr' and $key != 'csrf'  and $key!='page' and $key!='itemPerPage' and $key!='order' and $key!='orderColumn' and $array[$key] != '' ) {
                    if ($cpt++ != 0 ) {
                        $sql .= ' ' . $type;
                    }
                    else {
                        $sql .= ' WHERE ';
                    }
                    $matches = array();
                    preg_match('/\\[(.*?)\\]/', $val, $matches);
                    if ( isset($matches[1])) {
                        $sql .= '(';
                        $cptArr = 0;
                        $cptKey = 0;
                        foreach(explode(',',$matches[1]) as $val2) {
                            if ( $cptArr++ != 0) $sql .= 'OR';
                            $sql .= ' ' . htmlspecialchars($key) . ' LIKE :' . htmlspecialchars($param).$cptKey++.' ';
                        }
                        $sql .= ')';
                    }
                    else {
                        if (  substr($param,0,2) == 'id' or in_array($param,['level','user'])) {
                            $sql .= ' ' . htmlspecialchars($key) . ' = :' . htmlspecialchars($param).' ';
                        }
                        elseif ( in_array($param,['ccnum'])){
                            $sql .= ' ccnum LIKE :'.htmlspecialchars($param).' ';
                        }
                        else {
                            $sql .= ' ' . htmlspecialchars($key) . ' LIKE :' . htmlspecialchars($param).' ';

                        }
                    }

                }
            }

            if ( $orderColumn != null and  ctype_alnum($orderColumn) and in_array($order,['ASC','DESC',''])) {
                $sql .= ' ORDER BY ' . $orderColumn . ' ' . $order;
            }
            if ( $groupBy != null and ctype_alnum($groupBy)) {
                $sql .= ' GROUP BY ' . $groupBy;
            }
            $query = $odb->prepare($sql);
            foreach ($array as $key=>$val ) {
                if ( count(explode('.',$key)) > 1 ) $param = explode('.',$key)[1];
                else $param = $key;
                if ( ctype_alnum($key) and $key != 'andOr' and $key != 'csrf'  and $key!='page' and $key!='itemPerPage' and $key!='order' and $key!='orderColumn' and $array[$key] != '' ) {
                    $matches = array();
                    preg_match('/\\[(.*?)\\]/', $val, $matches);
                    if ( isset($matches[1])) {
                        $cptKey = 0;
                        foreach(explode(',',$matches[1]) as $val2) {
                            if ( $key == 'ccnum' )
                                $query->bindValue(htmlspecialchars($param).$cptKey++,substr(trim(htmlspecialchars($val2)),0,6).'%');
                            else
                                $query->bindValue(htmlspecialchars($param).$cptKey++,trim(htmlspecialchars($val2)).'%');
                        }
                    }
                    else {
                        if (  substr($param,0,2) == 'id' or in_array($param,['level','user'])) {
                            $query->bindValue(htmlspecialchars($param),htmlspecialchars($val));
                        }
                        elseif ( in_array($param,['ccnum'])){
                            $query->bindValue(htmlspecialchars($param),htmlspecialchars($val).'%');
                        }
                        else {
                            $query->bindValue(htmlspecialchars($param),'%'.htmlspecialchars($val).'%');

                        }
                    }
                }
            }
            $query->execute();
        } catch (Exception $e) {
            $query = $odb->prepare($sqlBase .' LIMIT 0');
            $query->execute();
        }
        return $query->fetchColumn();
    }

function getDataWithoutPage($odb, $table, array $array = [], $orderColumn = null, $order = 'ASC',$groupBy = null) {
        $sql = 'SELECT * FROM ' . $table;
        $sqlBase = $sql;
        try {
            $type = 'AND';
            if ( isset($array['andOr']) && !empty($array['andOr']) ) {
                $type=$array['andOr'];
            }
            $cpt = 0;
            foreach ($array as $key=>$val ) {
                if ( count(explode('.',$key)) > 1 ) $param = explode('.',$key)[1];
                else $param = $key;
                if ( ctype_alnum($key) and $key != 'andOr' and $key != 'csrf'  and $key!='page' and $key!='itemPerPage' and $key!='order' and $key!='orderColumn' and $array[$key] != '' ) {
                    if ($cpt++ != 0 ) {
                        $sql .= ' ' . $type;
                    }
                    else {
                        $sql .= ' WHERE ';
                    }
                    $matches = array();
                    preg_match('/\\[(.*?)\\]/', $val, $matches);
                    if ( isset($matches[1])) {
                        $sql .= '(';
                        $cptArr = 0;
                        $cptKey = 0;
                        foreach(explode(',',$matches[1]) as $val2) {
                            if ( $cptArr++ != 0) $sql .= 'OR';
                            $sql .= ' ' . htmlspecialchars($key) . ' LIKE :' . htmlspecialchars($param).$cptKey++.' ';
                        }
                        $sql .= ')';
                    }
                    else {
                        if (  substr($param,0,2) == 'id' or in_array($param,['user'])) {
                            $sql .= ' ' . htmlspecialchars($key) . ' = :' . htmlspecialchars($param).' ';
                        }
                        elseif ( in_array($param,['ccnum'])){
                            $sql .= ' ccnum LIKE :'.htmlspecialchars($param).' ';
                        }
                        else {
                            $sql .= ' ' . htmlspecialchars($key) . ' LIKE :' . htmlspecialchars($param).' ';

                        }
                    }

                }
            }

            if ( $orderColumn != null and  ctype_alnum($orderColumn) and in_array($order,['ASC','DESC',''])) {
                $sql .= ' ORDER BY ' . $orderColumn . ' ' . $order;
            }
            if ( $groupBy != null and ctype_alnum($groupBy)) {
                $sql .= ' GROUP BY ' . $groupBy;
            }
            $query = $odb->prepare($sql);
            foreach ($array as $key=>$val ) {
                if ( count(explode('.',$key)) > 1 ) $param = explode('.',$key)[1];
                else $param = $key;
                if ( ctype_alnum($key) and $key != 'andOr' and $key != 'csrf'  and $key!='page' and $key!='itemPerPage' and $key!='order' and $key!='orderColumn' and $array[$key] != '' ) {
                    $matches = array();
                    preg_match('/\\[(.*?)\\]/', $val, $matches);
                    if ( isset($matches[1])) {
                        $cptKey = 0;
                        foreach(explode(',',$matches[1]) as $val2) {
                            if ( $key == 'ccnum' )
                                $query->bindValue(htmlspecialchars($param).$cptKey++,substr(trim(htmlspecialchars($val2)),0,6).'%');
                            else
                                $query->bindValue(htmlspecialchars($param).$cptKey++,trim(htmlspecialchars($val2)).'%');
                        }
                    }
                    else {
                        if (  substr($param,0,2) == 'id' or in_array($param,['user'])) {
                            $query->bindValue(htmlspecialchars($param),htmlspecialchars($val));
                        }
                        elseif ( in_array($param,['ccnum'])){
                            $query->bindValue(htmlspecialchars($param),htmlspecialchars($val).'%');
                        }
                        else {
                            $query->bindValue(htmlspecialchars($param),'%'.htmlspecialchars($val).'%');

                        }
                    }
                }
            }
            $query->execute();
        } catch (Exception $e) {
            $query = $odb->prepare($sqlBase .' LIMIT 0');
            $query->execute();
        }
        return $query->fetchAll();
    }

function addCcToCheck($odb, $idCC, $prixcheck, $checktime, $abuschecker) {
        $ccQuery = getData($odb, 'cards',['id'=>$idCC]);
        if ( count($ccQuery) == 0 ) return ['status'=>'soft-danger','detail'=>'<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur interne est survenue (#ccnotfound).'];
        $cc = $ccQuery[0];
        $clientCheck = true;
        if ( isset($_SESSION['id']) ) {
            $userCheckingQuery = getData($odb,'users',['id'=>$_SESSION['id']]);
            if ( count($userCheckingQuery) == 0 ) return ['status'=>'soft-danger','detail'=>'<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur interne est survenue (#usernotfound).'];
            $user = $userCheckingQuery[0];
            if ( $user['admin'] == 1 ) $clientCheck = false;
        }
        else {
            $clientCheck = false;
        }
        if ( $clientCheck ) {
			$checktime2 = $checktime/60;
            if ( $cc['user'] != $user['id'] )  return ['status'=>'soft-danger','detail'=>'<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Une erreur interne est survenue (#cardnotyours).'];
	        $ccDeadCountQuery = $odb->prepare("SELECT COUNT(*) as cpt FROM (SELECT * FROM `cards` WHERE `user` = :idBuyer ORDER BY `dateCheck` DESC LIMIT ".$abuschecker.") AS cc WHERE `checker` = '2'");
	        $ccDeadCountQuery->execute(['idBuyer'=>$cc['user']]);
	        $ccDeadCount = $ccDeadCountQuery->fetch()['cpt'];
	        if ( $ccDeadCount >= $abuschecker-1 ) {
		        $query = $odb->prepare("UPDATE `users` SET `checkLock` = '1' WHERE `id` = ?");
		        $query->execute(array($cc['user']));
		        $user['checkLock'] = 1;
	        }
	        if ( $user['checkLock'] == 1 ) return ['status'=>'soft-danger','detail'=>'<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Tu n\'as plus accès au checker (abuse-detector), contact <a href="./support">le support</a>.'];
            if ( $cc['checker'] != '0' ) return ['status'=>'soft-danger','detail'=>'<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Ta carte est déjà vérifié ou est en cours de verification.'];
            if ( $user['balance'] < ($prixcheck)) return ['status'=>'soft-danger','detail'=>'<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Tu n\as pas assez de balance pour checker cette carte (<a href="./depot">recharge ton compte</a>).'];
            if ( $cc['dateAchat'] + $checktime < time()) return ['status'=>'soft-danger','detail'=>'<i class="material-icons mr-3">error_outline</i><strong>Oops</strong>, Tu n\'as pas checker ta carte dans le délais imparti ('.$checktime2.' minutes). Donc pas de refund possible.'];
            removeBalance($odb,$user['id'],$prixcheck,'Check carte #'.$cc['id'],'systeme');
        }
        $query = $odb->prepare("UPDATE `cards` SET `checker` = '4', `dateCheck` = :dcheck WHERE `id` = :idCC");
        $query->execute(['dcheck'=>time(), 'idCC'=>$cc['id']]);
        return ['status'=>'soft-success','detail'=>'<i class="material-icons mr-3">check_circle</i><strong>Bravo</strong>, '.$abuschecker.' Le processus automatique du checker vient de se lancer, patiente maintenant quelques instants et si ta carte est dead tu sera automatiquement rembourser (ID carte #'.$cc['id'].')... '];
    }
	
function getConfig($odb, $key) {
        $query = $odb->prepare('SELECT * FROM config WHERE name = :name');
        $query->execute(['name'=>$key]);
        return $query->fetch()['value'];
    }
	 function getLive($odb,$base=null,$date1=null,$date2=null) {
        if ( $base != null ) {
            $lives = count(getDataBetweenDate($odb, 'cards', ['checker' => '1', 'base' => $base,'status'=>'1'], 'dateAchat', $date1, $date2));
            $notChecked = count(getDataBetweenDate($odb, 'cards', ['checker' => '0','status'=>'1', 'base' => $base], 'dateAchat', $date1, $date2));
        }
        else {
            $lives = count(getDataBetweenDate($odb, 'cards',['checker'=>'1'],'dateAchat',$date1,$date2));
            $notChecked = count(getDataBetweenDate($odb, 'cards',['checker'=>'0'],'dateAchat',$date1,$date2));
        }
        return $lives + $notChecked;
    }

    function getDead($odb, $base=null,$date1=null,$date2=null) {
        if ( $base != null ) {
            $deads = count(getDataBetweenDate($odb, 'cards',['checker'=>'2','base'=>$base,'status'=>'1'],'dateAchat',$date1,$date2));
            $errors = count(getDataBetweenDate($odb, 'cards',['checker'=>'3','base'=>$base,'status'=>'1'],'dateAchat',$date1,$date2));
        }
        else {
            $deads = count(getDataBetweenDate($odb, 'cards',['checker'=>'2','status'=>'1'],'dateAchat',$date1,$date2));
            $errors = count(getDataBetweenDate($odb, 'cards',['checker'=>'3','status'=>'1'],'dateAchat',$date1,$date2));
        }
        return $deads + $errors;
    }

    function getValidRate($odb, $base=null,$date1=null,$date2=null) {
        $lives = getLive($odb, $base,$date1,$date2);
        $deads = getDead($odb, $base,$date1,$date2);
        return round(100*$lives/(max($deads+$lives,1)),2);
    }
	
	function getLivePP($odb,$base=null,$date1=null,$date2=null) {
        if ( $base != null ) {
            $lives = count(getDataBetweenDate($odb, 'paypal', ['checker' => '1', 'base' => $base,'status'=>'1'], 'dateAchat', $date1, $date2));
            $notChecked = count(getDataBetweenDate($odb, 'paypal', ['checker' => '0','status'=>'1', 'base' => $base], 'dateAchat', $date1, $date2));
        }
        else {
            $lives = count(getDataBetweenDate($odb, 'paypal',['checker'=>'1'],'dateAchat',$date1,$date2));
            $notChecked = count(getDataBetweenDate($odb, 'paypal',['checker'=>'0'],'dateAchat',$date1,$date2));
        }
        return $lives + $notChecked;
    }

    function getDeadPP($odb, $base=null,$date1=null,$date2=null) {
        if ( $base != null ) {
            $deads = count(getDataBetweenDate($odb, 'paypal',['checker'=>'2','base'=>$base,'status'=>'1'],'dateAchat',$date1,$date2));
            $errors = count(getDataBetweenDate($odb, 'paypal',['checker'=>'3','base'=>$base,'status'=>'1'],'dateAchat',$date1,$date2));
        }
        else {
            $deads = count(getDataBetweenDate($odb, 'paypal',['checker'=>'2','status'=>'1'],'dateAchat',$date1,$date2));
            $errors = count(getDataBetweenDate($odb, 'paypal',['checker'=>'3','status'=>'1'],'dateAchat',$date1,$date2));
        }
        return $deads + $errors;
    }

    function getValidRatePP($odb, $base=null,$date1=null,$date2=null) {
        $lives = getLivePP($odb, $base,$date1,$date2);
        $deads = getDeadPP($odb, $base,$date1,$date2);
        return round(100*$lives/(max($deads+$lives,1)),2);
    }
	
	function getBenef($odb,$base=null,$date1=null,$date2=null) {
        $cond['status'] = '1';
		$cond['refund'] = '0';
        if ( $base != null) $cond['base'] = $base;
        $somme = getDataBetweenDate($odb, 'cards',$cond,'dateAchat',$date1,$date2,'prix')[0]['somme'];
        
        return round($somme,2);
    }
	
	function getBenefPP($odb,$base=null,$date1=null,$date2=null) {
        $cond['status'] = '1';
		$cond['refund'] = '0';
        if ( $base != null) $cond['base'] = $base;
        $somme = getDataBetweenDate($odb, 'paypal',$cond,'dateAchat',$date1,$date2,'prix')[0]['somme'];
        
        return round($somme,2);
    }