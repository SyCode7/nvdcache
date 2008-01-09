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
$this_programs_name = "cve";

// load ini file into an array	
if (file_exists("local_config.ini")) {
	$ini_array = parse_ini_file("local_config.ini", true);
} else {
	$ini_array = parse_ini_file("config.ini", true); // load the main one
	if(!$ini_array) {
		$xml = c_initiate_xml($ini_array);
		$xml_error = $xml->addchild('error');
		$xml_error->addchild('code', '500');
		$xml_error->addchild('description', 'Configuration files were not found.  Please review the http://code.google.com/p/nvdcache/ file.');
		c_announce($xml);
	}
}

$start_db_con_call = time();
$db_link = dbf_connectDB($ini_array);
$end_db_con_call = time();

$seconds_to_make_con = $end_db_con_call - $start_db_con_call;

$start_db_query_call = time();
$cache_stats = dbf_cache_stats($db_link);
$end_db_query_call = time();

$seconds_to_make_query = $end_db_query_call - $start_db_query_call;

$nvdCache_age_seconds = time() - $cache_stats[last_db_update_epoch];

$msg = "Everything looks honky dory!";

$xml = c_initiate_xml($ini_array);
$xml_msg = $xml->addchild('status');
$xml_msg->addchild('code', '200');
$xml_msg->addchild('description', $msg);
$xml_msg->addchild('cache_age_seconds', $nvdCache_age_seconds);
$xml_msg->addchild('seconds_to_make_query', $seconds_to_make_query);
$xml_msg->addchild('cache_age_seconds', $seconds_to_make_con);
c_announce($xml);

?>