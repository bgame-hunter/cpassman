<?php
/**
 * @file 		kb.queries.categories.php
 * @author		Nils Laumaill�
 * @version 	2.0
 * @copyright 	(c) 2009-2011 Nils Laumaill�
 * @licensing 	CC BY-ND (http://creativecommons.org/licenses/by-nd/3.0/legalcode)
 * @link		http://www.teampass.net
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

session_start();
if (!isset($_SESSION['CPM'] ) || $_SESSION['CPM'] != 1)
	die('Hacking attempt...');

global $k, $settings;
include('../includes/settings.php');
header("Content-type: text/x-json; charset=".$k['charset']);

require_once("class.database.php");
$db = new Database($server, $user, $pass, $database, $pre);
$db->connect();

$sql = "SELECT id, category FROM ".$pre."kb_categories";

//manage filtering
if (!empty($_GET['term'])) {
	$sql .= " WHERE category LIKE '%".$_GET['term']."%'";
}

$sql .= " ORDER BY category ASC";

$sOutput = '';

$rows = $db->fetch_all_array($sql);
if ($rows[0]>0) {
	foreach($rows as $reccord){
		if (empty($sOutput)) {
			$sOutput = '"'.$reccord['category'].'"';
		}else{
			$sOutput .= ', "'.$reccord['category'].'"';
		}
	}

	//Finish the line
	echo '[ '.$sOutput.' ]';
}



?>