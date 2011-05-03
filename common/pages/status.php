<<<<<<< HEAD
<? iLog("<status>"); ?>
=======
>>>>>>> 62f5d2252b428e533ffafa773842968fa47e686a
<div class="sidebar-left">
	<div id="act-desac-todos-jardins">
		<a id="act-todos-jardins">Activar todos os jardins</a>
		<a id="desact-todos-jardins">Desactivar todos os jardins</a>
	</div>

	<ul class="meteo">
<<<<<<< HEAD
    	<li>LISTA DE JARDINS</li>
=======
    	<li><span>Temperatura Média:</span>20º C</li>
    	<li><span>Humidade Média:</span>20 mm</li>
    	<li><span>Caudal Médio:</span>224 m<sup>3</sup></li>
    	<li><span>Caudal Total:</span>126789 m<sup>3</sup></li>
>>>>>>> 62f5d2252b428e533ffafa773842968fa47e686a
    </ul>
</div>

<div class="content with-left-sidebar" style="height:600px">
<? 
global $canEditMarkers;

if ($canEditMarkers = hasPermission("edit_markers")): ?>
	<div id="edit_markers"><input type="button" value="Alterar Posições" class="button" /></div>
<? endif; ?>
	<? include("map.php"); ?>
	<div id="marker_dialg" title="">
		<p>O que deseja fazer neste jardim?</p>
	</div>

</div>
	<script>
		//All markers
		<? include("jardins.php"); ?>
		displayMarker(map, markers);
		
<?	if ($canEditMarkers): ?>
		$("#edit_markers input").click(function () {
			if (markers[1].draggingEnabled()) {
				// Desactivar
				for(i in markers) {
					markers[i].disableDragging();
				}
				$("#edit_markers input").attr("value","Alterar Posições");
			} else { // activar
				for(i in markers) {
					markers[i].enableDragging();
				}
				$("#edit_markers input").attr("value","Concluir");
			}
		})
<?	endif; ?>
	$("#marker_dialg").dialog({
		bgiframe: true,
		autoOpen: false,
//		width: 800,
//		height: 400,
		modal: true,
		buttons: {
		    'Ver/Editar Programas': function() {
		    	window.location = "programs-"+selectedMarker;
		    },
		    'Activar a programação do Jardim': function () {
				var answer = confirm("Activar a programação do jardim '"+markers[selectedMarker].name+"'?");
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
				var answer = confirm("Desactivar a programação do jardim '"+markers[selectedMarker].name+"'?");
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
<<<<<<< HEAD
<div>
<? iLog("</status>"); ?>
=======
<div>
>>>>>>> 62f5d2252b428e533ffafa773842968fa47e686a
