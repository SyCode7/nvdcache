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

require 'db_functions.php';
require 'common_functions.php';

$start_time_epoch = time();
$this_programs_version = "0.3";
$this_programs_name = "updateCache";

// load config file	
if (file_exists("local_config.php")) {
	require 'local_config.php';
} else {
	require 'config.php';
}

$db_link = dbf_connectDB($config_database);

// grab the cache stats from the table.  This will tell us the last time the db was updated (if at all)
$cache_stats = dbf_cache_stats($db_link);

if($cache_stats[last_db_update_epoch] == 1000000) { // new install
	// get all data from nvd and load it up.
	full_db_load($ini_array, $db_link);
	dbf_update_stats($db_link);
	$cache_stats = dbf_cache_stats($db_link);
} elseif($cache_stats[hours_since_last_update] > $config_nvdcache[update_freq_hours]) { // she hasn't been updated in the time frame
	// go and get the modifed file from nvd and run it through the db
	$url = $config_cve[url_base].$config_cve[url_cve_modified];
	stream_load_xml($url, $db_link);
	dbf_update_stats($db_link);
} else {
	// nothing for now.
}

mysqli_close($db_link);

$run_time = time() - $start_time_epoch;

$msg .= ' Took '.$run_time.' seconds to complete';
$nvdCache_age_seconds = time() - $cache_stats[last_db_update_epoch];

$xml = c_initiate_xml($config_nvdcache);
$xml_msg = $xml->addchild('status');
$xml_msg->addchild('code', '200');
$xml_msg->addchild('description', $msg);
$xml_msg->addchild('cache_age_seconds', $nvdCache_age_seconds);
$xml_msg->addchild('oldest_update_item_epoch', $oldest_update_epoch);
c_announce($xml);


function full_db_load($ini_array, $db_link) {
	
	// 
	// below has been implemented very poorly.  It must be re-written to just figure the currrent
	// year and do a for each back to 2002.
	//
	$url = $config_cve[url_base].$config_cve[url_cve_year_pre]."2002".$config_cve[url_cve_year_post];
	stream_load_xml($url, $db_link);
	
	$url = $config_cve[url_base].$config_cve[url_cve_year_pre]."2003".$config_cve[url_cve_year_post];
	stream_load_xml($url, $db_link);
	
	$url = $config_cve[url_base].$config_cve[url_cve_year_pre]."2004".$config_cve[url_cve_year_post];
	stream_load_xml($url, $db_link);
	
	$url = $config_cve[url_base].$config_cve[url_cve_year_pre]."2005".$config_cve[url_cve_year_post];
	stream_load_xml($url, $db_link);
	
	$url = $config_cve[url_base].$config_cve[url_cve_year_pre]."2006".$config_cve[url_cve_year_post];
	stream_load_xml($url, $db_link);
	
	$url = $config_cve[url_base].$config_cve[url_cve_year_pre]."2007".$config_cve[url_cve_year_post];
	stream_load_xml($url, $db_link);
	
	$url = $config_cve[url_base].$config_cve[url_cve_year_pre]."2008".$config_cve[url_cve_year_post];
	stream_load_xml($url, $db_link);
	
	$url = $config_cve[url_base].$config_cve[url_cve_modified];
	stream_load_xml($url, $db_link);
}

function stream_load_xml($url, $db_link) {
	$start_delimiter = "<entry ";
	$end_delimiter = "</entry>";
	$byte_chunk_size = 64;
	
	$handle = fopen("$url", "r");
	if(!$handle) {
		$xml = c_initiate_xml($config_nvdcache);
		$xml_error = $xml->addchild('error');
		$xml_error->addchild('code', '500');
		$xml_error->addchild('description', 'Could not establish a read handle to '.$url);
		c_announce($xml);
	}

	$in_entry = 0;

	while ($data = fread($handle, $byte_chunk_size)) {
		$read_data .= $data; // append the streaming data unto the variable
		
		// use the start_delimiter to detect the begginging of an xml entry
		$read_data_exploded_start = explode($start_delimiter, $read_data);
		
		// if the array has more than one item then we hit upon the entry.
		if (count($read_data_exploded_start) > 1 && !$in_entry) {

			$in_entry = 1;

			// what was infront of the delimieter is poo
			$poo = array_shift($read_data_exploded_start);
			
			// put the start delimieter back unto the string becasue it was removed
			// with the delimeter 'explode' call.
			$read_data = $start_delimiter;
			
			// making sure that all left delimited items get added back
			// to the read string
			foreach ($read_data_exploded_start as $value) {
				$read_data .= $value;
			}
		}
		
		// look for the end delimiter
		$read_data_exploded_end = explode($end_delimiter, $read_data);
		
		// look for the end delimieter to indicate that we've gotten to the end of the
		// entry
		if (count($read_data_exploded_end) > 1) {
			$in_entry = 0;
			
			$poo = array_shift($read_data_exploded_end);
			
			// we finaly have a full entry as string
			$xml_entry_as_string = $poo.$end_delimiter;
			
			// put it into the db.
			//echo $xml_entry_as_string;
			dbf_put_entry_in_db($xml_entry_as_string, $db_link);
			
			//$xml = new SimpleXMLElement($xml_entry_as_string);
			//echo $xml->asXML();
			
			// reset the read_data variable
			
			$read_data = '';
			
			// set the read_data variable with the end of the exploded
			// data as to allow it to make the next front end delimiter
			// check
			foreach ($read_data_exploded_end as $value) {
				$read_data .= $value;
			} 	
		}
	}
}
?>