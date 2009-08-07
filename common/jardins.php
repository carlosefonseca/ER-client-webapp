<? # OUTPUTS JAVASCRIPT CODE FOR SETTING GMAP2 MARKERS AND CENTER
require("../common/DBconnect.php");
require_once("../common/funcoes.php");
session_start();
require_once("../common/user.php");
checkLogin();
global $client;

$q = "SELECT * FROM jardins WHERE client LIKE '$client'".getUserGardens(true);
$res = mysql_query($q) or die(mysql_error());

if(mysql_num_rows($res)!=0):
	
	$max_lat = $max_lng = -9999;
	$min_lat = $min_lng =  9999;
	
	while ($r = mysql_fetch_assoc($res)) {
		echo "createMarkerAndPoint(map, ".$r['lat'].",".$r['lng'].", '".$r['id']."', '".utf8_encode($r['name'])."', '".$r['status']."', '". processMarkerData($r['data'])."');\n";
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