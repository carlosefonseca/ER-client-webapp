<?
if (!hasPermission("users")) {
	die('<meta http-equiv="refresh" content="0;'.url("status").'">');
}
$title = "Alterar Utilizadores";

$page = url("admin/editGPS");
iLog($page);

$q="select users.`user`, email, gardens, permissions.permissions from users left join permissions on (users.`user`=permissions.`user`) where client='$client' and (gardens not like '' and permissions  not like '')";

$res=mysql_query($q) or die("getting users<br>".mysql_error());
$table = array();
$table[] = array('user'=>"Utilizador", 'email'=>'Email', 'gardens'=>'Jardins', 'permissions'=>'Permissões', 'alterar'=>'Alterar');
while($r = mysql_fetch_assoc($res)) {
	if ((strpos($r['permissions'], "admin") !== false) && !hasPermission("admin")) {
		$r["alterar"]="Não pode alterar administradores";
	} else {
	$r["alterar"]="<a href='javascript:openChangePermissions(\"".$r["user"]."\",\"".$r["gardens"]."\",\"".$r["permissions"]."\");'>Alterar Permissões</a>";
	}
	$table[] = $r;
}

?><div class="content"><h2>Utilizadores com acesso a este site</h2>

<p>Para um utilizador registado ter acesso ao site, é necessário que lhe seja dada permissão para tal.</p>

<?= array2table($table, true);?>

<p>&nbsp;</p>
<p><a href="javascript:openAddUser();">Dar permissão a outros utilizadores para aceder a este site</a></p>

</div> <!-- /body -->


<!-- EDIT PERMISSIONS POPUP #############################################################################-->
<div id="editperms" class="popup">
<table width="100%"><tr>
	<td width="50%">
		<p style="font-weight:bold">Acesso a Jardins</p>
		
		<p>Acesso:
			<input type="radio" name="gardenAcessType" id="gatall" value="*" onclick="accessTypeX('*')" onchange="accessTypeX('*')"/>
			<label for="gatall">Global</label>&nbsp;&nbsp;&nbsp;
			<input type="radio" name="gardenAcessType" id="gatlim" value="l" onclick="accessTypeX('l')" onchange="accessTypeX('l')"/>
			<label for="gatlim">Limitado</label>
		</p>
		
		<ul id="gardenAccessList" class="nobullets">

<? $q="select id,acronym,name from jardins where client like '$client' order by id";
$res = mysql_query($q) or die(mysql_error());
while($r = mysql_fetch_assoc($res)) {
	$j = $r['id'];
	$n = $r['name'];
?>	<li><label for="<?= $j;?>"><input type="checkbox" name="<?= $j;?>" id="<?= $j;?>"/> <?= $n;?></label></li>
<? } ?>

			<p>Seleccionar: 
			<a href="#" onclick="$('#gardenAccessList input[type=checkbox]').attr('checked',true)">Todos</a> | 
			<a href="#" onclick="$('#gardenAccessList input[type=checkbox]').attr('checked',false)">Nenhum</a></p>
		</ul>
	</td>
	<td width="50%">
		<p style="font-weight:bold">Permissões:</p>
		<ul id="userPerms" class="nobullets">
			<li><input type="checkbox" id="permProgs" value="programs"/> <label for="permProgs">Editar Programas</label></li>
			<li><input type="checkbox" id="permGards" value="gardens"/> <label for="permGards">Editar Jardins</label></li>
&nbsp;		<li><input type="checkbox" id="permUsers" value="users"/> <label for="permUsers">Alterar Permissões de Utilizadores</label></li>
<? if (hasPermission("admin")): ?>
&nbsp;		<li><input type="checkbox" id="permAdmin" value="admin" onchange="adminX(this.checked)" onclick="adminX(this.checked)" /> <label for="permAdmin">Administrar</label></li>
<? endif; ?>
		</ul>
	</td>
</tr></table>
</div> <!-- popup edit permissions -->

