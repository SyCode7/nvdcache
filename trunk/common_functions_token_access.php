<?php
/*
fileHashDB
Copyright (c) 2009 The Hursk Group, LLC. All rights reserved.

www.hursk.com

hurskgroup@hursk.com

This software is distributed WITHOUT ANY WARRANTY; without even
the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
PURPOSE.

*/

function fta_logHit($_SERVER, $fta_config_data) {
	//print_r($fta_config_data);
	
	// get time in epoch of the hit
	$hit_time = time();
	
	// get the source ip addr
	// includes logic for reverse proxy
	if($fta_config_data[reverse_proxy]) {
		$source_ip = $_SERVER[HTTP_X_FORWARDED_FOR];
	} else {
		$source_ip = $_SERVER[REMOTE_ADDR];
	}
	
	// establish db connection
	$fta_db_link = fta_connectDB($fta_config_data);
	
	// build query
	$query = "INSERT INTO hit VALUES ('$source_ip', '$hit_time')";
	
	if(!$result = mysqli_query($fta_db_link, $query)) {
		echo "error ".mysqli_error($fta_db_link);
	}
	
	//mysqli_query($fta_db_link, $query);
	
}

function fta_connectDB($fta_config_data) {
	//print_r($fta_config_data);
	
	$fta_db_link = mysqli_connect($fta_config_data[db][host], $fta_config_data[db][user], $fta_config_data[db][password], $fta_config_data[db][db_name], $fta_config_data[db][port], $fta_config_data[db][socket]);
	
	return $fta_db_link;
}

?>