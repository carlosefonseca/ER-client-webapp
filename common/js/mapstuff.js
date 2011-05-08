var selectedMarker;
var map;
var center;
var markers = {};
var infobubbles = {};
var unknownGPS = {};
var jardins = {};

function initialize() {
    var latlng = new google.maps.LatLng(center_lat, center_lng);
    if (isNaN(zoom)) { zoom = 13; } 
    var myOptions = {
      zoom: zoom,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      minZoom: zoom
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	google.maps.event.trigger(map, 'resize');
}



// Pega numa lista de jardins (fornecida pelo jardins.php) e controi os marcadores no mapa
// e respectivos baloes de informação. Ajusta também o centro do mapa para o conjunto de marcadores.
// parametro info:
//		- bool true  > mostra o balão de informação
//		- bool false > não mostra balão de informação
//		- string	 > mostra balão com nome do jardim + string
function createMarkersFromJardins(map, jardins, info) {
	var min_lat =  9999;
	var min_lng =  9999;
	var max_lat = -9999;
	var max_lng = -9999;

	var size = 0;
	for (j in jardins) {
		size++;
	}

	for (j in jardins) {
//		jardins[j].name = Utf8.decode(jardins[j].name);
		var slaves = "";
		for (s in jardins[j].slaves) {
			slaves += "<span class='s"+jardins[j].slaves[s]+"'>"+s+"</span> ";
		}
		
		var sectores = "";
		for (i in jardins[j].sectores) {
			sectores += "<span class='s"+jardins[j].sectores[i]+"'>"+i+"</span> ";
		}
	
		if (info === true || info == undefined) {
			var more =  "<b>"+jardins[j].name+"</b>"+
				"<table class='mapbubble' border='0'>"+
				"<tr><th>Estado:</th><td>"+jardins[j].estado+"</td></tr>"+
				"<tr><th>Slaves:</th><td>"+slaves+"</td></tr>"+
				"<tr><th>Sectores:</th><td>"+sectores+"</td></tr>"+
//				"<tr><th>Progs Activos:</th><td>$programas</td></tr>".
//				"<tr><th>Tipo 1:</th><td>$mmT1 mm</td></tr>".
//				"<tr><th>Caudal 24h:</th><td>$c24h ($variacao)</td></tr>".
//				"<tr><th>Caudal Total:</th><td>$cTotal</td></tr>".
				"</table>";
		} else if (info === false) {
			more = false;
		} else {
			var more =  "<b>"+jardins[j].name+"</b>"+info
		}

		if (jardins[j].tmplat != undefined) {
			if (size == 1) {
				createMarker(map, jardins[j].tmplat, jardins[j].tmplng, j,
					jardins[j].name, jardins[j].status, "Posição Temporária!<br>Arraste para o local correcto");
					
				if (jardins[j].tmplat < min_lat) { min_lat = jardins[j].tmplat; }
				if (jardins[j].tmplat > max_lat) { max_lat = jardins[j].tmplat; }
				if (jardins[j].tmplng < min_lng) { min_lng = jardins[j].tmplng; }
				if (jardins[j].tmplng > max_lng) { max_lng = jardins[j].tmplng; }
			}
			unknownGPS[j] = jardins[j];
		} else {
			createMarker(map, jardins[j].lat, jardins[j].lng, j, jardins[j].name, jardins[j].status, more);

			if (jardins[j].lat < min_lat) { min_lat = jardins[j].lat; }
			if (jardins[j].lat > max_lat) { max_lat = jardins[j].lat; }
			if (jardins[j].lng < min_lng) { min_lng = jardins[j].lng; }
			if (jardins[j].lng > max_lng) { max_lng = jardins[j].lng; }
		}
	}
	var center = new google.maps.LatLng((min_lat*1+max_lat*1)/2 , (min_lng*1+max_lng*1)/2);
	map.setCenter(center);
}


function createMarker(map, lat, lng, id, title, status, info) {
//	console.log("A criar marker "+id+" ["+lat+","+lng+"] "+title);
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
		map: map,
		id: id
	});

	if (typeof(info)=='string') {
//		console.log("info: "+info);

		infobubbles[id] = new InfoBubble({
			map: map,
			maxWidth: 400,
			content: info,
			disableAutoPan: true,
			disableAnimation: true
		});

		google.maps.event.addDomListener(marker, 'mouseover', function() {
			infobubbles[id].open(map, marker);
		});
		
		google.maps.event.addDomListener(marker, "mouseout", function() {
			infobubbles[id].close();
		});
	}
	markers[id] = marker;
} 


// Removes the overlays from the map, but keeps them in the array
function clearOverlays() {
	if (markers) {
		for (i in markers) {
			markers[i].setMap(null);
			delete markers[i];
		}
	}
}

function loadJardim(id, info, callback) {
	var url = "jardins.php?id="+id;
	if (info == undefined) {
		info = true;
	}

	$.ajax({
		type: "GET",
		url: url,
		success: function (txt) {
			if (txt == "" || txt == "[]") {
				alert("Erro: Não foram carregados dados para o mapa!")
				return;
			}
//			txtutf8 = Utf8.decode(txt);
//			jardins = JSON.parse(txtutf8)
			jardins = JSON.parse(txt);
			for (i in jardins) {
				jardins[i].name = Utf8.decode(jardins[i].name);
			}

			createMarkersFromJardins(map, jardins, info);
			if (callback != undefined) { callback(); }
		}
	})
}