<? 
global $client;
function parseDataFile($fn) {
	if(!file_exists($fn)) { return NULL; }
	$fp = fopen($fn, "r");
	$data = fread($fp, filesize($fn));
	fclose($fp);
	$array = explode("\n",str_replace("\r", "", $data));

	$out = array();
	$out["var"]=$array[0];
	$out["unid"]=$array[1];
	$out["total"]=$array[2];
	$out["media"]=$array[3];
	
	for($i = 8; $i<count($array); $i++) {
		$out["j".($i-7)] = ($array[$i]*1);
	}
	return $out;
}


//GET DATAFILES
$files = array("caudal","ultimarega","tempoultimarega","caudalmm");
$data = array();
foreach($files as $f) {
	$data[$f] = parseDataFile("dados/$f.txt");
}

//GET GARDENS
require_once("../common/DBconnect.php");
$q = "SELECT id, name, status FROM jardins WHERE client = '$client' ".getUserGardens(true)." ORDER BY id";
$res = mysql_query($q);


?>
<link rel="stylesheet" type="text/css" href="../common/css/tablesorter/tablesorter.css" />
<div class="content" style="">
<p style="font-size:larger;text-align:center"><strong>Nota: Dados de Teste (Não reais)</strong></p>
	<table id="data" class="tablesorter">
		<thead>
			<tr>
				<th>Jardim</th>
				<th>Estado</th>
				<th>Último Report</th>
				<th>Anómalias</th>
				<th>Água gasta por mm</th>
				<th>Água gasta</th>
				<th>Tempo Última Rega</th>
				<th>Última rega</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th colspan="3" class="space"></th>
				<th>Média:</th>
				<th><? echo $data["caudalmm"]["media"];?> m<sup>3</sup>/mm</th>
				<th><? echo $data["caudal"]["media"];?> m<sup>3</sup></th>
				<th><? $t=sec2time($data["tempoultimarega"]["media"]);
						printf("%d:%02d",$t["hours"]*60+$t["minutes"],$t["seconds"]);?></th>
				<th><? echo $data["ultimarega"]["media"];?> mm</th>
			</tr>
			<tr class="last">
				<th colspan="3" class="space"></th>
				<th>Total:</th>
				<th><? echo $data["caudalmm"]["total"];?> m<sup>3</sup>/mm</th>
				<th><? echo $data["caudal"]["total"];?> m<sup>3</sup></th>
				<th><? $t=sec2time($data["tempoultimarega"]["total"]);
						printf("%d:%02d",$t["hours"]*60+$t["minutes"],$t["seconds"]);?></th>
				<th><? echo $data["ultimarega"]["total"];?> mm</th>
			</tr>
		</tfoot>
		<tbody>
<? while($r = mysql_fetch_array($res)): 	$id=$r["id"];?>
			<tr>
				<td><span><? echo utf8_encode($r["name"]);?></span></td>
				<td><span><? echo strtoupper($r["status"]);?></span></td>
				<td><span><? echo "$id:00:00"; ?></span></td>
				<td><span><? echo $id;?></span></td>
				<td><span><? echo $data["caudalmm"]["j".$id]; ?></span> m<sup>3</sup>/mm</td>
				<td><span><? echo $data["caudal"]["j".$id]; ?></span> m<sup>3</sup></td>
				<td><span><? $t=sec2time($data["tempoultimarega"]["j".$id]);
							 printf("%d:%02d",$t["hours"]*60+$t["minutes"],$t["seconds"]);?></span></td>
				<td><span><? echo $data["ultimarega"]["j".$id]; ?></span> mm</td>
			</tr>
<? endwhile; ?>
		</tbody>
	</table>
</div>


<script type="text/javascript" src="../common/js/jquery.tablesorter.min.js"></script>
<script>
    $("#data").tablesorter({
		textExtraction: function(node) { 
            // extract data from markup and return it  
           	return node.childNodes[0].innerHTML;
        },
        widgets: ['zebra']
	});
</script>