<!-- ADD USER POPUP ############################################################################# -->
<div id="addUser" class="popup">
<p>Contas de utilizadores que não têm permissões para aceder a este site</p>
<?	$q="
select * from
(	select u.user, group_concat(p.client SEPARATOR ', ') as clients
	from users as u
		left join
			(select * from permissions where (gardens <> '' or permissions <> '')) as p
			on (u.user = p.user)
	group by u.user
) as t
where clients not like '%$client%' and clients not like '%*%' or clients is null;";
	$res = mysql_query($q);
	
	$users = array();
	while($u = mysql_fetch_assoc($res)) {
		$u["adicionar"] = "<a href='javascript:addUser(\"".$u['user']."\");'>Adicionar utilizador</a>";
		$users[] = $u;
	}
	echo array2table($users);
?>
</div>

<script type="text/javascript">
var editUser;

function accessTypeX(newType) {
	if (newType == '*') {
		$('#gardenAccessList').slideUp();
	} else {
		$('#gardenAccessList').slideDown();
	}
}


function adminX(checked) {
	if (checked) {
		$("#userPerms input:not([id='permAdmin'])").attr("disabled",1).attr("checked",1);
	} else {
		$("#userPerms input:not([id='permAdmin'])").attr("disabled",0).attr("checked",0);
	}
}

function openChangePermissions(user, gardens, permissions) {
	editUser = user;
// Jardins
	if (gardens.indexOf("*") > -1) {
		$('#gatall').attr("checked",1);
		$('#gardenAccessList').hide();
	} else {
		$('#gardenAccessList').show();
		$('#gardenAccessList input[type=checkbox]').attr('checked',false);
		if (gardens != "") {
			var gardenArr = gardens.split(",");
			for (i in gardenArr) {
				$('#gardenAccessList input[type=checkbox]#'+gardenArr[i]).attr('checked',true);
			}
		}
		$('#gatlim').attr("checked",1);
		$('#gatlim').focus();
	}
	
// Permissoes
	$("#userPerms input").attr("checked",0);
	if (permissions.indexOf("admin") > -1) {
		$("#permAdmin").attr("checked",1);
		adminX(true);
	} else {
		adminX(false);
		permArr = permissions.split(",");
		for (i in permArr) {
			switch(permArr[i]) {
				case "programs": $("#permProgs").attr("checked",1); break;
				case "gardens":	 $("#permGards").attr("checked",1); break;
				case "users":	 $("#permUsers").attr("checked",1); break;
			}
		}
	}

	$("#editperms").dialog("option", "title", "Utilizador: "+user).dialog('open');
} //openChangePermissions


function save(element) {
	gardens = "";
	permissions = "";
	if ($('#gatall').attr("checked")) {
		gardens = "*";
	} else {
		$('#gardenAccessList input[type=checkbox]:checked').each(function(index, element) {
			gardens+=$(element).attr("name")+",";
		});
		gardens=gardens.substr(0,gardens.length-1);
	}

	$('#userPerms input[type=checkbox]:enabled:checked').each(function(index, element) {
		permissions+=$(element).attr("value")+",";
	});
	permissions=permissions.substr(0,permissions.length-1);
	
	
	var postData = "action=updateUser&user="+editUser+"&g="+gardens+"&p="+permissions;
//	console.log(postData);

	$.ajax({
		type: "POST",
		url: "actions.php",
		data: postData,
		success: function (txt) {
			if (txt != "OK") {
				alert("Ocorreu um erro:\n\n"+txt);
			} else {
				location.reload();
			}
		}
	})
}


function openAddUser() {
	$("#addUser").dialog("open");
}

function addUser(user) {
	$("#addUser").dialog("close");
	openChangePermissions(user, "", "");
}
	

// init
$(".popup").dialog({
	width: 650,
	position: ["center",40],
	bgiframe: true,
	autoOpen: false,
	modal: true,
	buttons: {
		'Cancelar': function() {
			$(this).dialog('close');
		},
		'Guardar': function() {
			save(this);
		}
	},
	close: function() {
	}
});

$("#addUser").dialog("option", "buttons", {	'Cancelar': function() { $(this).dialog('close'); } } )
			 .dialog("option", "title", "Adicionar Utilizador" );

</script>

