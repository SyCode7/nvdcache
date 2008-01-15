<?php
/*
nvdCache
Copyright (c) 2007 The Hursk Group, LLC. All rights reserved.

www.hursk.com

hurskgroup@hursk.com

This software is distributed WITHOUT ANY WARRANTY; without even
the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
PURPOSE.

*/

function dbf_connectDB($ini_array) {
	$db_link = mysqli_connect($ini_array[database][host], $ini_array[database][user], $ini_array[database][password], $ini_array[database][db_name], $ini_array[database][port], $ini_array[database][socket]);
	if(!$db_link) {
		$xml = c_initiate_xml($ini_array);
		$xml_error = $xml->addchild('error');
		$xml_error->addchild('code', '500');
		$xml_error->addchild('description', 'DB Error trying to connect.');
		c_announce($xml);
	}
	//mysqli_autocommit($db_link, FALSE); // turning off auto commit to be safer.

	return $db_link;
}

function dbf_cache_stats($db_link) {
	$query = "SELECT * FROM statistics";
	
	$result = mysqli_query($db_link, $query);
	
	if(!$result) { // no results
		return null;
	}
	
	$cache_stats =  mysqli_fetch_assoc($result);
	
	$cache_stats[seconds_since_last_update] = time() - $cache_stats[last_db_update_epoch];
	$cache_stats[minutes_since_last_update] = $cache_stats[seconds_since_last_update]/60;
	$cache_stats[hours_since_last_update] = $cache_stats[minutes_since_last_update]/60;
	
	return $cache_stats;
}

function dbf_put_entry_in_db($entry, $db_link) {
	// make sql safe the text
	$entry_mysql_safe = mysqli_real_escape_string($db_link, $entry);
	//echo $entry."\n";
	//echo $entry_mysql_safe."\n";
	
	$xml_entry = new SimpleXMLElement($entry);
	
	$query = "REPLACE INTO nvdEntry SET name = '$xml_entry[name]', type = '$xml_entry[type]', entry = '$entry_mysql_safe'";
	
	if(!$result = mysqli_query($db_link, $query)) {
		$xml = c_initiate_xml($ini_array);
		$xml_error = $xml->addchild('error');
		$xml_error->addchild('code', '500');
		$xml_error->addchild('description', 'DB Error: '.mysqli_error($db_link));
		c_announce($xml);
	}
}

function dbf_update_stats($db_link) {
	$query = "UPDATE statistics SET last_db_update_epoch = '".time()."' WHERE stat_id = '1'";
	
	if(!$result = mysqli_query($db_link, $query)) {
		$xml = c_initiate_xml($ini_array);
		$xml_error = $xml->addchild('error');
		$xml_error->addchild('code', '500');
		$xml_error->addchild('description', 'DB Error: '.mysqli_error($db_link));
		c_announce($xml);
	}
}

//
function dbf_getEntryData($db_link, $entryName, $entryType, $ini_array) {
	$query = "SELECT * FROM nvdEntry WHERE type = '$entryType' AND name = '$entryName'";
	if(!$result = mysqli_query($db_link, $query)) {
		$xml = c_initiate_xml($ini_array);
		$xml_error = $xml->addchild('error');
		$xml_error->addchild('code', '500');
		$xml_error->addchild('description', 'DB Error: '.mysqli_error($db_link));
		c_announce($xml);
	}

	if(mysqli_num_rows($result) == 0) {
		$xml = c_initiate_xml($ini_array);
		$xml_error = $xml->addchild('error');
		$xml_error->addchild('code', '400');
		$xml_error->addchild('description', $entryType.' entry '.$entryName.' was not found.');
		c_announce($xml);
	}
	
	// start building the xml data string
	$xml_parent_start = '<nvdCache version="'.$ini_array[nvdCache][version].'" cacheHost="'.$ini_array[nvdCache][cacheHost].'">';
	$xml_parent_end = '</nvdCache>';
	
	while ($row = mysqli_fetch_assoc($result)) {
		$xml_result = $xml_parent_start;
		$xml_result .= $row[entry];
		$xml_result .= $xml_parent_end;
	}
	
	$xml = new SimpleXMLElement($xml_result);
	
	return $xml;
	
}

?>