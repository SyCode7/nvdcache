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
$this_programs_name = "cacheStats";

// load config file	
if (file_exists("local_config.php")) {
	require 'local_config.php';
} else {
	require 'config.php';
}

// gather data on connection call to db
$start_db_con_call = time();
$db_link = dbf_connectDB($config_database);
$end_db_con_call = time();
$seconds_to_make_con = $end_db_con_call - $start_db_con_call;

// gather data on basic query call
$start_db_query_call = time();
$cache_stats = dbf_cache_stats($db_link);
$end_db_query_call = time();
$seconds_to_make_query = $end_db_query_call - $start_db_query_call;

// get teh age of the cache since last update
$nvdCache_age_seconds = time() - $cache_stats[last_db_update_epoch];

//  Build the response
$xml = c_initiate_xml($config_nvdcache);
$xml_msg = $xml->addchild('status');
$xml_msg->addchild('code', '200');
$xml_msg->addchild('cache_age_seconds', $nvdCache_age_seconds);
$xml_msg->addchild('seconds_to_make_db_connection', $seconds_to_make_con);
$xml_msg->addchild('seconds_to_make_query', $seconds_to_make_query);
c_announce($xml);

?>