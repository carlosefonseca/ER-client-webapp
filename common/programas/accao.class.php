<?
class Accao {
	var $sectores;
	var $duracao;
	
	function duracaoHumana() {
		$tempo = sec2time($this->duracao);
		return sprintf("%02d:%02d", $tempo["minutes"], $tempo["seconds"]);
	}
	
	function getReadableArray() {
		$out = array();
		$out["duracao"] = $this->duracaoHumana();
		$out["sectores"] = addslashes($this->sectores);
		return $out;
	}
	
	function setFromWeb($t) {
		$this->duracao = sprintf("%06d",time2sec($t->duracao));
		$this->sectores = $t->sectores;
	}
	/*
	function getCoord() {
//		echo $this->sectores;
		$sectrs = preg_split("/[^\d]+/", $this->sectores);
		if (!count($sectrs)) return "";
		$q = "SELECT * FROM jardins WHERE client = '$client' AND ";
		foreach($sectrs as $s) {
			$q.= "id = '".$s."' OR ";
		}
		$q = substr($q, 0 , -3);
		include_once(u("DBconnect.php"));
//		echo "((($q)))";
		$res = mysql_query($q) or die(mysql_error());
		$out = "";
		while ($r = mysql_fetch_array($res)) {
			$out .=  "<marker id=\"".$r['id']."\" nome=\"".htmlentities($r["name"]).
					 "\" lat=\"".$r['lat']."\" lng=\"".$r['lng']."\" status=\"".$r["status"].
					 "\" data=\"".processMarkerData($r["data"])."\"></marker>";
		}
		return $out;
	}
}

	// Está duplicada no jardins.php
	function processMarkerData($txt) {
		$out = $txt;
		$out = nl2br($txt);
		$out = strtr($out, "\n"," ");
		$out = htmlentities($out, ENT_QUOTES);
		return $out;
//		return htmlentities(nl2br($txt), ENT_QUOTES);
//		return utf8_encode(strtr(nl2br($txt), ENT_QUOTES), "\n"," ");
	}
	
	
	
	
	
	function getCoord() {
		$sectrs = preg_split("/[^\d]+/", $this->sectores);
		if (!count($sectrs)) return "";
		$q = "SELECT * FROM jardins WHERE client = '$client' AND ";
		foreach($sectrs as $s) {
			$q.= "id = '".$s."' OR ";
		}
		$q = substr($q, 0 , -3);
		include_once(u("DBconnect.php"));

		$res = mysql_query($q) or die(mysql_error());

		if(mysql_num_rows($res)!=0) {
		
			$max_lat = $max_lng = -9999;
			$min_lat = $min_lng =  9999;
			
			while ($r = mysql_fetch_assoc($res)) {
				echo "createMarkerAndPoint(map, ".$r['lat'].",".$r['lng'].", '".$r['id']."', '".$r['name']."', '".$r['status']."', '". processMarkerData($r['data'])."');\n";
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
				echo "center = new GLatLng('".str_replace(",", ".", $c_lat)."' , '".str_replace(",", ".", $c_lng)."');";
				?>
				map.setCenter(center);
				map.savePosition();
<?			}	
		}
?>
*/
}