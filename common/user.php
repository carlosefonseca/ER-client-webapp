<?
global $permissions;
global $logged_in;


function hasPermission($permission) {
	if (strpos($_SESSION["permissions"], $permission) === false) {
		return false;
	} else {
		return true;
	}
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
	
	$q = "select permissions from users where user = '$username' && pass='$password' && (client like '%$client%' OR client like '*')";
	$result = mysql_query($q);
	if(!$result || (mysql_numrows($result) < 1)){
		return 0; //Indicates username failure
	}

	/* Retrieve password from result, strip slashes */
	$dbarray = mysql_fetch_array($result);
	$_SESSION["permissions"] = $dbarray['permissions'];
	return 1;
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
<div id="username"><span>Utilizador: </span><input type="text" name="user" maxlength="30" size="15"></div>
<div id="password"><span>Password: </span><input type="password" name="pass" maxlength="30" size="15"></div>
<? /*<tr><td colspan="2" align="left"><input type="checkbox" name="remember">
<font size="2">Remember me next time</td></tr>*/?>
<?	if (isset($_GET['e'])) {echo "<p class='login error'>O nome de utilizador e password que introduziu não existem.</p>";} ?>
<div id="submit"><input type="submit" name="sublogin" value="Login"></div>
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
	$result = confirmUser($_POST['user'], $md5pass);
	/* Check error codes */
	if($result == 0){
//		header("Location: login&e");	
		echo 'That username/password doesn\'t exist in our database.';
		return ;
	}
	else if($result == 2){
		die('Incorrect password, please try again.');
	}

	/* Username and password correct, register session variables */
	$_POST['user'] = stripslashes($_POST['user']);
	$_SESSION['username'] = $_POST['user'];
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