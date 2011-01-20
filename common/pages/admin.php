<link rel="stylesheet" type="text/css" href="../common/css/tablesorter/tablesorter.css" />
<link rel="stylesheet" type="text/css" href="../common/css/tooltip.css" />
<script type="text/javascript" src="../common/js/jquery.tablesorter.min.js"></script>
<div class="content" style="">
<?
global $client;
global $params;

if(isset($_GET["section"]) || isset($params)) {

	if ($_GET["section"] == "jardinsFile2DB" || $params == "jardinsFile2DB") {

		$master = parseMaster("serverfiles/MastersList.txt");

		if (isset($_GET["add"]) && !empty($_GET["add"])) {
			# Adicionar o jardim especificado do txt para a DB
			foreach($master as $v) {
				if (strpos($_GET["add"], $v["ident"]) !== false) {
					$q = "INSERT INTO `jardins` (`client`,`id`,`acronym`,`name`,`contact`) VALUES
('$client', ".$v["n"].", '".$v["ident"]."', '".$v["nome"]."', '".$v["contacto"]."')";

					$res = mysql_query($q) or die("$q => ".mysql_error());
				}
			}
			echo '<meta http-equiv="refresh" content="0;admin&adminAction=jardinsFile2DB" />';
			die();
		}

	

?>	<h2>Importação de Jardins do ficheiro "MastersList.txt" para a base de dados</h2> <?
		#conjugação entre o ficheiro Masters e a DB
		$q = "SELECT id, lat, acronym FROM jardins WHERE client LIKE '$client'".getUserGardens(true);
		$res = mysql_query($q) or die("$q => ".mysql_error());
		$indb = "";
		$hasGPS = "";
		$ids = array();
		while($row = mysql_fetch_array($res)) {
			$indb .= $row["acronym"]." ";
			$ids[$row["acronym"]] = $row["id"];
			if ($row["lat"]) {
				$hasGPS.=$row["acronym"]." ";
			}
		}

		$toAdd ="";
		foreach($master as $k => $v) {
			if ($k == 0) { //header
				$master[0]["inDB"] = "Na BD";
				$master[0]["hasGPS"] = "Com GPS";
				continue;
			}

#			$master[$k]["dbid"] = $ids[$v["ident"]];

			if (strpos($indb, $v["ident"]) !== false) {
				$master[$k]["inDB"] = "Sim";
				if (strpos($hasGPS, $v["ident"]) !== false) {
					$master[$k]["hasGPS"] = "Sim. <a href='javascript:editGPS(".$ids[$v["ident"]].",\"".$v["nome"]."\")'>Editar</a>";
				} else {
					$master[$k]["hasGPS"] = "Não. <a href='javascript:addGPS(".$ids[$v["ident"]].",\"".$v["nome"]."\")'>Adicionar</a>";
				}
			} else {
				$master[$k]["inDB"] = "Não. <a href='admin&adminAction=jardinsFile2DB&add=".$v["ident"]."'>Adicionar</a>";
				$toAdd.=$v["ident"].",";
				$master[$k]["hasGPS"] = "&mdash;";
			}
		}
		echo array2table($master, true);
		if (strlen($toAdd)>0) {
		echo "<a href='admin&adminAction=jardinsFile2DB&add=".$toAdd."'>Adicionar todos à BD</a>"; }?>
		

<div id="editGPS" style="display:none">
<? 	global $canEditMarkers;
	$canEditMarkers = true;
	include("map.php"); ?>
</div>

<script language="javascript">
	var editID = -1;
	var marker;
	var objs = Object();
	objs['Fechar'] = function() {window.location.reload();};
	objs['Colocar Marcador Aqui'] = function() {
				if (marker) {
					marker.setLatLng(map.getCenter());
				} else {
					marker = createMarker(map, false, editID, "", "", "");
					displayMarker(map, marker);
					marker.enableDragging();
				}
				updateLocation(marker);
			};
	$("#editGPS").dialog({
		autoOpen: false,
		width: 800,
		height: 500,
		modal: true,
	});

	function addGPS(id, nome) {
		$("#editGPS").dialog("option","buttons",{"Fechar":objs['Fechar'],"Colocar Marcador Aqui":objs['Colocar Marcador Aqui']})
		$("#editGPS").dialog("option", "title",nome).dialog("open");
		editID = id;
		map.checkResize();
	}
	function editGPS(id, nome) {
		$("#editGPS").dialog("option","buttons",{"Fechar":function(){$(this).dialog("close");},"Reposicionar Marcador Aqui":objs['Colocar Marcador Aqui']});
		$("#editGPS").dialog("option", "title",nome).dialog("open");
		editID = id;
		var markers = new Object();
		loadJardim(id);
//		markers[id].enableDragging();
		map.checkResize();
	}
	$("table.autogen").tablesorter({
		widgets: ['zebra']
	});
</script>
		
<?	} #	if ($_GET["section"] == "jardinsFile2DB")
} # if(isset($_GET["section"]))

else { ?>
		<h2>Opções</h2>
		<ul><li><a href="<? L("admin/jardinsFile2DB");?>">Jardins</a></li></ul>
	<?
}