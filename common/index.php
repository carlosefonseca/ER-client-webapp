<?
/*ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);
ini_set('log_errors',TRUE);
ini_set('html_errors',FALSE);
ini_set('error_log','/Users/carlos/Sites/Engirega/app.log');
ini_set('display_errors',FALSE);
*/

//if(!isset($client)) { echo '<meta http-equiv="refresh" content="0;.." />'; die(); }

include("../common/funcoes.php");
session_start(); 
include(u("user.php"));
global $logged_in;
global $page;
global $params;

$page = "";
$file = "";

if(preg_match('/MSIE [0-7]/i',$_SERVER['HTTP_USER_AGENT'])) { 
	$file = u("pages/oldie.php");
} else

if(isset($_GET["q"])) {
	$p = $_GET["q"];
	if ($p == "newaccount" || $logged_in) {
		$page = strtr($p, ".", "  ");
		$pages = explode("/", $page, 2);
		$file = u("pages/". $pages[0].".php");
		if (count($pages) > 1) {
			$params = $pages[1];
		}
	} else {
		$page = "login";
		$file = u("pages/login.php");
	}
} else {
	if ($logged_in) {
		$page = "status";
		$file = u("pages/status.php");
	} else {
		$page = "login";
		$file = u("pages/login.php");
	}
}



$title = "";
//Guarda todo o output do conteúdo da pagina para inserir no HTML em baixo
ob_start();
loadContent($file);
$body = ob_get_contents();
ob_end_clean();

if ($title != '') {
	$title .= " &ndash; ";
}
$title .= "$name &ndash; EngiRega";

function loadContent($file) {
	if(file_exists($file)) {
		include($file);
	} else {
		echo "404 Page '$file' not found! Página '$file' não encontrada!";
	}
}

iLog("\n".date("Y-m-d H:i:s").": REQ. ".($logged_in?$_SESSION['username'].((hasPermission("users")||hasPermission("gps"))?"(A)":""):"!LI")." PAGE:$file PARMS: '$params'");

?><!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8" >
	<meta charset="utf-8"/> 
	<title><?= $title ?></title>
	<link rel="stylesheet" type="text/css" href="../common/css/jquery-ui.css" />
	<link rel="stylesheet" type="text/css" href="../common/css/style.css" />
	<script src="../common/js/basicFunctions.js" type="text/javascript"></script>
	<script src="../common/js/jquery.js" type="text/javascript"></script>
	<script src="../common/js/jquery-ui.js" type="text/javascript"></script>
</head>
<? if (isset($_GET['full'])):		################################# content only ?>
<body class="full">
<?= $body; ?>
<? else: 							######################### Full page ?>
<body>
	<div class="header">
		<div class="logo">
			<div class="engirega">EngiRega</div>
			<div class="client"><a href="."><? echo $name; ?></a></div>
		</div>
<? if ($logged_in): ?>
		<div class="user">
			<li class="first"><? echo $_SESSION['username'];?></li>
			<li><a href="<? L("user");?>">Conta</a></li>
<? if(hasPermission("users")||hasPermission("gps")) { ?> <li><a href="<? L("admin");?>">Administração</a></li> <? } ?>
			<li><a href="<? L("logout");?>">Terminar sessão</a></li>
		</div>
		<? /* <div id="userperm"><? echo $_SESSION['permissions'];?></div> */ ?>
		<div class="primary-menu">
			<li class="first"><a href="<? L("status#content"); ?>">Mapa de Estado</a></li>
			<li><a href="<? L("data");?>">Dados Locais</a></li>
			<li><a href="<? L("meteo");?>">Informação Meteorológica</a></li>
			<!--<li><a href="">Advertising</a></li>-->
		</div>
<? endif; ?>
	</div><!-- //header -->
	
	<div class="mainWrap <? echo strtr($page, "/", "-");?>">
		<a name="content"></a>
		<?= $body ?>
	</div>

	<div id="message"></div>

	<div class="footer">&copy; EngiRega 2011 <small>| <a href="<? l("changelog");?>">v0.9 RC1</a></small></div>
<? endif; ?>
<div id="alert"></div>
</div>
</body>