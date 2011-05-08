<?
global $permissions;
global $logged_in;

function hasPermission($permission) {
	if ($permission[0] == 'j') {
		return hasGardenPermission($permission);
	}
	return (in_array($permission, $_SESSION["permissions"]) || in_array("admin", $_SESSION["permissions"]));
}

function hasGardenPermission($garden) {
	if ($garden[0] == 'j') {
		$garden = substr($garden, 1);
	}
	return (in_array('*', $_SESSION["permGardens"]) || in_array($garden, $_SESSION["permGardens"]));
}

/**
 * Checks whether or not the given username is in the
 * database, if so it checks if the given password is
 * the same password in the database for that user.
 * If the user doesn't exist or if the passwords don't
 * match up, it returns an error code (1 or 2). 
 * On success it returns 0.
 */

function confirmUser($username, $password){
	global $permissions;
	global $client;
	require_once(u("DBconnect.php"));
	/* Add slashes if necessary (for query) */
	if(!get_magic_quotes_gpc()) {
		$username = addslashes($username);
		$password = stripslashes($password);
	}

	/* Verify that user is in database */
	
	$q = "select users.user, email, gardens, permissions.permissions
		  from users left join permissions on (users.user=permissions.user)
		  WHERE (permissions.client = '$client' OR permissions.client = '*')
		  	and (users.user = '$username' OR users.email = '$username')
		  	and pass='$password';";

//	iLog($q);


	$result = mysql_query($q);
	if(!$result || (mysql_num_rows($result) != 1)){
		return false; //Indicates failure
	}

	$dbarray = mysql_fetch_array($result);
	if ($dbarray['gardens']=="" && $dbarray['permissions'] == "") {
		return "NO_PERMISSION";
	}
	
	$_SESSION["permissions"] = explode(",", $dbarray['permissions']);
	$_SESSION["permGardens"] = explode(",", $dbarray['gardens']);
	return $dbarray['user'];
/*	$dbarray['password']  = stripslashes($dbarray['password']);
	$password = stripslashes($password);

	/* Validate that password is correct *
	if(md5($password) == $dbarray['password']){
		return 0; //Success! Username and password confirmed
	}
	else{
		return 2; //Indicates password failure
	}*/
}

/**
 * checkLogin - Checks if the user has already previously
 * logged in, and a session with the user has already been
 * established. Also checks to see if user has been remembered.
 * If so, the database is queried to make sure of the user's 
 * authenticity. Returns true if the user has logged in.
 */
function checkLogin(){
	/* Check if user has been remembered */
	if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookpass'])){
		$_SESSION['username'] = $_COOKIE['cookname'];
		$_SESSION['password'] = $_COOKIE['cookpass'];
	}

	/* Username and password have been set */
	if(isset($_SESSION['username']) && isset($_SESSION['password'])){
//		iLog("User&Pass are set... confirming...");
		/* Confirm that username and password are valid */
		if(confirmUser($_SESSION['username'], $_SESSION['password'])){
			return true;
		} else {
			/* Variables are incorrect, user not logged in */
			unset($_SESSION['username']);
			unset($_SESSION['password']);
			return false;
		}
	}
	/* User not logged in */
	else{
		return false;
	}
}


function requireLogin() {
	if(!checkLogin()) header("Location: ".L(login,true));
}

/**
 * Determines whether or not to display the login
 * form or to show the user that he is logged in
 * based on if the session variables are set.
 */
function displayLogin(){
	global $logged_in;
	if($logged_in){
		echo "<h3>Logged In!</h3>";
		echo "Bem-vindo <b>$_SESSION[username]</b>. <a href=\"".L("logout",true)."\">Logout</a>";
	}
	else{
?>

<form action="<? L("login"); ?>" method="post">
<h3>Login :: Área de Cliente</h3>
<div id="username"><span>Utilizador ou email: </span><input type="text" name="user" maxlength="30" size="15"></div>
<div id="password"><span>Password: </span><input type="password" name="pass" maxlength="30" size="15"></div>
<? /*<tr><td colspan="2" align="left"><input type="checkbox" name="remember">
<font size="2">Remember me next time</td></tr>*/?>

<? if (isset($_GET['e'])): ?>
	<p class="login error">O nome de utilizador e password que introduziu não existem.</p>
<? elseif (isset($_GET['np']) && isset($_GET['u'])): ?>
	<p class="login error">O utilizador '<?= $_GET['u'];?>' não tem permissões de acesso a este site. Contacte o seu responsável.</p>
<? endif; ?>

<div id="submit"><input type="submit" name="sublogin" class="botao" value="Login"></div>
<p><a href="<? l("newaccount"); ?>">Criar uma conta</a></p>
</form>
<?
	}
}


/**
 * Checks to see if the user has submitted his
 * username and password through the login form,
 * if so, checks authenticity in database and
 * creates session.
 */
if(isset($_POST['sublogin'])){
	global $logged_in;
	/* Check that all fields were typed in */
	if(!$_POST['user'] || !$_POST['pass']){
		die('You didn\'t fill in a required field.');
	}
	/* Spruce up username, check length */
	$_POST['user'] = trim($_POST['user']);
	if(strlen($_POST['user']) > 30){
		die("Sorry, the username is longer than 30 characters, please shorten it.");
	}

	/* Checks that username is in database and password is correct */
	$md5pass = md5($_POST['pass']);
//	iLog("2-User&Pass are set... confirming...");
	$result = confirmUser($_POST['user'], $md5pass);
	/* Check error codes */
	if($result === false){
		header("Location: ".url("login&e"));	
//		echo 'That username/password doesn\'t exist in our database.';
		return ;
	}
	if ($result == "NO_PERMISSION") {
		header("Location: ".url("login&np&u=".$_POST['user']));
		return ;
	}

	/* Username and password correct, register session variables */
	$_POST['user'] = $result;//stripslashes($_POST['user']);
	$_SESSION['username'] = $result;//$_POST['user'];
	$_SESSION['password'] = $md5pass;
	$_SESSION['permissions'] = $permissions;

	/**
 	* This is the cool part: the user has requested that we remember that
 	* he's logged in, so we set two cookies. One to hold his username,
 	* and one to hold his md5 encrypted password. We set them both to
 	* expire in 100 days. Now, next time he comes to our site, we will
 	* log him in automatically.
 	*
	if(isset($_POST['remember'])){
		setcookie("cookname", $_SESSION['username'], time()+60*60*24*100, "/");
		setcookie("cookpass", $_SESSION['password'], time()+60*60*24*100, "/");
	}/

	/* Quick self-redirect to avoid resending data on refresh */
	//echo "<meta http-equiv=\"Refresh\" content=\"0;url=$HTTP_SERVER_VARS[PHP_SELF]\">";
	header("Location: $_SERVER[PHP_SELF]");
	return;
} else {
	if(isset($_GET['q']) && $_GET['q'] == 'logout') {
		/* Kill session variables */
		unset($_SESSION['username']);
		unset($_SESSION['password']);
		unset($_SESSION['permissions']);
		$_SESSION = array(); // reset session array
		session_destroy();	// destroy session.
	}
}

/* Sets the value of the logged_in variable, which can be used in your code */
$logged_in = checkLogin();
?>