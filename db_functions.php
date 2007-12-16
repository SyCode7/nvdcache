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
	
	return $cache_stats;
}

function dbf_put_cve_in_db($cve, $db_link) {
	// if it exists teplace the current cve
	$cve[description] = mysqli_real_escape_string($db_link, $cve[description]);
	$query = "REPLACE INTO cve SET cve_name = '$cve[cve_name]', reject = '$cve[reject]', published_epoch = '$cve[published_epoch]', modified_epoch = '$cve[modified_epoch]', last_cache_update_epoch = '$cve[last_cache_update_epoch]', severity = '$cve[severity]', CVSS_score = '$cve[CVSS_score]', CVSS_vector = '$cve[CVSS_vector]', CVSS_version = '$cve[CVSS_version]', CVSS_base_score = '$cve[CVSS_base_score]', CVSS_impact_subscore = '$cve[CVSS_impact_subscore]', CVSS_exploit_subscore = '$cve[CVSS_exploit_subscore]', description = '$cve[description]'";
	
	
	if(!$result = mysqli_query($db_link, $query)) {
		$xml = c_initiate_xml($ini_array);
		$xml_error = $xml->addchild('error');
		$xml_error->addchild('code', '500');
		$xml_error->addchild('description', 'DB Error: '.mysqli_error($db_link));
		c_announce($xml);
	}
	
	// delete any exsisting refrences or software items
	$query = "DELETE FROM cve_ref WHERE cve_name = '$cve[cve_name]'";
	
	if(!$result = mysqli_query($db_link, $query)) {
		$xml = c_initiate_xml($ini_array);
		$xml_error = $xml->addchild('error');
		$xml_error->addchild('code', '500');
		$xml_error->addchild('description', 'DB Error: '.mysqli_error($db_link));
		c_announce($xml);
	}
	
	// if there are refrences add them
	if($cve[refs]) {
		foreach ($cve[refs] as $key => $row) {
			$row[ref_txt] = mysqli_real_escape_string($db_link, $row[ref_txt]);
			$row[ref_source] = mysqli_real_escape_string($db_link, $row[ref_source]);
			$row[ref_patch] = mysqli_real_escape_string($db_link, $row[ref_patch]);
			$query = "INSERT INTO cve_ref (cve_name, ref_source, ref_url, ref_patch, ref_text) VALUES ('$cve[cve_name]', '$row[ref_source]', '$row[ref_url]', '$row[ref_patch]', '$row[ref_txt]')";
			if(!$result = mysqli_query($db_link, $query)) {
				$xml = c_initiate_xml($ini_array);
				$xml_error = $xml->addchild('error');
				$xml_error->addchild('code', '500');
				$xml_error->addchild('description', 'DB Error: '.mysqli_error($db_link));
				c_announce($xml);
			}
		}
	}
	
	$query = "DELETE FROM cve_prod WHERE cve_name = '$cve[cve_name]'";
	
	if(!$result = mysqli_query($db_link, $query)) {
		$xml = c_initiate_xml($ini_array);
		$xml_error = $xml->addchild('error');
		$xml_error->addchild('code', '500');
		$xml_error->addchild('description', 'DB Error: '.mysqli_error($db_link));
		c_announce($xml);
	}
	
	// add any software refrences add those also
	if($cve[cve_prod]) {
		//echo "in prg\n";
		foreach ($cve[cve_prod] as $key2 => $row2) {
			//echo "in foreach\n";
			$row2[prod_name] = mysqli_real_escape_string($db_link, $row2[prod_name]);
			$row2[prod_vendor] = mysqli_real_escape_string($db_link, $row2[prod_vendor]);
			$row2[prod_ver] = mysqli_real_escape_string($db_link, $row2[prod_ver]);
			$query = "INSERT INTO cve_prod (cve_name, prod_name, prod_vendor, vers_num) VALUES ('$cve[cve_name]', '$row2[prod_name]', '$row2[prod_vendor]', '$row2[prod_ver]')";
			
			if(!$result = mysqli_query($db_link, $query)) {
				$xml = c_initiate_xml($ini_array);
				$xml_error = $xml->addchild('error');
				$xml_error->addchild('code', '500');
				$xml_error->addchild('description', 'DB Error: '.mysqli_error($db_link));
				c_announce($xml);
			}
		}
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

?>