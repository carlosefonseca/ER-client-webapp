<link rel="stylesheet" type="text/css" href="../common/css/tablesorter/tablesorter.css" />
<link rel="stylesheet" type="text/css" href="../common/css/tooltip.css" />
<script type="text/javascript" src="../common/js/jquery.tablesorter.min.js"></script>
<?
global $client;
global $params;
global $title;
$title = "Administração";

if(!hasPermission("users") && !hasPermission("gardens")) {
	die('<meta http-equiv="refresh" content="0;'.url("status").'">');
}

$params = cleanString($params);

if(isset($params) && $params != "") {
	$file = u("pages/admin/".$params.".php");
	if (file_exists($file)) {
		include($file);
	} else {
		echo "404.";
	}
} else { 
		iLog("Showing Admin Options");
?>
<div class="content" style="">
	<h2>Opções</h2>
	<ul>
<? if (hasPermission("gardens")): ?>
	<li><a href="<? L("admin/jardinsFile2DB");?>">Jardins</a></li>
<? endif; if (hasPermission("users")): ?>
	<li><a href="<? L("admin/users");?>">Utilizadores</a></li>
<? endif; ?>
	</ul>
</div>
<?
}