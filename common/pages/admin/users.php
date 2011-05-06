<?
$page = url("admin/editGPS");
iLog($page);

$q="select users.`user`, email, gardens, permissions.permissions from users left join permissions on (users.`user`=permissions.`user`) where client='$client' ";
//$q="Select user, email, permissions from users";
$res=mysql_query($q);
$table = array();
while($r = mysql_fetch_assoc($res)) {
	$r["alterar"]="<a href='javascript:alterarPermissoes(\"".$r["user"]."\");'>Alterar Permissões</a>";
	$table[] = $r;
	
}

?><div class="content"><h2><?= $client ?></h2><p>Utilizadores com acesso a este site.</p>

<?= array2table($table);?>

<p>&nbsp;</p>
<p><a href="javascript:adicionarUsers();">Dar permissão a outros users para aceder a este site</a></p>

</div>

<div id="popup">cenas</div>

<script type="text/javascript">

function alterarPermissoes(user) {
	$("#popup").dialog('open');
}

$("#popup").dialog({
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

	
</script>

