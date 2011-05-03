var selectedMarker;
var map;
var center;
var markers = {};

function initialize() {
    var latlng = new google.maps.LatLng(center_lat, center_lng);
    if (isNaN(zoom)) { zoom = 13; } 
    var myOptions = {
      zoom: zoom,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
//	map.setUIToDefault();
	google.maps.event.trigger(map, 'resize');
}


function createMarkersFromJardins(map, jardins) {
	console.log("A preparar markers");
	console.log(jardins);

	var min_lat = 9999;
	var min_lng = 9999;
	var max_lat = -9999;
	var max_lng = -9999;

	for (j in jardins) {
		console.log(jardins[j]);
		slaves = "";
		for (s in jardins[j].slaves) {
			slaves += "<span class='s"+jardins[j].slaves[s]+"'>"+(s+1)+"</span> ";
		}
		
		sectores = "";
		for (i in jardins[j].sectores) {
			sectores += "<span class='s"+jardins[j].sectores[i]+"'>"+i+"</span> ";
		}
	
		more =  "<h3>"+jardins[j].name+"</h3>"+
				"<table border='0'>"+
				"<tr><th>Estado:</th><td>"+jardins[j].estado+"</td></tr>"+
				"<tr><th>Slaves:</th><td>"+slaves+"</td></tr>"+
				"<tr><th>Sectores:</th><td>"+sectores+"</td></tr>"+
//				"<tr><th>Progs Activos:</th><td>$programas</td></tr>".
//				"<tr><th>Tipo 1:</th><td>$mmT1 mm</td></tr>".
//				"<tr><th>Caudal 24h:</th><td>$c24h ($variacao)</td></tr>".
//				"<tr><th>Caudal Total:</th><td>$cTotal</td></tr>".
				"</table>";

		createMarker(map, jardins[j].lat, jardins[j].lng, j, jardins[j].name, jardins[j].status, more);

		if (jardins[j].lat < min_lat) { min_lat = jardins[j].lat; }
		if (jardins[j].lat > max_lat) { max_lat = jardins[j].lat; }
		if (jardins[j].lng < min_lng) { min_lng = jardins[j].lng; }
		if (jardins[j].lng > max_lng) { max_lng = jardins[j].lng; }
	}
	var center = new google.maps.LatLng((min_lat*1+max_lat*1)/2 , (min_lng*1+max_lng*1)/2);
	map.setCenter(center);
}


function createMarker(map, lat, lng, id, title, status, info) {
	console.log("A criar marker "+id+" ["+lat+","+lng+"] "+title);
	markerIcon = "";
	if (status == "0") {
		markerIcon = "../common/css/markers/green.png";
	} else if (status == "off") {
		markerIcon = "../common/css/markers/grey.png";
	} else {
		markerIcon = "../common/css/markers/red.png";
	}

	var marker = new google.maps.Marker({
		position: new google.maps.LatLng(lat,lng),
		title: title,
		icon: markerIcon,
		map: map
	});

	if (typeof(info)=='string') {
		console.log("info: "+info);
		var infowindow = new google.maps.InfoWindow({
			content: info
		});
	
		google.maps.event.addListener(marker, 'mouseover', function() {
			infowindow.open(map,marker);
		});
		
		google.maps.event.addDomListener(marker, "mouseout", function() {
			infowindow.close();
		});
	}
	markers[id] = marker;
} 




/*
function createMarkerAndPoint(map, lat, lng, id, nome, status, data) {
	if (lat == 0 || lng == 0) {
		point = new google.maps.LatLng(center_lat,center_lng); 
	} else {
		point = new google.maps.LatLng(lat,lng); 
	}
	createMarker(map, point, id, nome, status, data, true);
}*/

// Creates a marker. If point === false, middle of map is used
//function createMarker(map, point, id, nome, status, data) { createMarker(map, point, id, nome, status, data, true); }

/*function createMarker(map, point, id, nome, status, data, info) {
	// Set up our google.maps.MarkerOptions object
	markerOptions = { /*<? if ($canEditMarkers) echo "draggable: true"; ?>* / };
	if (point === false) { point = map.getCenter(); }
	var marker = new google.maps.Marker(point, markerOptions);
	marker.id = id;
	marker.status = status;
	marker.name = nome;

	if (info) {
		google.maps.event.addDomListener(marker, "click", function() {
	//		marker.openInfoWindowHtml("<h3>#"+id+" - "+nome+"</h3>"+data);
			selectedMarker = id;
			$("span.ui-dialog-title").html(nome);
			$('#marker_dialg').dialog('open');
		});
		
		google.maps.event.addDomListener(marker, "mouseover", function() {
			switch(status) {
				case "0": estado = "OK"; break;
				case "off": estado = "Desactivado"; break;
				default: estado = status+" erros!"; break;
			}
			marker.openInfoWindowHtml("<h3>#"+id+" - "+nome+"</h3>"+data);
		});
	
		google.maps.event.addDomListener(marker, "mouseout", function() {
			map.closeInfoWindow();
		});
	
		google.maps.event.addDomListener(marker, "dragstart", function() {
			map.closeInfoWindow();
		});
	} // info
	
	google.maps.event.addDomListener(marker, "dragend", function() { 
		updateLocation(this);
	});

	markers[id] = marker;
	return marker;
}*/

function updateLocation(marker) {
	$.ajax({
		type: "POST",
		url: "../common/actions.php",
		data: "action=updateMarker&id="+marker.id+"&lat="+marker.getLatLng().lat()+"&lng="+marker.getLatLng().lng(),
		success: function (msg) {
			if (msg != "OK")
				alert("Erro ao guardar o marcador!\n\n"+msg);
		}
	})
}

function displayMarker(map, marker) {
/*	if ((marker.constructor.toString().indexOf("Object") == -1) && (marker.constructor.toString().indexOf("Array") == -1)) { //Se marker Ž apenas um pobre coitado
		map.addOverlay(marker);
		marker.disableDragging();
		if (marker.status == "0") {
			marker.setImage("../common/css/markers/green.png");
		} else if (marker.status == "off") {
			marker.setImage("../common/css/markers/grey.png");
		} else {
			marker.setImage("../common/css/markers/red.png");
		}
	} else { //Se marker Ž um array/object
		var min_lat = 9999;
		var min_lng = 9999;
		var max_lat = -9999;
		var max_lng = -9999;
		for(i in marker) {
			marker[i].setMap(map); 
			
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
			center = new google.maps.LatLng(c_lat,c_lng);
			map.panTo(center);
		} else {
			map.returnToSavedPosition();
		}
	}*/
}


function reloadJardins() {
	$.ajax({
		type: "GET",
		url: "jardins.php",
		success: function (txt) {
			iLog("Ajaxing jardins");
			clearOverlays();
    		eval(txt);
    		displayMarker(map, markers);
		}
	})
}

// Removes the overlays from the map, but keeps them in the array
function clearOverlays() {
  if (markers) {
    for (i in markers) {
      markers[i].setMap(null);
    }
  }
}

function loadJardim(id) {
//	markers = new Object();
	$.ajax({
		type: "GET",
		url: "jardins.php?id="+id,
		success: function (txt) {
			jardim = JSON.parse(txt)
			createMarkersFromJardins(map, jardim);
		}
	})
}