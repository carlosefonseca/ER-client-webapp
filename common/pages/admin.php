<?
if(isset($_GET["adminAction"])) {

	if ($_GET["adminAction"] == "jardinsFile2DB") {
		print_r(parseMaster($root."MastersList.txt"));
	}

}