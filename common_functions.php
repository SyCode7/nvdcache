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

function c_initiate_xml($ini_array) {
	$xml_string = '<?xml version="1.0" encoding="ISO-8859-1"?><nvdCache version="'.$ini_array[nvdCache][version].'"></nvdCache>';
	$xml = new SimpleXMLElement($xml_string);
	return $xml;
}

function c_announce($xml) {
	if(isset($xml->error)) {
		header ("content-type: text/xml");
		echo $xml->asXML();
		exit(1);
	} else {
		header ("content-type: text/xml");
		echo $xml->asXML();
		exit(0);
	}
}
?>