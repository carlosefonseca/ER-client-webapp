<script>
	$(function() {
		$( "#tabs" ).tabs();
	});
</script>



<div id="tabs">
	<ul>
		<li><a href="#graficos">Gráficos</a></li>
		<li><a href="#texto">Texto</a></li>
	</ul>
	<div id="graficos">
		<img src="http://www.meteopt.com/modelos/meteogramas/gfsgraphic.php?cidade=OEIRAS"/>
	</div>
	<div id="texto">
		<iframe id="frame" src="http://www.meteopt.com/modelos/meteogramas/gfstxt.php?cidade=OEIRAS" width="100%" height="2500"></iframe>
	</div>

</div>
