<? iLog("<status>"); ?>
<div class="sidebar-left">
	<div id="act-desac-todos-jardins">
		<a id="act-todos-jardins">Activar todos os jardins</a>
		<a id="desact-todos-jardins">Desactivar todos os jardins</a>
	</div>

	<ul id="gardenList">
	<p>Lista de Jardins</p>
	</ul>
</div>

<div class="content with-left-sidebar" style="height:600px">

	<? include("map.php"); ?>
	<div id="marker_dialg" title="">
		<p>O que deseja fazer neste jardim?</p>
	</div>

</div>
<script>
	selectedMarker = 10;
	initialize();
	txt = '<? include("jardins.php"); ?>';
	if (txt == "" || txt == "[]") {
		alert("Erro: Não foram carregados dados para o mapa! Pode ter ocorrido um erro ou pode não ter permissões para ver os dados.")
	} else {
		//All markers
		info = JSON.parse(Utf8.decode(txt));
		jardins = info['jardins'];
		mapInfo = info['map'];
		createMarkersFromJardins(map, jardins);
		
		// Lista lateral
		for (i in jardins) {
			if ( unknownGPS[i] == undefined ) {
				$("#gardenList").append($("<li><a class='s"+jardins[i].status+"' href='javascript:openOptions("+i+");' gid='"+i+"'>"+i+". "+jardins[i].name+"</a></li>"));
			}
		}
		$("#gardenList li a").hover(
			function() {	// mouse enter
				infobubbles[$(this).attr("gid")].open(map, markers[$(this).attr("gid")]);
			},
			function() {	// mouse enter
				infobubbles[$(this).attr("gid")].close();
			});
		
		function openOptions(id) {
			selectedMarker = id;
			$("span.ui-dialog-title").html(markers[id].title);
			$("#marker_dialg").dialog("open");
		}
	}

$("#marker_dialg").dialog({
	bgiframe: true,
	autoOpen: false,
	modal: true,
	buttons: {
		'Ver/Editar Programas': function() {
			window.location = <? echo '"'.url('programs/"+selectedMarker'); ?>;
		},
		'Activar a programação do Jardim': function () {
			var answer = confirm("Activar a programação do jardim '"+markers[selectedMarker].title+"'?");
			if(answer) {
				$.ajax({
					type: "POST",
					url: "actions.php",
					data: "action=actJardim&id="+selectedMarker,
					dataType: "text",
					success: function(txt){
						if(txt != "OK") {
							alert("ERRO!\n\n"+txt);
							return;
						} else {
							reloadJardins();
						}
					}
				})
				$(this).dialog('close');
			}
		},
		'Desactivar a programação do Jardim': function () {
			//alert("A ser implementado brevemente.");
			var answer = confirm("Desactivar a programação do jardim '"+markers[selectedMarker].title+"'?");
			if(answer) {
				$.ajax({
					type: "POST",
					url: "actions.php",
					data: "action=desactJardim&id="+selectedMarker,
					dataType: "text",
					success: function(txt){
						if(txt != "OK") {
							alert("ERRO!\n\n"+txt);
							return;
						} else {
							reloadJardins();
						}
					}
				})
				$(this).dialog('close');
			}				
		},
		'Cancelar': function() {
			$(this).dialog('close');
		}
	},
	close: function() {
	}
});		


$("#act-todos-jardins").click(function () {
	var answer = confirm("Activar a programação todos os Jardins?");
	if(answer) {
		$.ajax({
			type: "POST",
			url: "actions.php",
			data: "action=actJardins",
			dataType: "text",
			success: function(txt){
				if(txt != "OK") {
					alert("ERRO!\n\n"+txt);
					return;
				} else {
					reloadJardins();
				}
			}
		})
	}
})
$("#desact-todos-jardins").click(function () {
	var answer = confirm("Desactivar a programação todos os jardins?");
	if(answer) {
		$.ajax({
			type: "POST",
			url: "actions.php",
			data: "action=desactJardins",
			dataType: "text",
			success: function(txt){
				if(txt != "OK") {
					alert("ERRO!\n\n"+txt);
					return;
				} else {
					reloadJardins();
				}
			}
		})
	}
})

</script>
<div>
<? iLog("</status>"); ?>