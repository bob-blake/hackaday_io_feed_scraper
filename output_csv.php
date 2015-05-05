<?
// output_csv.php
// Loads all hackaday.io feed items from my database and outputs them to a .csv file for download.
// http://www.bobblake.me/had_scraper/output_csv.php
// Author: Bob Blake
// Date: April 30, 2015

	require("../../php/had_scraper/vars.php");

	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=file.csv");
	header("Pragma: no-cache");
	header("Expires: 0");

	// This is deprecated, I know I know
	$server = mysql_connect(DB_HOST,DB_USER,DB_PASS);
	$dbcnx = @mysql_select_db(DB_NAME);

	$query = "SELECT id, class, title, date_time 
				FROM feed_items 
				ORDER BY date_time;";

	$mysql_array = mysql_query($query);
	$array[] = array("Item ID", "Feed Item Class", "Feed Item Title", "Date/Time");
	while($data_row = mysql_fetch_array($mysql_array, MYSQL_BOTH)){
		$array[] = array($data_row['id'],$data_row['class'],str_replace(array("\r", "\n", "\t"), '',strip_tags(htmlspecialchars_decode($data_row['title']))),$data_row['date_time']);	// Format nicely for now
	}

	outputCSV($array);

	function outputCSV($data) {
		$outstream = fopen("php://output", "w");
		function __outputCSV(&$vals, $key, $filehandler) {
			fputcsv($filehandler, $vals); // add parameters if you want
		}
		array_walk($data, "__outputCSV", $outstream);
		fclose($outstream);
	}
?>