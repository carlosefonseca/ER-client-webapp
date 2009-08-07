<?
if(!isset($client)) { echo '<meta http-equiv="refresh" content="0;.." />'; die(); }

include("../common/funcoes.php");
session_start(); 
include(u("user.php"));
global $logged_in;
global $page;
global $params;

if(isset($_GET["q"])) {
	$p = $_GET["q"];
	if ($p == "newaccount" || $logged_in) {
	    $page = strtr($p, "./", "  ");
		$pages = explode("-", $page, 2);
	    $file = u("pages/". $pages[0].".php");
		$params = $pages[1];
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
		echo "404 Page not found! Página não encontrada!";
	}
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title></title>
	<link rel="stylesheet" type="text/css" href="../common/css/style.css" />
	<link rel="stylesheet" type="text/css" href="../common/css/jquery-ui.css" />
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
			<li><a href="user">Conta</a></li>
			<li><a href="logout">Terminar sessão</a></li>
		</div>
		<? /*<div id="userperm"><? echo $_SESSION['permissions'];?></div>*/?>
		<div class="primary-menu">
			<li class="first"><a href="status#content">Estado</a></li>
			<li><a href="data">Dados Locais</a></li>
			<li><a href="meteo">Meteorologia</a></li>
			<li><a href="">Advertisinsg</a></li>
		</div>
<? endif; ?>
	</div><!-- //header -->
	
	<div class="mainWrap <? echo $page;?>">
		<a name="content"></a>
		<? loadContent($file); ?>
	</div>

	<div id="message"></div>

	<div class="footer">&copy; EngiRega 2009 <small>| <a href="changelog">v0.7b</a></small></div>
</body>
</html>
