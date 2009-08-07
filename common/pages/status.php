<div class="sidebar-left meteo">
	<div><span>Temperatura Média:</span>20º C</div>
	<div><span>Humidade Média:</span>20 mm</div>
	<div><span>Caudal Médio:</span>224 m<sup>3</sup></div>
	<div><span>Caudal Total:</span>126789 m<sup>3</sup></div>
</div>

<div class="content with-left-sidebar" style="height:700px">
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
		height: 300,
		modal: true,
		buttons: {
		    'Aceder aos Programas': function() {
		    	window.location = "programs-"+selectedMarker;
		    },
		    'Parar os programas': function () {
				alert("A ser implementado brevemente.");
		    },
		    'Cancelar': function() {
		    	$(this).dialog('close');
		    }
		},
		close: function() {
		}
	});		
		
	</script>
