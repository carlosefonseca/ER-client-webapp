<? 	# ISTO DEVIA SER SO' UM OBJECTO JSON E A LOGICA DEVIA TAR TODA NO MAP.PHP...
	# mas pronto assim ficam mais coisas na parte do php
	# OUTPUTS JAVASCRIPT CODE FOR SETTING GMAP2 MARKERS AND CENTER

require("../common/DBconnect.php");
require_once("../common/funcoes.php");
iLog("<jardins>");
if (!isset($_SESSION)) session_start();
require_once("../common/user.php");
global $client;

if (!headers_sent()) {
	header('Content-Type: text/html; charset=utf-8');
}

$id = false;
if(isset($_GET["id"])) {
	$id = mysql_real_escape_string($_GET["id"]);
}


$q = "SELECT * FROM jardins WHERE client LIKE '$client'".getUserGardens(true);
if ($id !== false) {$q.=" AND id = '$id'";}
$res = mysql_query($q) or die("$q => ".mysql_error());
iLog("Select jardins: ".mysql_num_rows($res));

$jardins = array();

if(mysql_num_rows($res)!=0):

	$data = parseDataFiles("serverfiles");

#	iLog(print_r($data, true));

	$max_lat = $max_lng = -9999;
	$min_lat = $min_lng =  9999;
	
	while ($r = mysql_fetch_assoc($res)) {
		$id = $r['id'];	// index da BD
		$jid = 'j'.$id;	// index no array que vem dos ficheiros

		
		$jardins[$r['id']] = array(	'name'=> utf8_encode($r['name']),
									'lat' => $r['lat'], 'lng' => $r['lng']);

		if ($r['lat'] == "" || $r['lat'] == "0") {
			$jardins[$r['id']]['tmplat'] = getMapCenter("lat");
			$jardins[$r['id']]['tmplng'] = getMapCenter("lng");
		}

		if (!array_key_exists($jid, $data["status"])) {
			$dataj = array();
		} else {
			$dataj = $data["status"][$jid];

			$slaves = "";
			$slavesArr = array();
			for($i=0;$i< strlen($dataj["estado"]);$i++) {
				$slavesArr[$i+1] = $dataj["estado"][$i];
			}
			$jardins[$r['id']]['slaves'] = $slavesArr;
	
			$sectores = "";
			$sectoresArr = array();
			if ($data["secAct"][$jid] != null) {
				foreach($data["secAct"][$jid] as $n => $s) {
					$sectoresArr[$n] = $s;
				}
			}
			$jardins[$r['id']]['sectores'] = $sectoresArr;
		}

		if (array_key_exists($jid, $data["progAct"])) {
			$jardins[$r['id']]['programas'] = $data["progAct"][$jid];
		}
		if (array_key_exists($jid, $data["progAct"])) {
			$jardins[$r['id']]['mmT1'] = $data["mmT1"][$jid];
		}
		if (array_key_exists($jid, $data["c24h"])) {
			$jardins[$r['id']]['c24h'] = 	 $data["c24h"][$jid]["caudal"];
			$jardins[$r['id']]['variacao'] = $data["c24h"][$jid]["variacao"];
		}
		if (array_key_exists($jid, $data["cTotal"])) {
			$jardins[$r['id']]['cTotal'] = $data["cTotal"][$jid];
		}

		if (array_key_exists("activo", $dataj)) {
			$st = ($dataj["activo"]*1)?$dataj["erros"]:"off";	// estados: off ou numero de erros. 
			switch($st) {	// acho que isto está no JS... se estiver deve-se tirar
				case "0": 	$estado = "OK"; break;
				case "off":	$estado = "Desactivado"; break;
				case "1": 	$estado = "1 erro!"; break;
				default: 	$estado = $st." erros!";
			}
			$jardins[$r['id']]['status'] = $st;
			$jardins[$r['id']]['estado'] = $estado;
		}
	}
	
	if (!isset($_GET["id"])) {
		$q = "SELECT center_lat, center_lng FROM clientes WHERE name LIKE '$client'";
		$res = mysql_query($q) or die("SQL Error while getting center coords.<br>".mysql_error());
		$r = mysql_fetch_assoc($res);

		$out = array('jardins' => $jardins, 'map' => $r);
	} else {
		$out = $jardins;
	}
#	iLog(print_r($out, true));
	echo json_encode($out);
	
	$c_lat = ($max_lat + $min_lat)/2;
	$c_lng = ($max_lng + $min_lng)/2;
	
endif; 
iLog("</jardins>");