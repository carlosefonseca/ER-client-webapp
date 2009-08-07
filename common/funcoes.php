<?

function u($url) { return "../common/$url"; }
function eu($url) { echo u($url); }

// FUNÇÕES AUXILIARES
date_default_timezone_set("Europe/Lisbon");
setlocale(LC_ALL, 'pt_PT');

//Obtem um array com os jardins que o utilizador pode aceder
// OU, caso true seja passado por argumento
function getUserGardens($asSql = false) {
	$jardins = array();
	$perms = explode(",", $_SESSION["permissions"]);

	if(hasPermission("j*")) {	//SE PODE VER TODOS OS JARDINS
		if ($asSql) { return ""; } else { return "*"; };
	} else {
		if ($asSql) $q = " AND (";
		foreach($perms as $p) {	//CC
			if (preg_match('/j(\d+)/',$p)) {
				if ($asSql) {
					$q .= " id like '".substr($p,1)."' OR";
				} else {
					$jardins[] = substr($p,1);
				}
			}
		}
		if ($asSql) {
			return substr($q,0,-2).")";
		} else {
			return $jardins;
		}
	}
}



//Está em duplicado no programas/accao.class.php!
function processMarkerData($txt) {
	return utf8_encode(strtr(nl2br($txt), "\n"," "));
}

function normaliza ($string){ 
    $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
    $b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
	$string = strtr($string, "\n","_");
    $string = utf8_decode($string);     
    $string = strtr($string, utf8_decode($a), $b); 
    $string = strtolower($string); 
    return utf8_encode($string); 
} 

function date2daysSince($date, $refdate = "2002/01/01") {
	if($date == "00/00/0000") return "000000";
	$refdate = strtotime($refdate);
	$date = preg_replace('/(\d\d)\/(\d\d)\/(\d\d\d\d)/','${3}/${2}/${1}', $date);
	$date = strtotime($date);
	$date = ceil(($date - $refdate)/60/60/24);
	return $date;
}

function daysSince2date($days, $refdate = "2002/01/01") {
	return date("d/m/Y", strtotime($refdate . " +".$days." days"));
}

function sec2time($time){
  if(is_numeric($time)){
    $value = array(
      "years" => 0, "days" => 0, "hours" => 0,
      "minutes" => 0, "seconds" => 0,
    );
    if($time >= 31556926){
      $value["years"] = floor($time/31556926);
      $time = ($time%31556926);
    }
    if($time >= 86400){
      $value["days"] = floor($time/86400);
      $time = ($time%86400);
    }
    if($time >= 3600){
      $value["hours"] = floor($time/3600);
      $time = ($time%3600);
    }
    if($time >= 60){
      $value["minutes"] = floor($time/60);
      $time = ($time%60);
    }
    $value["seconds"] = floor($time);
    return (array) $value;
  }else{
    return (bool) FALSE;
  }
}

function time2sec( $time ) {
// INPUT: string formatted in "hh:mm:ss.ms"
// RETURN: time converted to seconds
// RETURN: -1 for invalid

  $tokens = array();  // token array
  $token = "";        // token string

  // Parse string
  for( $i = 0 ; $i < strlen($time); $i++ ) {
    $char = $time[$i];
    if( $char == ":" ) {  // handle h/m/s delimiter
      $tokens[] = $token;
      $token = "";
    } else {  // handle token
      $token .= $char;
    }
  }
  $tokens[] = $token;  // add final token to token array

  // Calculate seconds
  $total = 0;
  for( $i = count($tokens), $j = 0; $i > 0; $i--, $j++ ) {
    switch($i) {
      case 3:  // handle hours
        $total += 60 * 60 * $tokens[$j]; break;
      case 2:  // handle minutes
        $total += 60 * $tokens[$j]; break;
      case 1:  // handle seconds
        $total += 1 * $tokens[$j]; break;
      default: // handle other situations
        return(-1);
    }
  }

  // Return result, with msec added
  return( 0 + $total);
}


//print_r(Sec2Time("028860"));

/**
 * Converte string com dias do mês para texto.
 *
 * @param string com 31 zeros ou uns, correspondente aos dias do mês activados ou desactivados
 * @return string
 */
