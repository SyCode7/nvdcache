; This is the nvdCache configuration file.
; 
; Copyright 2007 The Hursk Group, LLC
;
;
; Note: if a value in the ini file contains any non-alphanumeric characters it needs to be enclosed in double-quotes (").
; 
; Note: There are reserved words which must not be used as keys for ini files. These include: null, yes, no, 
; true, and false. Values null, no and false results in "", yes and true results in "1".
; 
; Note: Characters {}|&~![()" must not be used anywhere in the key and have a special meaning in the value.
; Warning: cannot cope with values containing the equal sign (=).
;

[database]
host = "hostname or ip address"
user = "username for db authentication"
password = "user password"
db_name = "name of the database"

[security]
token_required = "0" ; will the program calling this cache be required to hand over the access token below
access_token = "put random stuff here"

[nvdCache]
version = "0.1"
update_freq_hours = "12"
supported_nvd_xml_version = "1.2"

[cve]
url_base = "http://nvd.nist.gov/download/"
url_cve_modified = "nvdcve-modified.xml"
url_cve_recent = "nvdcve-recent.xml"
url_cve_year_pre = "nvdcve-"
url_cve_year_post = ".xml"