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
$this_programs_version = "0.1"; // Original release
$this_programs_name = "updateDataSource";
$msg = "";
global $oldest_update_epoch;
$oldest_update_epoch = 100000000000;

// load ini file into an array	
if (file_exists("local_config.ini")) {
	$ini_array = parse_ini_file("local_config.ini", true);
} else {
	$ini_array = parse_ini_file("config.ini", true); // load the main one
	if(!$ini_array) {
		$xml = c_initiate_xml($ini_array);
		$xml_error = $xml->addchild('error');
		$xml_error->addchild('code', '500');
		$xml_error->addchild('description', 'Configuration files were not found.  Please see http://code.google.com/p/nvdcache/ for information.');
		c_announce($xml);
	}
}

$db_link = dbf_connectDB($ini_array);

// grab the cache stats from the table.  This will tell us the last time the db was updated (if at all)
$cache_stats = dbf_cache_stats($db_link);

if(!$cache_stats) { // new install or empty statistics table
	$msg .= "The statistics table is empty.  Maybe a new install.  A full load will be initiated.";
	full_db_load($ini_array);
	dbf_update_stats($db_link);
	$cache_stats = dbf_cache_stats($db_link);
	dbf_new_database($db_link);
}

$seconds_since_last_update = time() - $cache_stats[last_db_update_epoch];
$minutes_since_last_update = $seconds_since_last_update/60;
$hours_since_last_update = $minutes_since_last_update/60;

if($hours_since_last_update > $ini_array[nvdCache][update_freq_hours]) { // she hasn't been updated in the time frame
	// go and get the modifed file from nvd and run it through the db
	$url = $ini_array[cve][url_base].$ini_array[cve][url_cve_modified];
	$oldest_update_epoch = stream_load_xml($url);
	dbf_update_stats($db_link);
	$msg .= 'Due for a modified update.';
} else {
	$cache_stats[last_db_update_epoch];
	$msg .= 'Nothing to do.';
}

//echo $oldest_update_epoch;

mysqli_close($db_link);

$run_time = time() - $start_time_epoch;

$msg .= ' Took '.$run_time.' seconds to complete';
$nvdCache_age_seconds = time() - $cache_stats[last_db_update_epoch];

$xml = c_initiate_xml($ini_array);
$xml_msg = $xml->addchild('status');
$xml_msg->addchild('code', '200');
$xml_msg->addchild('description', $msg);
$xml_msg->addchild('cache_age_seconds', $nvdCache_age_seconds);
$xml_msg->addchild('oldest_update_item_epoch', $oldest_update_epoch);
c_announce($xml);

//
//
// Function land!
//
//

function full_db_load($ini_array) {
	$url = $ini_array[cve][url_base].$ini_array[cve][url_cve_year_pre]."2002".$ini_array[cve][url_cve_year_post];
	$oldest_update_epoch = stream_load_xml($url);
	
	$url = $ini_array[cve][url_base].$ini_array[cve][url_cve_year_pre]."2003".$ini_array[cve][url_cve_year_post];
	$oldest_update_epoch = stream_load_xml($url);
	
	$url = $ini_array[cve][url_base].$ini_array[cve][url_cve_year_pre]."2004".$ini_array[cve][url_cve_year_post];
	$oldest_update_epoch = stream_load_xml($url);
	
	$url = $ini_array[cve][url_base].$ini_array[cve][url_cve_year_pre]."2005".$ini_array[cve][url_cve_year_post];
	$oldest_update_epoch = stream_load_xml($url);
	
	$url = $ini_array[cve][url_base].$ini_array[cve][url_cve_year_pre]."2006".$ini_array[cve][url_cve_year_post];
	$oldest_update_epoch = stream_load_xml($url);
	
	$url = $ini_array[cve][url_base].$ini_array[cve][url_cve_year_pre]."2007".$ini_array[cve][url_cve_year_post];
	$oldest_update_epoch = stream_load_xml($url);
	
	$url = $ini_array[cve][url_base].$ini_array[cve][url_cve_modified];
	$oldest_update_epoch = stream_load_xml($url);
}



