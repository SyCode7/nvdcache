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
	$cache_stats[minutes_since_last_update] = $seconds_since_last_update/60;
	$cache_stats[hours_since_last_update] = $minutes_since_last_update/60;
	
	return $cache_stats;
}

function dbf_put_entry_in_db($entry, $db_link) {
	// make sql safe the text
	$entry_mysql_safe = mysqli_real_escape_string($db_link, $entry);
	
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

function dbf_new_database($db_link) {
	$query = "INSERT INTO statistics (last_db_update_epoch) VALUES ('".time()."')";
	if(!$result = mysqli_query($db_link, $query)) {
		$xml = c_initiate_xml($ini_array);
		$xml_error = $xml->addchild('error');
		$xml_error->addchild('code', '500');
		$xml_error->addchild('description', 'DB Error: '.mysqli_error($db_link));
		c_announce($xml);
	}
}

//
function dbf_getCveData($db_link, $cve_id, $ini_array) {
	$query = "SELECT * FROM cve WHERE cve_name = '$cve_id'";
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
		$xml_error->addchild('description', 'CVE entry provided was not found.');
		c_announce($xml);
	}
	
	// start building the xml data string
	$xml = c_initiate_xml($ini_array);
	$xml_entry = $xml->addchild('entry');
	$xml_entry['type'] = "CVE"; 
	
	while ($row = mysqli_fetch_assoc($result)) {
		
		$xml_entry['name'] = $row[cve_name];
		$xml_entry['published'] = date('Y-m-d', $row[published_epoch]);
		$xml_entry['modified'] = date('Y-m-d', $row[modified_epoch]);
		$xml_entry['severity'] = $row[severity];
		$xml_entry['CVSS_score'] = $row[CVSS_score];
		$xml_entry['CVSS_vector'] = $row[CVSS_vector];
		$xml_entry['CVSS_version'] = $row[CVSS_version];
		$xml_entry['CVSS_base_score'] = $row[CVSS_base_score];
		$xml_entry['CVSS_impact_subscore'] = $row[CVSS_impact_subscore];
		$xml_entry['CVSS_exploit_subscore'] = $row[CVSS_exploit_subscore];
		
		$xml_desc = $xml_entry->addchild('desc');
		
		$xml_descript = $xml_desc->addchild('descript', $row[description]);
		
		//echo $row[description]."\n";
		
		$xml_descript['source'] = "CVE";
	}
	
	//get any refrences
	
	$query = "SELECT * FROM cve_ref WHERE cve_name = '$cve_id'";
	if(!$result = mysqli_query($db_link, $query)) {
		$xml = c_initiate_xml($ini_array);
		$xml_error = $xml->addchild('error');
		$xml_error->addchild('code', '500');
		$xml_error->addchild('description', 'DB Error: '.mysqli_error($db_link));
		c_announce($xml);
	}
	
	$xml_refs = $xml_entry->addchild('refs');
	
	while ($row = mysqli_fetch_assoc($result)) {
		$xml_ref = $xml_refs->addchild('ref', $row[ref_text]);
		$xml_ref['source'] = $row[ref_source];
		$xml_ref['url'] = $row[ref_url];
		$xml_ref['patch'] = $row[ref_patch];
	}
	
	
	// get the product data
	
	$query = "SELECT * FROM cve_prod WHERE cve_name = '$cve_id'";
	if(!$result = mysqli_query($db_link, $query)) {
		$xml = c_initiate_xml($ini_array);
		$xml_error = $xml->addchild('error');
		$xml_error->addchild('code', '500');
		$xml_error->addchild('description', 'DB Error: '.mysqli_error($db_link));
		c_announce($xml);
	}
	
	$xml_vuln_soft = $xml_entry->addchild('vuln_soft');
	
	while ($row = mysqli_fetch_assoc($result)) {
		$xml_prod = $xml_vuln_soft->addchild('prod');
		$xml_prod['name'] = $row[prod_name];
		$xml_prod['vendor'] = $row[prod_vendor];
		$xml_prod['version'] = $row[vers_num];
	}
	
	return $xml;
}

?>