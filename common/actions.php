<?
require_once("../common/funcoes.php");

//Página requer que o utilizador se tenha autenticado
session_start();
require_once(u("user.php"));
checkLogin();

function hasPermissionToGarden() {
	global $client;
	if(!isset($_POST["garden"])) die("ERROR: No GARDEN set");
	$id = $_POST["garden"]; //Numero do Jardim
	
	//Verificar a existencia do jardim
	require_once("DBconnect.php");
	$q = "SELECT id FROM jardins WHERE client like '%$client%' AND id='$id'";
	$res=mysql_query($q) or die(mysql_error());

	if(mysql_num_rows($res)!=1 || (!hasPermission("j$j")&&!hasPermission("j*"))) {
		die("PERMISSION DENIED");
	}
	return $id;
}


function getPrograma($id) {
	$nJ = hasPermissionToGarden(); //Morre se o user nao tiver acesso ao jardim

	$id = normaliza($id);
	
	$progs = leProgramacao("programas/p$nJ.txt");
	if ($p = $progs[$id]) {
		die(json_encode($p->toBrowser()));
	} else {
		die("PROGRAM '$id' DOES NOT EXIST");
	}
}

function apagarPrograma() {
	if (!hasPermission("edit_program")) {
		die("PERMISSION DENIED");
	}
	
	$nJ = hasPermissionToGarden(); //Morre se o user nao tiver acesso ao jardim
	
	$progs = leProgramacao("programas/p$nJ.txt");
	unset($progs[$_POST["id"]]);
	escreveProgramacao($progs,"programas/p$nJ.txt");
	die("OK");
}

function getListaProgramas() {
	$nJ = hasPermissionToGarden(); //Morre se o user nao tiver acesso ao jardim

	if(!file_exists("programas/p$nJ.txt")) die("[]");
	$progs = leProgramacao("programas/p$nJ.txt");
	$a = array();
	foreach($progs as $key => $p) {
		$a[] = $p->activo.$p->nome;
	}
	die(json_encode($a));
}

function activarPrograma() {
	if(!isset($_POST["id"])) die("ERROR: No ID set");
	$nJ = hasPermissionToGarden(); //Morre se o user nao tiver acesso ao jardim
	$progs = leProgramacao("programas/p$nJ.txt");
	$p = $progs[$_POST["id"]];
	$p->activar();
	escreveProgramacao($progs, "programas/p$nJ.txt");
	die(json_encode($p->toBrowser()));
}

function desactivarPrograma() {
	if(!isset($_POST["id"])) die("ERROR: No ID set");
	$nJ = hasPermissionToGarden(); //Morre se o user nao tiver acesso ao jardim
	$progs = leProgramacao("programas/p$nJ.txt");
	$p = $progs[$_POST["id"]];
	$p->desactivar();
	escreveProgramacao($progs, "programas/p$nJ.txt");
	die(json_encode($p->toBrowser()));
}


function savePrograma() {	
	if(!isset($_POST["id"])) die("ERROR: No ID.");
	if(!isset($_POST["data"])) die("ERROR: No DATA.");
	
	$nJ = hasPermissionToGarden(); //Morre se o user nao tiver acesso ao jardim
	
	$d = str_replace(array("\\\"","\\\\","£"), array("\"","\\","+"), $_POST["data"]); 

	$p = new Programa();
    $p->recorrencia = new Recorrencia();
    $p->updateFromSite(json_decode($d));
    $newId = $p->getKey();

	$id = $_POST["id"];

	if(file_exists("programas/p$nJ.txt")) {
		$progs = leProgramacao("programas/p$nJ.txt");
	}
		
 	$progs[$newId] = $p;
	if ($id != $newId && $id != "NOVO") {
		unset($progs[$id]);
	}
	
	escreveProgramacao($progs, "programas/p$nJ.txt");
	die(json_encode($progs[$newId]->toBrowser()));
}

function setGardenPlaces() {
	if (!hasPermission("edit_markers")) {
		die("PERMISSION DENIED");
	}

	$id  = addslashes($_POST["id" ]);
	$lat = addslashes($_POST["lat"]);
	$lng = addslashes($_POST["lng"]);

	include("DBconnect.php");
	$q = "UPDATE  `jardins` SET  lat = '$lat', lng = '$lng' WHERE CONVERT(  `jardins`.`id` USING utf8 ) =  '$id' LIMIT 1 ;";
	mysql_query($q) or die("SQL Error");

	die("OK");
}
global $client;

if( isset( $_POST['action'])) {
	switch($_POST['action']) {
		case "apagarPrograma":		apagarPrograma();		break;
		case "desactivarPrograma":	desactivarPrograma(); 	break;
		case "activarPrograma":		activarPrograma(); 		break;
		case "getPrograma":			getPrograma($_POST["id"]); 	break;
//		case "getProgramaJSON":		getProgramaJSON(); 		break;
		case "getEditPrograma":		getEditPrograma(); 		break;
		case "setPrograma":			savePrograma(); 		break;
		case "getListaProgramas":	getListaProgramas(); 	break;
		case "updateMarker":		setGardenPlaces();		break;
		default:					die("UNKNOWN COMMAND\n\n".print_r($_POST,true));
	}
} else {
	die("ACTION COMMAND NEED");
}



?>