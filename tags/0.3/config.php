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

// nvdCache infomration
$config_nvdcache[version] = "0.3";
$config_nvdcache[update_freq_hours] = "12";
$config_nvdcache[supported_nvd_xml_version] = "1.2";
$config_nvdcache[cacheHost] = ""; //if you're hosting the data put your name here

// database stuff
$config_database[host] = ""; //hostname or ip addr of db
$config_database[user] = ""; //user name to connect as
$config_database[password] = ""; //user password
$config_database[db_name] = "nvdCache"; //Should be nvdCache, but could be different
$config_database[port] = "3306"; //3306 is the standard MySQL port
$config_database[socket] = ""; //Socket file

// token related (this will probably be going away soon) <-- 11-2008
$config_sec[token_required] = "0"; //will the program calling this cache be required to hand over the access token below
$config_sec[access_token] = ""; //you would put the token data in this filed.

// cve repository information
$config_cve[proxy_url] = "";
$config_cve[proxy_port] = "";
$config_cve[protocol] = "http://";
$config_cve[url_base] = "nvd.nist.gov/download/";
$config_cve[url_cve_modified] = "nvdcve-modified.xml";
$config_cve[url_cve_recent] = "nvdcve-recent.xml";
$config_cve[url_cve_year_pre] = "nvdcve-";
$config_cve[url_cve_year_post] = ".xml";

?>