<?
global $c_lat;
global $c_lng;
global $zoom;
global $canEditMarkers;
?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=ABQIAAAAAXja6TyGWuD4IaWbzAXbvxSLjE9tukku2vkqMEHoc5mw6LJAEBRj_whLfeS6X4EmxvGFhH_BczregQ"></script>
<script type="text/javascript">

var selectedMarker;
var map;
var center;
var markers = new Object();

function initialize() {
  if (GBrowserIsCompatible()) {
    map = new GMap2(document.getElementById("map_canvas"));
	center = new GLatLng(<? echo $c_lat;?>,<? echo $c_lng;?>);
    map.setCenter(center, <? echo isset($zoom)?$zoom:"13";?>);
	map.setUIToDefault();
  }
}

function createMarkerAndPoint(map, lat, lng, id, nome, status, data) {
	if (lat == 0 || lng == 0) { 
		point = new GLatLng(<? echo $c_lat;?>,<? echo $c_lng;?>); 
	} else {
		point = new GLatLng(lat,lng); 
	}
	createMarker(map, point, id, nome, status, data);
}

// Creates a marker
function createMarker(map, point, id, nome, status, data) {
	// Set up our GMarkerOptions object
	markerOptions = { <? if ($canEditMarkers) echo "draggable: true"; ?> };
	var marker = new GMarker(point, markerOptions);
	marker.id = id;
	marker.status = status;
	marker.name = nome;
	GEvent.addListener(marker, "click", function() {
//		marker.openInfoWindowHtml("<h3>#"+id+" - "+nome+"</h3>"+data);
		selectedMarker = id;
		$("span.ui-dialog-title").html(nome);
		$('#marker_dialg').dialog('open');
	});
	
	GEvent.addListener(marker, "mouseover", function() {
		switch(status) {
			case "0": estado = "OK"; break;
			case "off": estado = "Desactivado"; break;
			default: estado = status+" erros!"; break;
		}
		marker.openInfoWindowHtml("<h3>#"+id+" - "+nome+"</h3>"+data);
	});

	GEvent.addListener(marker, "mouseout", function() {
		map.closeInfoWindow();
	});

	GEvent.addListener(marker, "dragstart", function() {
		map.closeInfoWindow();
	});

    GEvent.addListener(marker, "dragend", function() {
		$.ajax({
			type: "POST",
			url: "../common/actions.php",
			data: "action=updateMarker&id="+this.id+"&lat="+this.getLatLng().lat()+"&lng="+this.getLatLng().lng(),
			success: function (msg) {
				if (msg != "OK")
					alert("Erro ao guardar o marcador!\n\n"+msg);
			}
		})
    });
	markers[id] = marker;
//	displayMarker(map, marker);
	return marker;
}

function displayMarker(map, marker) {
	if ((marker.constructor.toString().indexOf("Object") == -1) && (marker.constructor.toString().indexOf("Array") == -1)) { //Se marker é apenas um pobre coitado
		map.addOverlay(marker);
		marker.disableDragging();
		if (marker.status == "0") {
			marker.setImage("../common/css/markers/green.png");
		} else if (marker.status == "off") {
			marker.setImage("../common/css/markers/grey.png");
		} else {
			marker.setImage("../common/css/markers/red.png");
		}
	} else { //Se marker é um array/object
		var min_lat = 9999;
		var min_lng = 9999;
		var max_lat = -9999;
		var max_lng = -9999;
		for(i in marker) {
			map.addOverlay(marker[i]);
			marker[i].disableDragging();
			if (marker[i].status == "0") {
				marker[i].setImage("../common/css/markers/green.png");
			} else if (marker[i].status == "off") {
				marker[i].setImage("../common/css/markers/grey.png");
			} else {
				marker[i].setImage("../common/css/markers/red.png");
			}
			var pos = marker[i].getLatLng();
    		if (pos.lat() < min_lat) { min_lat = pos.lat(); }
    		if (pos.lat() > max_lat) { max_lat = pos.lat(); }
    		if (pos.lng() < min_lng) { min_lng = pos.lng(); }
    		if (pos.lng() > max_lng) { max_lng = pos.lng(); }
		}
		var c_lat = (max_lat + min_lat)/2;
		var c_lng = (max_lng + min_lng)/2;
		if(c_lat && c_lng) {
			center = new GLatLng(c_lat,c_lng);
			map.panTo(center);
		} else {
			map.returnToSavedPosition();
		}
	}
}


function reloadJardins() {
	$.ajax({
		type: "GET",
		url: "jardins.php",
		success: function (txt) {
			map.clearOverlays();
    		eval(txt);
    		displayMarker(map, markers);
		}
	})
}
</script>

<div id="map_canvas" style="width: 100%; height: 100%"></div>
	
</div>

<script>
	initialize();
	$("body").unload(function () { GUnload(); });

</script>