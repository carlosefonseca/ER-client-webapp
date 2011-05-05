<link rel="stylesheet" type="text/css" href="../common/css/tablesorter/tablesorter.css" />
<link rel="stylesheet" type="text/css" href="../common/css/tooltip.css" />
<script type="text/javascript" src="../common/js/jquery.tablesorter.min.js"></script>
<?
global $client;
global $params;

if(isset($params)) {
	$file = u("pages/admin/".cleanString($params).".php");
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
	<ul><li><a href="<? L("admin/jardinsFile2DB");?>">Jardins</a></li></ul>
</div>
<?
}