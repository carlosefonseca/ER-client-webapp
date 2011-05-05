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
<? if (isset($_GET['id'])): ?>
	loadJardim(<? echo $_GET['id']; ?>, false, function () {
		for (i in markers) {
			markers[i].setDraggable(true);
		}
	});
<? endif; ?>
</script>
</div>

<? else: ?>
Não tem permissões para alterar as posições dos jardins.
<? endif; ?>
