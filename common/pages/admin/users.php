<?
$page = url("admin/editGPS");
iLog($page);


$q="Select user, email, permissions from users";
$res=mysql_query($q);
$table = array();
while($r = mysql_fetch_assoc($res)) {
	$table[] = $r;
}

echo '<div class="content">';
echo array2table($table);
echo '</div>';