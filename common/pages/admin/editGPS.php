<? 
$page = url("admin/editGPS");
iLog($page);


global $canEditMarkers;

if ($canEditMarkers = hasPermission("edit_markers")): ?>
<div style="width:100%; height:100%">
<? include(u("pages/map.php")); ?>
<script type="text/javascript">
	if (!Object.keys) {
		Object.keys = function(obj) {
			var keys = new Array();
			for (k in obj) if (obj.hasOwnProperty(k)) keys.push(k);
			return keys;
		};
	}
	
	initialize();
	map.setZoom(map.zoom+4);
	map.setMapTypeId(google.maps.MapTypeId.SATELLITE);
<? if (isset($_GET['id'])): ?>
	loadJardim(<? echo $_GET['id']; ?>, false, function () {
		for (i in markers) {
			markers[i].setDraggable(true);
			google.maps.event.addDomListener(markers[i], "dragstart", function() {
				infobubbles[i].close();
			});
		
			google.maps.event.addDomListener(markers[i], "dragend", function() {
				updateLocation(markers[i])
			});
		}
	});
<? endif; ?>





function updateLocation(marker) {
	$.ajax({
		type: "POST",
		url: "actions.php",
		data: "action=updateMarker&id="+marker.id+"&lat="+marker.getPosition().lat()+"&lng="+marker.getPosition().lng(),
		success: function (msg) {
			if (msg != "OK") {
				alert("Erro ao guardar o marcador! \n \n"+msg);
			} else {
				fireAlert("Posição actualizada.");
			}
		}
	})
}

</script>

<? else: ?>
Não tem permissões para alterar as posições dos jardins.
<? endif; ?>
