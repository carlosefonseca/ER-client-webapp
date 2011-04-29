<link rel="stylesheet" type="text/css" href="../common/css/tablesorter/tablesorter.css" />
<link rel="stylesheet" type="text/css" href="../common/css/tooltip.css" />
<script type="text/javascript" src="../common/js/jquery.tablesorter.min.js"></script>
<div class="content" style="">
<?
global $client;
global $params;

$page;

if(isset($_GET["section"]) || isset($params)) {

	if ($params == "jardinsFile2DB" || $_GET["section"] == "jardinsFile2DB") {
		include("admin/jardinsFile2DB.php");
	}
} else { 
		iLog("Showing Admin Options");
?>
		<h2>Opções</h2>
		<ul><li><a href="<? L("admin/jardinsFile2DB");?>">Jardins</a></li></ul>
<? }