function stream_load_xml($url) {
	global $oldest_update_epoch;
	$oldest_update_epoch = 100000000000;
	
	$handle = fopen("$url", "r");
	if(!$handle) {
		$xml = c_initiate_xml($ini_array);
		$xml_error = $xml->addchild('error');
		$xml_error->addchild('code', '500');
		$xml_error->addchild('description', 'Could not establish a read handle to '.$url);
		c_announce($xml);
	}
	
	$xml_parser = xml_parser_create();
	xml_set_element_handler($xml_parser, "xml_start_element", "xml_end_element");
	xml_set_character_data_handler($xml_parser, "xml_character_data");
	while ($data = fread($handle, 4096)) {
		if(!xml_parse($xml_parser, $data, feof($handle))) {    
			$xml = c_initiate_xml($ini_array);
			$xml_error = $xml->addchild('error');
			$xml_error->addchild('code', '500');
			$xml_error->addchild('description', 'XML Parsing error: '.xml_error_string(xml_get_error_code($xml_parser)).' Line '.xml_get_current_line_number($xml_parser));
			c_announce($xml);
		}
	}
	xml_parser_free($xml_parser);
	
	return $oldest_update_epoch; //success!
}

function xml_start_element($parser, $name, $attrs) {
	global $cve, $ref_count, $prod_count, $oldest_update_epoch;
	if($name == "ENTRY") { // this is a cve entry so we will start populating
		// the array that will be updated/inserted into the db
		$cve = array(); // empty / create array
		$ref_count = 0;
		$prod_count = 0;
		$prod_ver_count = 0;
		$cve[cve_name] = $attrs[NAME];
		$cve[published_epoch] = strtotime($attrs[PUBLISHED]);
		$cve[modified_epoch] = strtotime($attrs[MODIFIED]);
		if($cve[modified_epoch] < $oldest_update_epoch) {
			$oldest_update_epoch = $cve[modified_epoch];
		}
		//echo $oldest_update_epoch."\n";
		$cve[reject] = $attrs[REJECT];
		$cve[severity] = $attrs[SEVERITY];
		$cve[CVSS_score] = $attrs[CVSS_SCORE];
		$cve[CVSS_vector] = $attrs[CVSS_VECTOR];
		$cve[CVSS_version] = $attrs[CVSS_VERSION];
		$cve[CVSS_base_score] = $attrs[CVSS_BASE_SCORE];
		$cve[CVSS_impact_subscore] = $attrs[CVSS_IMPACT_SUBSCORE];
		$cve[CVSS_exploit_subscore] = $attrs[CVSS_EXPLOIT_SUBSCORE];
		$cve[last_cache_update_epoch] = time();
	}
	
	if($name == "DESCRIPT") {
		$cve[in_desc] = 1;
	}
	
	if($name == "REF") {
		$cve[in_ref] = 1;
		$cve[refs][$ref_count][ref_source] = $attrs[SOURCE];
		$cve[refs][$ref_count][ref_url] = $attrs[URL];
		$cve[refs][$ref_count][ref_patch] = $attrs[patch];
	}
	
	if($name == "PROD") {
		$cve[prod_name] = $attrs[NAME];
		$cve[prod_vendor] = $attrs[VENDOR];
	}
	
	if($name == "VERS") {
		$cve[cve_prod][$prod_count][prod_name] = $cve[prod_name];
		$cve[cve_prod][$prod_count][prod_vendor] = $cve[prod_vendor];
		$cve[cve_prod][$prod_count][prod_ver] = $attrs[NUM];
		$prod_count++;
	}
}

function xml_end_element($parser, $name) {
	global $cve, $ref_count, $prod_count, $db_link;
	if($name == "ENTRY") {
		//print_r($cve);
		
		// the big call
		dbf_put_cve_in_db($cve, $db_link);
	}
	
	if($name == "DESCRIPT") {
		$cve[in_desc] = 0;
	}
	
	if($name == "REF") {
		$cve[in_ref] = 0;
	}
}

function xml_character_data($parser, $data) {
	global $cve, $ref_count, $prod_count;
	if($cve[in_desc]) {
		$cve[description] = $data;
	}
	
	if($cve[in_ref]) {
		$cve[refs][$ref_count][ref_txt] = $data;
		$ref_count++;
	}
}
?>