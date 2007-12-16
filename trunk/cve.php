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
$ini_array = parse_ini_file("local_config.ini", true); // look for a local copy first
if (!$ini_array) {
	$ini_array = parse_ini_file("config.ini", true); // load the main one
} elseif(!$ini_array) {
	$xml = c_initiate_xml($ini_array);
	$xml_error = $xml->addchild('error');
	$xml_error->addchild('code', '500');
	$xml_error->addchild('description', 'Configuration files were not found.  Please review the README.txt file.');
	c_announce($xml);
}

$db_link = dbf_connectDB($ini_array);

$cache_stats = dbf_cache_stats($db_link);

$cve_id = $_REQUEST['id'];
$token = $_REQUEST['token'];

if($ini_array[security][token_required] == 1 && $ini_array[security][access_token] != $token) {
	// this is very rude unsafe security.
	echo "Invalid token or no token given.  A token is required to communicate with this system.  Please see README.txt for information.";
	exit(1);
}



echo "Bob was here!";
?>