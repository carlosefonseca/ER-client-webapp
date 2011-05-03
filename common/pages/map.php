<?
global $c_lat;
global $c_lng;
global $zoom;
global $canEditMarkers;

iLog("<Map>");
require_once("../common/user.php");
require_once("../common/funcoes.php");
?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&language=pt"></script>
<script type="text/javascript" src="../common/js/mapstuff.js"></script>

<div id="map_canvas" style="width: 100%; height: 100%"></div>
	
</div>

<script>
var center_lat = <? echo getMapCenter("lat"); ?>;
var center_lng = <? echo getMapCenter("lng"); ?>;
var zoom = <? echo $zoom; ?>;

$("body").unload(function () { GUnload(); });
</script>
<? iLog("</Map>"); ?>