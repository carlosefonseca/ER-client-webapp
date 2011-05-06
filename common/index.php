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

function loadContent($file) {
	if(file_exists($file)) {
		include($file);
	} else {
		echo "404 Page '$file' not found! Página '$file' não encontrada!";
	}
}

iLog("\n".date("Y-m-d H:i:s").": REQ. ".($logged_in?$_SESSION['username'].(hasPermission("admin")?"(A)":""):"!LI")." PAGE:$file PARMS: '$params'");

if (isset($_GET['full'])):
?><!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title></title>
	<link rel="stylesheet" type="text/css" href="../common/css/style.css" />
	<link rel="stylesheet" type="text/css" href="../common/css/jquery-ui.css" />
	<script src="../common/js/basicFunctions.js" type="text/javascript"></script>
	<script src="../common/js/jquery.js" type="text/javascript"></script>
	<script src="../common/js/jquery-ui.js" type="text/javascript"></script>
</head>
<body class="full">
<? loadContent($file); ?>
<div id="alert"></div>
</div>
</body>

<? else:

?><!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title></title>
	<link rel="stylesheet" type="text/css" href="../common/css/style.css" />
	<link rel="stylesheet" type="text/css" href="../common/css/jquery-ui.css" />
	<script src="../common/js/basicFunctions.js" type="text/javascript"></script>
	<script src="../common/js/jquery.js" type="text/javascript"></script>
	<script src="../common/js/jquery-ui.js" type="text/javascript"></script>
</head>
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
<? if(hasPermission("admin")) { ?> <li><a href="<? L("admin");?>">Administração</a></li> <? } ?>
			<li><a href="<? L("logout");?>">Terminar sessão</a></li>
		</div>
		<? /* <div id="userperm"><? echo $_SESSION['permissions'];?></div> */ ?>
		<div class="primary-menu">
			<li class="first"><a href="<? L("status#content"); ?>">Mapa de Estado</a></li>
			<li><a href="<? L("data");?>">Dados Locais</a></li>
			<li><a href="<? L("meteo");?>">Meteorologia</a></li>
			<!--<li><a href="">Advertising</a></li>-->
		</div>
<? endif; ?>
	</div><!-- //header -->
	
	<div class="mainWrap <? echo $page;?>">
		<a name="content"></a>
		<? loadContent($file); ?>
	</div>

	<div id="message"></div>

	<div class="footer">&copy; EngiRega 2011 <small>| <a href="<? l("changelog");?>">v0.9b</a></small></div>
	<div id="alert"></div>
	</div>
</body>
</html>
<? endif;