<? 
$page = url("admin/editGPS");
iLog($page);


global $canEditMarkers;

if ($canEditMarkers = hasPermission("edit_markers")): ?>
	<div style="width:100%; height:100%">
	<? include(u("pages/map.php")); ?>
	<script type="text/javascript">
		initialize();
		<? if (isset($_GET['id'])) { echo "loadJardim(".$_GET['id'].");"; } ?>
	</script>
	</div>
<? else: ?>
N‹o tem permiss›es para alterar as posi›es dos jardins.
<? endif; ?>
