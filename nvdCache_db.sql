-- Host: localhost
-- Generation Time: Nov 14, 2008 at 05:59 PM
-- Server version: 5.0.67
-- PHP Version: 5.2.6


SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE DATABASE `nvdCache`;

--
-- Database: `nvdCache`
--

-- --------------------------------------------------------

--
-- Table structure for table `nvdData`
--

CREATE TABLE IF NOT EXISTS `nvdData` (
  `name` varchar(64) NOT NULL,
  `type` varchar(64) NOT NULL,
  `entry` text NOT NULL,
  PRIMARY KEY  (`name`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `statistics`
--

CREATE TABLE IF NOT EXISTS `statistics` (
  `stat_id` int(12) NOT NULL auto_increment,
  `last_db_update_epoch` int(10) NOT NULL,
  PRIMARY KEY  (`stat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;