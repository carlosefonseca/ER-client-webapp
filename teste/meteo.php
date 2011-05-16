<? 
$url = "http://www.meteooeiras.com/graficos-historicos";
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $url); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
$meteo = curl_exec($ch); 
curl_close($ch); 

echo $meteo;
?>
<style>
#main_bg, #content_bg {
	margin: 0;
	width: inherit;
}

#header, #topmenu, #navigation, #footer {
	display: none;
}

#content {
	background-color: white;
	width: 100%;
	padding: 0;
}

.jwts_tabbertab > * {
	text-align: center;
}

.jwts_tabbernav {
	width: 320px;
	margin: auto;
}
</style>
<div style="position: absolute; left: 240px; padding: 5px 10px; background: #EEE; top: 18px;">Dados de <a href="http://www.meteooeiras.com">MeteoOeiras.com</a>
</div>