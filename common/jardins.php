<? iLog("<jardins>"); ?>
<? 	# ISTO DEVIA SER SO' UM OBJECTO JSON E A LOGICA DEVIA TAR TODA NO MAP.PHP...
	# mas pronto assim ficam mais coisas na parte do php
	# OUTPUTS JAVASCRIPT CODE FOR SETTING GMAP2 MARKERS AND CENTER

require("../common/DBconnect.php");
require_once("../common/funcoes.php");
//session_start();
require_once("../common/user.php");
requireLogin();
global $client;

$id = false;
if(isset($_GET["id"])) {
	$id = mysql_real_escape_string($_GET["id"]);
}


$q = "SELECT * FROM jardins WHERE client LIKE '$client'".getUserGardens(true)." AND lat <> 0";
if ($id !== false) {$q.=" AND id = '$id'";}
$res = mysql_query($q) or die("LN11: $q => ".mysql_error());
iLog("Select jardins: ".mysql_num_rows($res));


if(mysql_num_rows($res)!=0):

	$data = parseDataFiles("serverfiles");
/*	$status = parseDataFile("dados/status.txt");
	if ($status == NULL) {die("ERROR: STATUS INFO NOT AVAILABLE");}

	$active = parseDataFile("dados/activos.txt");
	if ($active == NULL) {die("ERROR: ACTIVE INFO NOT AVAILABLE");}
*/
	//$status = $data
//	echo print_r($data,true);

	$max_lat = $max_lng = -9999;
	$min_lat = $min_lng =  9999;
	
	while ($r = mysql_fetch_assoc($res)) {
		$id = $r['id'];
		$jid = 'j'.$id;
		$dataj = $data["status"][$jid];
		$st = ($dataj["activo"]*1)?$dataj["erros"]:"off";

		$slaves = "";
		for($i=0;$i< strlen($dataj["estado"]);$i++) {
			$slaves .= "<span class='s".$dataj["estado"][$i]."'>".($i+1)."</span> ";
		}

		$sectores = "";
		if ($data["secAct"][$jid] != null) {
			foreach($data["secAct"][$jid] as $n => $s) {
				$sectores .= "<span class='s$s'>$n</span> ";
			}
		}

		$programas = $data["progAct"][$jid];
		$mmT1 = $data["mmT1"][$jid];
		$c24h = $data["c24h"][$jid]["caudal"];
		$variacao = $data["c24h"][$jid]["variacao"];
		$cTotal = $data["cTotal"][$jid];

		switch($st) {
			case "0": 	$estado = "OK"; break;
			case "off":	$estado = "Desactivado"; break;
			case "1": 	$estado = "1 erro!"; break;
			default: 	$estado = $st." erros!";
		}


		$more = "<table border='0'>".
				"<tr><th>Estado:</th><td>$estado</td></tr>".
				"<tr><th>Slaves:</th><td>$slaves</td></tr>".
				"<tr><th>Sectores:</th><td>$sectores</td></tr>".
//				"<tr><th>Progs Activos:</th><td>$programas</td></tr>".
//				"<tr><th>Tipo 1:</th><td>$mmT1 mm</td></tr>".
//				"<tr><th>Caudal 24h:</th><td>$c24h ($variacao)</td></tr>".
//				"<tr><th>Caudal Total:</th><td>$cTotal</td></tr>".
				"</table>";


		echo "createMarkerAndPoint(map, ".$r['lat'].",".$r['lng'].", '".$r['id']."', '".utf8_encode($r['name'])."', '".$st."', \"". $more."\");\n";
		if ($r['lat'] && $r['lng']) {
    		if ($r['lat'] < $min_lat) { $min_lat = $r['lat']; }
    		if ($r['lat'] > $max_lat) { $max_lat = $r['lat']; }
    		if ($r['lng'] < $min_lng) { $min_lng = $r['lng']; }
    		if ($r['lng'] > $max_lng) { $max_lng = $r['lng']; }
    	}
	}
	
	//echo "// $max_lat $min_lat\n//$max_lng $min_lng";
	$c_lat = ($max_lat + $min_lat)/2;
	$c_lng = ($max_lng + $min_lng)/2;
	
	if ($c_lat || $c_lng) {
		echo "center = new GLatLng('".str_replace(",", ".", $c_lat)."' , '".str_replace(",", ".", $c_lng)."');\n";
		?>
map.setCenter(center);
map.savePosition();
<?	}	
endif; ?>
<? iLog("</jardins>"); ?>