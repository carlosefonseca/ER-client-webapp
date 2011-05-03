<?
require_once("../common/funcoes.php");

//Página requer que o utilizador se tenha autenticado
session_start();
require_once(u("user.php"));
requireLogin();

define("PROGS_PATH","serverfiles/programas");
define("ALTERACOES_FILE","serverfiles/Alteracoes.txt");

function hasPermissionToGarden($id = null) {
	global $client;
	if(!$id && !isset($_POST["garden"])) die("ERROR: No GARDEN set");
	if(!$id) {
		$id = $_POST["garden"]; //Numero do Jardim
	}
	
	//Verificar a existencia do jardim
	require_once("DBconnect.php");
	$q = "SELECT id,acronym FROM jardins WHERE client like '%$client%' AND id='$id'";
	$res=mysql_query($q) or die(mysql_error());

	if(mysql_num_rows($res)!=1 || (!hasPermission("j$j")&&!hasPermission("j*"))) {
		die("PERMISSION DENIED");
	}
	
	$r = mysql_fetch_array($res);
	return $r[1];
}


function getPrograma($id) {
	$nJ = hasPermissionToGarden(); //Morre se o user nao tiver acesso ao jardim

	$id = normaliza($id);
	
	$progs = leProgramacao(PROGS_PATH."/$nJ.txt");
	if ($p = $progs[$id]) {
		die(json_encode($p->toBrowser()));
	} else {
		die("PROGRAM '$id' DOES NOT EXIST");
	}
}

function apagarPrograma($pID) {
	if (!hasPermission("edit_program")) {
		die("PERMISSION DENIED");
	}
	
	$nJ = hasPermissionToGarden(); //Morre se o user nao tiver acesso ao jardim
	
	$progs = leProgramacao(PROGS_PATH."/$nJ.txt");
	$nome = $progs[$pID]->nome;
	unset($progs[$pID]);
	escreveProgramacao($progs,PROGS_PATH."/$nJ.txt");
	escreveAlteracao(ALTERACOES_FILE,$nJ,"PR",$nome);
	die("OK");
}


function getListaProgramas() {
	$nJ = hasPermissionToGarden(); //Morre se o user nao tiver acesso ao jardim

	if(!file_exists(PROGS_PATH."/$nJ.txt")) die("[]");
	$progs = leProgramacao(PROGS_PATH."/$nJ.txt");
	$a = array();
	foreach($progs as $key => $p) {
		$a[] = $p->activo.$p->nome;
	}
	die(json_encode($a));
}


function activarPrograma($pID) {
	if(!isset($pID)) die("ERROR: No ID set");
	$nJ = hasPermissionToGarden(); //Morre se o user nao tiver acesso ao jardim
	$progs = leProgramacao(PROGS_PATH."/$nJ.txt");
	$p = $progs[$pID];
	$p->activar();
	escreveProgramacao($progs, PROGS_PATH."/$nJ.txt");
	escreveAlteracao(ALTERACOES_FILE,$nJ,"PR",$p->nome);
	die(json_encode($p->toBrowser()));
}


function desactivarPrograma($pID) {
	if(!isset($pID)) die("ERROR: No ID set");
	$nJ = hasPermissionToGarden(); //Morre se o user nao tiver acesso ao jardim
	$progs = leProgramacao(PROGS_PATH."/$nJ.txt");
	$p = $progs[$pID];
	$p->desactivar();
	escreveProgramacao($progs, PROGS_PATH."/$nJ.txt");
	escreveAlteracao(ALTERACOES_FILE,$nJ,"PR",$p->nome);
	die(json_encode($p->toBrowser()));
}



function savePrograma($id, $data) {	
	if(!isset($id)) die("ERROR: No ID.");
	if(!isset($data)) die("ERROR: No DATA.");
	
	$nJ = hasPermissionToGarden(); //Morre se o user nao tiver acesso ao jardim
//	die("->$nJ<-");
	$d = str_replace(array("\\\"","\\\\","£"), array("\"","\\","+"), $_POST["data"]); 

	$p = new Programa();
	$p->recorrencia = new Recorrencia();
	$p->updateFromSite(json_decode($d));
	$newId = $p->getKey();

	if(file_exists(PROGS_PATH."/$nJ.txt")) {
		$progs = leProgramacao(PROGS_PATH."/$nJ.txt");
	}

	$progs[$newId] = $p;
	if ($id != $newId && $id != "NOVO") {
		unset($progs[$id]);
	}
	
	escreveProgramacao($progs, PROGS_PATH.'/'.$nJ.".txt");
	escreveAlteracao(ALTERACOES_FILE,$nJ,"PR",$p->nome);
	die(json_encode($progs[$newId]->toBrowser()));
}


function setGardenPlaces($id, $lat, $lng) {
	if (!hasPermission("edit_markers")) {
		die("PERMISSION DENIED");
	}

	$id  = addslashes($id);
	$lat = addslashes($lat);
	$lng = addslashes($lng);

	include("DBconnect.php");
	$q = "UPDATE  `jardins` SET  lat = '$lat', lng = '$lng' WHERE CONVERT(`jardins`.`id` USING utf8 ) =  '$id' LIMIT 1 ;";
	mysql_query($q) or die("SQL Error");

	updateCenterCoords();

	die("OK");
}

function actDeactJardins($pID, $accao) {
	$jardinsAcessiveis = getUserGardens();

//	$file = "dados/activos.txt";
//	$activos = parseDataFile($file);
	if ($pID == "*") {
		foreach($jardinsAcessiveis as $i) {
			$nJ = hasPermissionToGarden($i);
			$activos[$nJ] = ($accao=="act"?1:0);
		}
		//print_r($activos);
	} else {
		if(!isset($pID)) die("ERROR: No ID.");
		$nJ = hasPermissionToGarden($pID);
		$activos[$nJ] = ($accao=="act"?1:0);
	}
	escreveAlteracao(ALTERACOES_FILE,$activos, "IO");
	updateActivos($activos);
	die("OK");
}

global $client;

if( isset( $_POST['action'])) {
	switch($_POST['action']) {
		case "apagarPrograma":		apagarPrograma($_POST["id"]);		break;
		case "desactivarPrograma":	desactivarPrograma($_POST["id"]); 	break;
		case "activarPrograma":		activarPrograma($_POST["id"]); 		break;
		case "getPrograma":			getPrograma($_POST["id"]); 	break;
//		case "getProgramaJSON":		getProgramaJSON(); 		break;
		case "getEditPrograma":		getEditPrograma(); 		break;
		case "setPrograma":			savePrograma($_POST["id"],$_POST["data"]); 		break;
		case "getListaProgramas":	getListaProgramas(); 	break;
		case "updateMarker":		setGardenPlaces($_POST["id"],$_POST["lat"],$_POST["lng"]);		break;
		case "actJardins":			actDeactJardins("*","act");	break;
		case "desactJardins":		actDeactJardins("*","deact");	break;		
		case "actJardim":			actDeactJardins($_POST["id"],"act");	break;
		case "desactJardim":		actDeactJardins($_POST["id"],"deact");break;		
		default:					die("UNKNOWN COMMAND\n\n".print_r($_POST,true));
	}
} else {
	actDeactJardins("act");
	//die("ACTION COMMAND NEED");
}



?>