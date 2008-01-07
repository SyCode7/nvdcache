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

$cve_id = mysqli_real_escape_string($db_link, $_REQUEST['cve_id']);
$token = $_REQUEST['token'];

if($ini_array[security][token_required] == 1 && $ini_array[security][access_token] != $token) {
	// this is very rude unsafe security.
	$xml = c_initiate_xml($ini_array);
	$xml_error = $xml->addchild('error');
	$xml_error->addchild('code', '500');
	$xml_error->addchild('description', 'Invalid token or no token given.  A token is required to communicate with this system.  Please see http://code.google.com/p/nvdcache/ for information.');
	c_announce($xml);
}

$regex_status = eregi("^cve-+[0-9]{4}-+[0-9]{4}", $cve_id);
if(!$regex_status || !$cve_id) {
	$xml = c_initiate_xml($ini_array);
	$xml_error = $xml->addchild('error');
	$xml_error->addchild('code', '400');
	$xml_error->addchild('description', 'Bad Request.  A CVE name was not given or was malformed.  Looking for this format - CVE-XXXX-XXXX');
	c_announce($xml);
}

//
// everything looks good to this point.  We will now pull together the data from the db and build an xml string to return.
//

$cve_data = dbf_getCveData($db_link, $cve_id);

echo $xml->asXML();

?>