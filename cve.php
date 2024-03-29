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
// used to track who's using system and throttle
// requests later on
//require 'common_functions_token_access.php';

$start_time_epoch = time();
$this_programs_version = "0.3";
$this_programs_name = "cve";

// load config file	
if (file_exists("local_config.php")) {
	require 'local_config.php';
} else {
	require 'config.php';
}

$db_link = dbf_connectDB($config_database);

$cache_stats = dbf_cache_stats($db_link);

// log hit to service
//fta_logHit($_SERVER, $fta_config_data);


/* parse the URL */

//echo $HTTP_SERVER_VARS["REQUEST_URI"];


$exp1 = explode("/",$HTTP_SERVER_VARS["REQUEST_URI"]);
// $hash is the value passed to the script.  this needs to be handled carefuly!
$cve_id = $exp1[count($exp1)-1];

//$cve_id = $_REQUEST['cve_id'];
//$token = $_REQUEST['token'];

if($ini_array[security][token_required] == 1 && $ini_array[security][access_token] != $token) {
	// this is very rude unsafe security.
	$xml = c_initiate_xml($config_nvdcache);
	$xml_error = $xml->addchild('error');
	$xml_error->addchild('code', '500');
	$xml_error->addchild('description', 'Invalid token or no token given.  A token is required to communicate with this system.  Please see http://code.google.com/p/nvdcache/ for information.');
	c_announce($xml);
}

$regex_status = eregi("^cve-[0-9]{4}-[0-9]{4}$", $cve_id);
if(!$regex_status || !$cve_id) {
	$xml = c_initiate_xml($config_nvdcache);
	$xml_error = $xml->addchild('error');
	$xml_error->addchild('code', '400');
	$xml_error->addchild('description', 'Bad Request.  A CVE name was not given or was malformed.  Looking for this format - CVE-XXXX-XXXX');
	c_announce($xml);
}

//
// everything looks good to this point.  We will now pull together the data from the db and build an xml string to return.
//

$xml_cve = dbf_getEntryData($db_link, strtoupper($cve_id), 'CVE', $config_nvdcache);

//echo $xml_cve;

c_announce($xml_cve);

?>