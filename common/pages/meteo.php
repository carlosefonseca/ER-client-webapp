<? 
global $params;
if ($params=="") { $params = "rega"; }
?>
<div class="sidebar-left">
<ul id="meteo-menu">
<li <?= $params=="rega"?"class='active'":''; ?>><a href="<?= l("meteo/rega"); ?>">Informações de Rega</a></li>
<li <?= $params=="actual"?"class='active'":''; ?>><a href="<?= l("meteo/actual"); ?>">Meteorologia Actual</a></li>
<li <?= $params=="previsao"?"class='active'":''; ?>><a href="<?= l("meteo/previsao"); ?>">Previsão Meteorológica</a></li>
</ul>
</div>

<?
$file = "";
switch($params) {
	case "actual":	$file = "meteo-actual.php"; break;
	case "previsao":$file = "meteo-previsao.php"; break;
	case "rega":
	default:		$file = u("pages/meteo-rega.php");
}

?>
<div class="content with-left-sidebar">
<?
if ($params == "actual"): ?>
<iframe id="frame" src="meteo-actual.php" width="100%" frameborder="0" marginheight="0" marginwidth="0"></iframe>
<script type="text/javascript">
function resizeIframe() {
	document.getElementById("frame").height = document.getElementById("frame").contentDocument.height
};
document.getElementById('frame').onload = resizeIframe;
window.onresize = resizeIframe;
</script>

<? else:

if (file_exists($file)) {
	include($file);
}
endif;
?>
</div>
<?
return;
?>
<? if (false):// && file_exists("meteo.php")): ?>
<div class="content" style="width: 500px">
<iframe id="frame" src="meteo.php" width="100%" frameborder="0" marginheight="0" marginwidth="0"></iframe>
</div>
<script type="text/javascript">
function resizeIframe() {
	document.getElementById("frame").height = document.getElementById("frame").contentDocument.height
};
document.getElementById('frame').onload = resizeIframe;
window.onresize = resizeIframe;
</script>

<?	return;
endif;
global $title;
$title = "Meteorologia";
?>
<link rel="stylesheet" type="text/css" href="../css/meteo.css" />
<link rel="stylesheet" type="text/css" href="../common/css/meteo.css" />

<div class="content with-left-sidebar">

	<fieldset>
		<legend>Barómetro</legend>
		<ul>
			<li>888 <span class="unid">mbar</span></li>
		</ul>
	</fieldset>
	
	<fieldset>
		<legend>Temperatura</legend>
		<ul>
			<li>Interior: 888 <span class="unid">ºC</span></li>
			<li>Exterior: 888 <span class="unid">ºC</span></li>
		</ul>
	</fieldset>
	
	<fieldset>
		<legend>Ponto de Orvalho</legend>
		<ul>
			<li>888 <span class="unid">ºC</span></li>
		</ul>
	</fieldset>
	
	<fieldset>
		<legend>Humidade</legend>
		<ul>
			<li>Interior: 888 <span class="unid">%</span></li>
			<li>Exterior: 888 <span class="unid">%</span></li>
		</ul>
	</fieldset>
	
	<fieldset>
		<legend>Indice de Calor</legend>
		<ul>
			<li>888 <span class="unid"></span></li>
		</ul>
	</fieldset>
	
	<fieldset>
		<legend>Windchill</legend>
		<ul>
			<li>888 <span class="unid">ºC</span></li>
		</ul>
	</fieldset>
	
	<fieldset>
		<legend>Vento</legend>
		<ul>
			<li>Velocidade: 888 <span class="unid">km/h</span></li>
			<li>Direcção: 888 <span class="unid"></span></li>
			<li>Rajada: 888 <span class="unid">km/h</span></li>
		</ul>
	</fieldset>
	
	<fieldset>
		<legend>Chuva</legend>
		<ul>
			<li>Total: 888 <span class="unid">mm/h</span></li>
			<li>Racio: 888
<span class="unid">mm/h</span></li>
			<li>1h: 888 <span class="unid">mm/h</span></li>
			<li>24h: 888 <span class="unid">mm/h</span></li>
			<li>Mês: 888 <span class="unid">mm/h</span></li>
		</ul>
	</fieldset>

</div>