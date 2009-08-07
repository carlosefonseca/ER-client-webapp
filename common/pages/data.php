<? 
global $client;

$data = parseDataFiles("serverfiles");

function echoValOrND($v, $unid = "", $hasSpan = true) {
	$ND = "<span title='Dados não disponiveis'>N/D</span>";
	if ($v != "") {
		if($hasSpan) {
			echo "<span>$v</span> $unid";
		} else {
			echo "$v $unid";
		}
	} else {
		echo $ND;
	}
}

//GET GARDENS
require_once("../common/DBconnect.php");
$q = "SELECT id, name, status FROM jardins WHERE client = '$client' ".getUserGardens(true)." ORDER BY id";
$res = mysql_query($q);

?>
<link rel="stylesheet" type="text/css" href="../common/css/tablesorter/tablesorter.css" />
<link rel="stylesheet" type="text/css" href="../common/css/tooltip.css" />
<script src="../common/js/jquery.tooltip.js"></script>

<div class="content" style="">
	<table id="data" class="tablesorter">
		<thead>
			<tr>
				<th>#</th>
				<th>Jardim</th>
				<th>Último Relatório</th>
				<th title="Estado de cada slave - Verde:OK | Vermelho:Problema">Estado Slaves</th>
<? #				<th title="Número de Slaves em erro">Anómalias</th>?>
				<th title="Sectores activos e inactivos - Verde:Activo | Vermelho:Inactivo">Sectores </th>
				<th title="Número de programas activos em cada jardim">Programas</th>
				<th title="Caudal de água gasta nas regas do tipo 1">Caudal Tipo 1</th>
				<th title="Caudal total nas últimas 24h">Total 24h</th>
				<th title="Variação do caudal total em relação ao dia anterior">Dia anterior</th>
				<th title="Volume total gasto por jardim">Volume total</th>
			</tr>
		</thead>


		<tbody>
<? while($r = mysql_fetch_array($res)):
	$id=$r["id"]; $jId="j".$id;
	$st = $data["status"]["j".$id];
	
	$name = utf8_encode($r["name"]);
	$report = ($st["data"]!=""&&$st["hora"]!="")?$st["data"]." - ".$st["hora"]:'';

//	$status = $st["estado"];
	$slaves = "";
	for($i=0;$i< strlen($st["estado"]);$i++) {
		$slaves .= "<span class='s".$st["estado"][$i]."'>".($i+1)."</span> ";
	}

	$anomalias = $st["erros"];
	
	$sectores = "";
	if ($data["secAct"][$jId] != null) {
		foreach($data["secAct"][$jId] as $n => $s) {
			$sectores .= "<span class='s$s'>$n</span> ";
		}
	}
	
	$programas = $data["progAct"][$jId];
	$mmT1 = $data["mmT1"][$jId];
	$c24h = $data["c24h"][$jId]["caudal"];
	$variacao = $data["c24h"][$jId]["variacao"];
	$cTotal = $data["cTotal"][$jId];

	?>
			<tr>
				<td class="id"	><? echoValOrND($id);			?></td>
				<td class="name"><? echoValOrND($name);			?></td>
				<td class="time"><? echoValOrND($report); 		?></td>
				<td class="stat"><? echoValOrND($slaves,'',0); 		?></td>
<? /*				<td class="anom"><? echoValOrND($anomalias);	?></td>*/ ?>
				<td class="sact"><? echoValOrND($sectores,'',0);?></td>
				<td class="prog"><? echoValOrND($programas);	?></td>
				<td class="mmt1"><? echoValOrND($mmT1, "mm");	?></td>
				<td class="c24h"><? echoValOrND($c24h, "m<sup>3</sup>"); 	?></td>
				<td class="cvar"><? echoValOrND($variacao,"m<sup>3</sup>");	?></td>
				<td class="ctot"><? echoValOrND($cTotal, "m<sup>3</sup>");	?></td>
					<? /*<td class=""><span><? $t=sec2time($data["tempoultimarega"][$jId]); printf("%d:%02d",$t["hours"]*60+$t["minutes"],$t["seconds"]);?></span></td>*/?>
			</tr>
<? endwhile; ?>
		</tbody>


		<tfoot>
<? /*			<tr>
				<th colspan="5" class="space"></th>
				<th>Média:</th>
				<th><? echo $data["caudalmm"]["media"];?> m<sup>3</sup>/mm</th>
				<th><? echo $data["caudal"]["media"];?> m<sup>3</sup></th>
				<th><? $t=sec2time($data["tempoultimarega"]["media"]);
						printf("%d:%02d",$t["hours"]*60+$t["minutes"],$t["seconds"]);?></th>
				<th><? echo $data["ultimarega"]["media"];?> mm</th>
			</tr> */ ?>
			<tr class="last">
				<th colspan="5" class="space"></th>
				<th>Total:</th>
				<th><? echo $data["mmT1"]["total"];?> m<sup>3</sup>/mm</th>
				<th colspan="2"><? echo $data["c24h"]["total"];?> m<sup>3</sup></th>
				<th><? echo $data["cTotal"]["total"];?> m<sup>3</sup></th>
<? /*				<th><? $t=sec2time($data["tempoultimarega"]["total"]);
						printf("%d:%02d",$t["hours"]*60+$t["minutes"],$t["seconds"]);?></th>*/?>
			</tr>
		</tfoot>
	</table>
</div>


<script type="text/javascript" src="../common/js/jquery.tablesorter.min.js"></script>
<script>
	$("#data").tablesorter({
		textExtraction: function(node) { 
			// extract data from markup and return it  
			return $(node).find("span").html();
		},
		widgets: ['zebra'],
		sortList: [[0,0]]
	});
	$("#data th").tooltip({showBody:" - ",track: true,delay: 0, fade: 250,positionLeft: true});
</script>