<?
global $D;
$D = false;

function processChanges() {
	$passSQL = "";
	if(strlen($_POST['pass1'])>3) {
		if(strlen($_POST['pass2'])>3) {
			if ($_POST['pass1'] == $_POST['pass2']) {
				//filter SOME evil stuff
				$pass = addslashes($_POST['pass1']);
				$pass = strip_tags($pass);
				// encrypt password
				$pass = md5($pass);
				// add SQL wrapper
				$passSQL = ", pass='$pass'";
			} else {
				return "Passwords diferem";
			}
		} else {
			return "Tem que introduzir a password nos dois campos.";
		}
	}
	//filter SOME evil stuff
	$email = addslashes($_POST['email']);

	// check e-mail format
	if (!preg_match("/.*@.*..*/", $_POST['email']) ||
		preg_match("/(<|>)/", $_POST['email'])) {
		if ($D) echo "WRONG EMAIL";
		return('Endereço de e-mail inválido.');
	}

	// now we can update the database.
	$q = "UPDATE users SET email = '$email' $passSQL WHERE user like '".$_SESSION["username"]."'";
	if ($D) echo "<p>$q</p>";
	$res = mysql_query($q);
	if (mysql_errno()) {
		return mysql_error();
	}
	// agora que tudo esta' bem, redefinir a var de sessao para que não seja terminada a sessao do user
	if ($passSQL != "") {
		$_SESSION['password'] = $pass;
	}
	// "" é o código de sucesso neste caso
	return "";
}





if(isset($_POST["changeUserAccount"])) {
	$r = processChanges();
	if ($D) echo $r;
	if ($r != "") { // "" é quando corre bem
		if ($D) echo "ERROR";
		$error = $r;
		$success = false;
	} else {
		if ($D) echo "ALL GOOD";
		$success = true;
	}
}

?>
<div class="content user-account">
	<h1>Conta de Utilizador</h1>

<? if ($success): ?>
	<p>Alterações efectuadas.</p>
	<p>Vai ser redireccionado para a página inicial.</p>
	<meta http-equiv="refresh" content="4;status">
<? else:
	//require_once("../common/DBconnect.php");
	$q = "SELECT * FROM users WHERE user like '".$_SESSION['username']."' LIMIT 1;";
	$res = mysql_query($q) or die(mysql_error());
	$user = mysql_fetch_array($res);
?>
	<p>Pode alterar aqui os dados associados à conta.<br/>&nbsp;</p>

	<form action="user" method="post">
		<p><label>Nome de Utilizador:</label> <? echo $user['user'];?></p>
		<p><label for="email">E-mail:</label> <input type="text" name="email" id="email" value="<? echo $user['email'];?>" size="30" /></p>
		<? if (isset($error)) echo "<p class='error'>$error</p>"; ?>
		<fieldset id="password">
			<p>Alterar apenas se quiser alterar a password.</p>
			<p><label for="pass1">Nova Password:</label> <input type="password" name="pass1" id="pass1"/></p>
			<p><label for="pass2">Alterar Password:</label> <input type="password" name="pass2" id="pass2"/></p>
		</fieldset>
		<p align="center"><input type="submit" name="changeUserAccount" value="Guardar Alterações"/></p>
	</form>
<? endif; ?>
</div>