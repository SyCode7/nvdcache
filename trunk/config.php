/*
nvdCache
Copyright (c) 2007 The Hursk Group, LLC. All rights reserved.

www.hursk.com

hurskgroup@hursk.com

This software is distributed WITHOUT ANY WARRANTY; without even
the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
PURPOSE.

*/

[database]
$host =		"" //hostname or ip addr of db
user = 		"" //user name to connect as
password = 	"" //user password
db_name = 	"nvdCache" //Should be nvdCache, but could be different
port = 		"3306" //3306 is the standard MySQL port
socket = 	"" //Socket file

[security]
token_required = 	"0" //will the program calling this cache be required to hand over the access token below
access_token = 		"" //you would put the token data in this filed.

[nvdCache]
version = "0.2"
update_freq_hours = "12"
supported_nvd_xml_version = "1.2"
cacheHost = "" //if you're hosting the data put your name here

[cve]
url_base = "http://nvd.nist.gov/download/"
url_cve_modified = "nvdcve-modified.xml"
url_cve_recent = "nvdcve-recent.xml"
url_cve_year_pre = "nvdcve-"
url_cve_year_post = ".xml"