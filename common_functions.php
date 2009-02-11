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

function c_initiate_xml($config_nvdcache, $text="0") {
	$xml_string = '<?xml-stylesheet href="nvdCache-style.css" type="text/css"?>';
	$xml_string .= '<nvdCache version="'.$config_nvdcache[version].'" cacheHost="'.$config_nvdcache[cacheHost].'"></nvdCache>';
	
	if($text) {
		return $xml_string;
	} else {
		$xml = new SimpleXMLElement($xml_string);
		return $xml;
	}
	
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