function diasMes2Human($t) {
	$dias = strposall($t, "1");
	if ($dias === false) {	//se não houver dias seleccionados
		return "Não há dias seleccionados.";
	} else {				//se houver
		$out = "";
		foreach ($dias as $d) {
			$out .= ($d+1).", ";
		}
		return substr($out, 0, -2);
	}
}

/**
 * Converte string com dias da semana para texto.
 *
 * @param string com 7 zeros ou uns, correspondente aos dias da semana activados ou desactivados
 * @return string
 */
function diasSemana2Human($t) {
	$dias = strposall($t, "1");
	if (!$dias)
		return false;
	$out = "";
	foreach($dias as $dia) {
		switch($dia) {
			case "0": $out .= "Segundas, "; break;
			case "1": $out .= "Terças, "; break;
			case "2": $out .= "Quartas, "; break;
			case "3": $out .= "Quintas, "; break;
			case "4": $out .= "Sextas, "; break;
			case "5": $out .= "Sábados, "; break;
			case "6": $out .= "Domingos, "; break;
		}
	}
	return(substr($out, 0, -2));
}

//echo diasSemana2human("0010010");



/** 
 * strposall 
 * 
 * Find all occurrences of a needle in a haystack 
 * 
 * @param string $haystack 
 * @param string $needle 
 * @return array or false 
 */ 
function strposall($haystack,$needle){ 
    
    $s=0; 
    $i=0; 
    
    while (is_integer($i)){ 
        
        $i = strpos($haystack,$needle,$s); 
        
        if (is_integer($i)) { 
            $aStrPos[] = $i; 
            $s = $i+strlen($needle); 
        } 
    } 
    if (isset($aStrPos)) { 
        return $aStrPos; 
    } 
    else { 
        return false; 
    } 
} 


function arrayTime2Sec($a) {
	$out = array();
	if (count($a)!=0) {
		foreach($a as $time) {
			$out[]=sprintf("%06d",time2sec($time));
		}
		sort($out);
	}
	return $out;
}




//###################################################################




include("../common/programas/programa.class.php");

function leProgramacao($fn = "11Programas.txt") {
	//Verificar a existencia do ficheiro e abre-o
	if (!file_exists($fn)) die("O ficheiro com a programação ($fn) não foi encontrado."/*.print_r(scandir("."),true)*/);
	$fp = fopen($fn, "r");

	//Lê e divide o ficheiro nos diferentes programas
	$progsArray = explode("InicioDePrograma\n", str_replace("\r", "", fread($fp, filesize($fn))));

	fclose($fp);
	$programas = array();
	
	
	//Para cada programa
	foreach($progsArray as $p) {
		//Remove as linhas que forem comentários
		$firstChar = substr(trim($p),0,1);
		if (($firstChar == "'") || (strpos(substr(trim($p),0,5), "'")!= false))
			continue;
	
		$prog = new programa();
		$r = $prog->initFromFile($p);
		if($r === false)
			continue;
		
		//Junta o programa ao array de programas
		$programas[$prog->getKey()] = $prog;
	}
	ksort($programas);
	//devolve o array
	return($programas);
}


function escreveProgramacao($programas, $fn = "11Programas.txt") {

	$out = "'Activo(0/1),Livre1(#), Livre2(#),ETR,Geral(0/1/2),%Rega(100),Rotativo(1),Tempo de rotativo(000000),Se o programa é recorrente não limitado/recorrente limitado/semanal(0/1/3), Dia de inicio do programa(000296),dia fim(000297), Outros para ajudar a escrever se o programa é limitado ou não e as datas por extenso de inicio e fim do programa,#Se 0 ou 2#:dias de intervalo(000000), Ocorrencias(000005) #se 3#dias da semana(0/1)0000000 Dias do Mês(0/1)0000000000000000000000000000000
'mais inf
\r\n";

	foreach($programas as $p) {
		$out .= $p->outputForFile();
	}

	// escreve no ficheiro
	if(!$fp = fopen($fn, "w+")) die("ERRO AO ACEDER AO FICHEIRO DE ESCRITA");
	fwrite($fp, $out);
	fclose($fp);
}


/*
function leMeteo($fn = "meteo.txt") {
	//Verificar a existencia do ficheiro e abre-o
	if (!file_exists($fn)) die("O ficheiro com a meteorologia não foi encontrado.".print_r(scandir("."),true));
	$fp = fopen($fn, "r");
	
}

*/
?>