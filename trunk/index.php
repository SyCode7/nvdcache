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

$db_link = dbf_connectDB($ini_array);

$cache_stats = dbf_cache_stats($db_link);

$nvdCache_age_seconds = time() - $cache_stats[last_db_update_epoch];

$xml = c_initiate_xml($ini_array);
$xml_msg = $xml->addchild('message');
$xml_msg->addchild('code', '200');
$xml_msg->addchild('description', $msg);
$xml_msg->addchild('nvdCache_age_seconds', $nvdCache_age_seconds);
c_announce($xml);

?>