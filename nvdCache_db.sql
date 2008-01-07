SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `nvdCache`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `cve`
-- 

CREATE TABLE `cve` (
  `cve_name` varchar(13) NOT NULL,
  `reject` tinyint(1) NOT NULL default '0',
  `last_cache_update_epoch` int(10) NOT NULL,
  `published_epoch` varchar(10) NOT NULL,
  `modified_epoch` varchar(10) NOT NULL,
  `severity` varchar(32) NOT NULL,
  `CVSS_score` varchar(10) NOT NULL,
  `CVSS_vector` varchar(64) NOT NULL,
  `CVSS_version` varchar(32) NOT NULL,
  `CVSS_base_score` varchar(10) NOT NULL,
  `CVSS_impact_subscore` varchar(10) NOT NULL,
  `CVSS_exploit_subscore` varchar(10) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`cve_name`),
  KEY `last_modified_epoch` (`modified_epoch`),
  KEY `last_update_epoch` (`last_cache_update_epoch`),
  KEY `reject` (`reject`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `cve_prod`
-- 

CREATE TABLE `cve_prod` (
  `cve_prod_id` int(12) NOT NULL auto_increment,
  `cve_name` varchar(13) NOT NULL,
  `prod_name` varchar(128) NOT NULL,
  `prod_vendor` varchar(128) NOT NULL,
  `vers_num` varchar(64) NOT NULL,
  `vers_edition` varchar(128) NOT NULL,
  PRIMARY KEY  (`cve_prod_id`),
  KEY `cve_name` (`cve_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `cve_ref`
-- 

CREATE TABLE `cve_ref` (
  `cve_ref_id` int(12) NOT NULL auto_increment,
  `cve_name` varchar(13) NOT NULL,
  `ref_source` varchar(32) NOT NULL,
  `ref_url` varchar(256) NOT NULL,
  `ref_patch` varchar(12) NOT NULL,
  `ref_text` text NOT NULL,
  PRIMARY KEY  (`cve_ref_id`),
  KEY `cve_name` (`cve_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `statistics`
-- 

CREATE TABLE `statistics` (
  `stat_id` int(12) NOT NULL auto_increment,
  `last_db_update_epoch` int(10) NOT NULL,
  PRIMARY KEY  (`stat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT INTO `statistics` (`stat_id`, `last_db_update_epoch`) VALUES
(1, 0);