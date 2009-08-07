<div class="content">
<?php

function err($msg) {
	die('<p>'.$msg.'</p><p><a href="javascript:history.go(-1);">Voltar</a></p>');
}

if (isset($_POST['submit'])) { // if form has been submitted
    /* check they filled in what they supposed to, 
    passwords matched, username
    isn't already taken, etc. */

    if (!$_POST['uname'] || !$_POST['passwd'] ||
        !$_POST['passwd_again']) {
//		err("You did not fill in a required field.");
		err("Não foram preenchidos todos os campos necessários.");
    }

	//filter SOME evil stuff
    if (!get_magic_quotes_gpc()) {
        $_POST['uname'] = addslashes($_POST['uname']);
        $_POST['passwd'] = addslashes($_POST['passwd']);
        $_POST['email'] = addslashes($_POST['email']);
    }
   
    // no HTML tags in username, website, location, password
    $_POST['uname'] = strip_tags($_POST['uname']);
    $_POST['passwd'] = strip_tags($_POST['passwd']);



    // check if username exists in database.

	require_once(u("DBconnect.php"));
    $qry = "SELECT user FROM users WHERE user = '".$_POST['uname']."'";

	$name_check = mysql_query($qry) or die(mysql_error());

    $name_checkk = mysql_num_rows($name_check);

    if ($name_checkk != 0) {
		err("O nome de utilizador <strong>'".$_POST['uname']."'</strong> já esta registado.<br />Por favor escolha outro.");
//        die('Sorry, the username: <strong>'.$_POST['uname'].'</strong>'. ' is already taken, please pick another one.');
    }

    // check passwords match

    if ($_POST['passwd'] != $_POST['passwd_again']) {
    	err("As passwords não são iguais.");
//        reloadForm('Passwords did not match.', "pass");
    }

    // check e-mail format

    if (!preg_match("/.*@.*..*/", $_POST['email']) ||
         preg_match("/(<|>)/", $_POST['email'])) {
		err("O endereço de email é inválido");
        //reloadForm('Invalid e-mail address.', 'email');
    }

    // now we can add them to the database.
    // encrypt password

    $_POST['passwd'] = md5($_POST['passwd']);


    $insert = "INSERT INTO users (
            user, 
            pass, 
            email) 
            VALUES (
            '".$_POST['uname']."', 
            '".$_POST['passwd']."',
            '".$_POST['email']."'
            )";

    $add_member = mysql_query($insert) or die(mysql_error());
?>

<h1>Registo efectuado!</h1>

<p>Obrigado. Pode efectuar o login em <a href="?q=login" title="Login">log in</a>.</p>

<?php

} else {    // if form hasn't been submitted

?>
<h1>Registar nova conta:</h1>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?q=signup&submit" method="post">
<table align="center" border="0" cellspacing="0" cellpadding="3">
<tr><td>Nome de Utilizador:</td><td>
<input type="text" name="uname" maxlength="40">
</td></tr>
<tr><td>Palavra-passe:</td><td>
<input type="password" name="passwd" maxlength="50">
</td></tr>
<tr><td>Confirmar a palavra-passe:</td><td>
<input type="password" name="passwd_again" maxlength="50">
</td></tr>
<tr><td>E-Mail:</td><td>
<input type="text" name="email" maxlength="100">
</td></tr>
<tr><td>Telefone:</td><td>
<input type="text" name="website" maxlength="150">
</td></tr>
<tr><td colspan="2" align="right">
<input type="submit" name="submit" value="Registar">
</td></tr>
</table>
</form>

<?php

}

?>
</div>