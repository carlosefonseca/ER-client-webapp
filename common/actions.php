<?
require_once("../common/funcoes.php");

//Página requer que o utilizador se tenha autenticado
if (!isset($_SESSION)) session_start();
require_once(u("user.php"));
requireLogin();


define("PROGS_PATH","serverfiles/programas");
define("ALTERACOES_FILE","serverfiles/Alteracoes.txt");


/* Verifica se o utilizador por aceder ao jardim e retorna o Acr. desse jadrim */
function hasPermissionToGarden($id = null) {
	global $client;
	if (isset($client) && $client == "") { die("hasPermissionToGarden: Client not set"); }
	if(!$id && !isset($_POST["garden"])) die("ERROR: No GARDEN set");
	if(!$id) {
		$id = $_POST["garden"]; //Numero do Jardim
	}

	//Verificar a existencia do jardim
	require_once("DBconnect.php");
	$q = "SELECT id,acronym FROM jardins WHERE client like '%$client%' AND id='$id'";
	$res=mysql_query($q) or die(mysql_error());

	if(mysql_num_rows($res)!=1 || !hasGardenPermission($id)) {
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
	global $client;
	if (isset($client) && $client == "") { die("setGardenPlaces: Client not set"); }
	if (!hasPermission("edit_markers")) {
		die("PERMISSION DENIED");
	}

	$id  = cleanString($id);
	iLog("setGardenPlaces: $id, $lat, $lng");
	if (!is_numeric($id)) { die("Identificador errado."); }
	$lat = addslashes($lat);
	$lng = addslashes($lng);
	if (!is_numeric($lat) || !is_numeric($lng)) { die("Posição errada."); }

	include("DBconnect.php");
	$q = "UPDATE  `jardins` SET  lat = '$lat', lng = '$lng' WHERE client LIKE '$client' AND CONVERT(`jardins`.`id` USING utf8 ) =  '$id' LIMIT 1 ;";
	mysql_query($q) or die("SQL Error: ".mysql_error());

	updateCenterCoords($client);

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


function updateUser($username, $gardens, $permissions) {
	if (!hasPermission("users")) {
		die("PERMISSION DENIED");
	}
	global $client;

	$username	= mysql_real_escape_string($username);
	$gardens 	= mysql_real_escape_string($gardens);
	$permissions= mysql_real_escape_string($permissions);

	$q = "select users.user, email, gardens, permissions.permissions
		  from users left join permissions on (users.user=permissions.user)
		  WHERE (permissions.client = '$client' OR permissions.client = '*') && users.user = '$username'";
	$res = mysql_query($q) or die("FAIL\n\n".mysql_error());
	if (mysql_num_rows($res) != 1) {
		iLog($q);
		die("ERROR: Wrong username");
	}
	$user = mysql_fetch_assoc($res);
	if (strpos($user["permissions"], "admin") !== false && !hasPermission("admin")) {
		die("ERROR: You don't have permission to alter an administrator.");
	}
	$q = "REPLACE INTO `permissions` (`user`,`client`,`gardens`,`permissions`) VALUES ".
		 "('$username', '$client', '$gardens', '$permissions')";
	iLog("Prestes a alterar o user '$username'\n$q");
	$res = mysql_query($q);
	if ($res) {
		die("OK");
	} else {
		die("mysql_affected_rows != 1");
	}
}


if( isset( $_POST['action'])) {
	switch($_POST['action']) {
		case "apagarPrograma":		apagarPrograma($_POST["id"]);								break;
		case "desactivarPrograma":	desactivarPrograma($_POST["id"]); 							break;
		case "activarPrograma":		activarPrograma($_POST["id"]); 								break;
		case "getPrograma":			getPrograma($_POST["id"]); 									break;
//		case "getProgramaJSON":		getProgramaJSON(); 											break;
		case "getEditPrograma":		getEditPrograma(); 											break;
		case "setPrograma":			savePrograma($_POST["id"],$_POST["data"]); 					break;
		case "getListaProgramas":	getListaProgramas(); 										break;
		case "updateMarker":		setGardenPlaces($_POST["id"],$_POST["lat"],$_POST["lng"]);	break;
		case "actJardins":			actDeactJardins("*","act");									break;
		case "desactJardins":		actDeactJardins("*","deact");								break;
		case "actJardim":			actDeactJardins($_POST["id"],"act");						break;
		case "desactJardim":		actDeactJardins($_POST["id"],"deact");						break;
		case "updateUser":			updateUser($_POST["user"],$_POST["g"],$_POST["p"]);			break;		
		default:					die("UNKNOWN COMMAND\n\n".print_r($_POST,true));
	}
} else {
	actDeactJardins("act");
	//die("ACTION COMMAND NEED");
}



?>