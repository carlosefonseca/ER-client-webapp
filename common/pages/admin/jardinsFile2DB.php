<?
if (!hasPermission("gardens")) {
	die('<meta http-equiv="refresh" content="0;'.url("status").'">');
}
global $title;
$title = "Alterar Jardins";
?>
<div class="content" style="">
<?
$page = url("admin/jardinsFile2DB");
iLog($page);

$master = parseMaster("serverfiles/MastersList.txt");


// Acções
if (isset($_GET["add"]) && !empty($_GET["add"])) {
	# Adicionar o jardim especificado do txt para a DB
	foreach($master as $v) {
		if (strpos($_GET["add"], $v["ident"]) !== false) {
			$q = "INSERT INTO `jardins` (`client`,`id`,`acronym`,`name`,`contact`) VALUES
('$client', ".$v["n"].", '".$v["ident"]."', '".$v["nome"]."', '".$v["contacto"]."')";

			$coiso = "<meta http-equiv='refresh' content='0;$page' />";
			iLog($coiso);
			$res = mysql_query($q) or die($coiso);
		}
	}
} else if (isset($_GET["updatedata"])) {
	updateDbWithMasterFile($master, $client);
	die("<meta http-equiv='refresh' content='0;$page' />");
}



?>	<h2>Importação de Jardins do ficheiro "MastersList.txt" para a base de dados</h2>
<p>Os dados na tabela abaixo foram obtidos do ficheiro MasterList.txt.<br>Se este ficheiro for alterado, a base de dados deve ser actualizada. <br />Ao actualizar, as posições de GPS não se perdem.</p> <?
#conjugação entre o ficheiro Masters e a DB
$q = "SELECT id, lat, acronym FROM jardins WHERE client LIKE '$client'".getUserGardens(true);
$res = mysql_query($q) or die("$q => ".mysql_error());
$indb = "";		// txt com todos os acronimos dos jardins na DB
$hasGPS = "";	// txt com todos os acronimos dos jardins que tenham GPS
$ids = array();	// [acronimo] = DB-id
while($row = mysql_fetch_array($res)) {
	$indb .= $row["acronym"]." ";		
	$ids[$row["acronym"]] = $row["id"];	
	if ($row["lat"]) {
		$hasGPS.=$row["acronym"]." ";
	}
}

$toAdd ="";
foreach($master as $k => $v) {	// master -> array com as cenas do TXT
	if ($k == 0) { 	//header
		$master[0]["inDB"] = "Na BD";
		$master[0]["hasGPS"] = "Com GPS";
		continue;
	}

#			$master[$k]["dbid"] = $ids[$v["ident"]];

	// coloca os textos que dizem se está na DB e se tem GPS
	if (strpos($indb, $v["ident"]) !== false) {
		$master[$k]["inDB"] = "Sim";
		if (strpos($hasGPS, $v["ident"]) !== false) {
			$master[$k]["hasGPS"] = "Sim. <a href='javascript:editGPS(".$ids[$v["ident"]].",\"".$v["nome"]."\")'>Editar</a>";
		} else {
			$master[$k]["hasGPS"] = "Não. <a href='javascript:addGPS(".$ids[$v["ident"]].",\"".$v["nome"]."\")'>Adicionar</a>";
		}
	} else {
		$master[$k]["inDB"] = "Não. <a href='".url("admin/jardinsFile2DB&add=".$v["ident"])."'>Adicionar</a>";
		$toAdd.=$v["ident"].",";
		$master[$k]["hasGPS"] = "&mdash;";
	}
}

if (strlen($toAdd)>0) {
	echo "<a href='".url("admin/jardinsFile2DB&add=".$toAdd)."'>Adicionar todos à BD</a>";
}
echo "<p><a href='$page&updatedata'>Actualizar todos os dados da DB com os dados do ficheiro</a></p>";

// Constroi a tabela
echo array2table($master, true);
?>


<div id="editGPS">
<? 	global $canEditMarkers;
$canEditMarkers = true;
//include(u("pages/map.php")); ?>
</div>

</div>
<script language="javascript">
	var editID = -1;
	var marker;
	var objs = Object();
	objs['Fechar'] = function() {window.location.reload();};
	objs['drop'] = function() {
			if (marker) {
				marker.setPosition(map.getCenter());
			} else {
				marker = createMarker(map, false, editID, "", "", "");
				marker.enableDragging();
			}
			updateLocation(marker);
		};
	$("#editGPS").dialog({
		autoOpen: false,
		width: "90%",
		height: "600",
		modal: true,
	});
	
	function addGPS(id, nome) {
		$("#editGPS").dialog("option","buttons",{"Fechar":objs['Fechar']});
		$("#editGPS").dialog("option", "title",nome).dialog("open");
		$("#editGPS").html("<iframe style='width:100%;height:100%' src='<? l('admin/editGPS');?>&full&id="+id+"'></iframe>");
	}
	
	function editGPS(id, nome) {
		$("#editGPS").dialog("option","buttons",{"Fechar":function(){$(this).dialog("close");}});
		$("#editGPS").dialog("option","title",nome).dialog("open");
		$("#editGPS").html("<iframe style='width:100%;height:100%' src='<? l('admin/editGPS');?>&full&id="+id+"'></iframe>");
	}
	$("table.autogen").tablesorter({
		widgets: ['zebra']
	});
</script>