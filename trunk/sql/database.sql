-- phpMyAdmin SQL Dump
-- version 2.10.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Sep 20, 2007 at 06:33 PM
-- Server version: 5.0.45
-- PHP Version: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Table structure for table `nicaw_accounts`
-- 

CREATE TABLE `nicaw_accounts` (
  `accno` int(11) unsigned NOT NULL,
  `email` varchar(50) collate latin1_general_ci NOT NULL,
  `rlname` varchar(50) collate latin1_general_ci NOT NULL default '',
  `location` varchar(50) collate latin1_general_ci NOT NULL default '',
  `ip` varchar(16) collate latin1_general_ci NOT NULL default '',
  `blocked` tinyint(1) NOT NULL default '0',
  `comment` tinytext collate latin1_general_ci NOT NULL,
  KEY `accno` (`accno`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `nicaw_logs`
-- 

CREATE TABLE `nicaw_logs` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `ip` varchar(16) collate latin1_general_ci NOT NULL,
  `account` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `action` tinytext collate latin1_general_ci NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `account` (`account`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `nicaw_news`
-- 

CREATE TABLE `nicaw_news` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(64) collate latin1_general_ci NOT NULL,
  `creator` varchar(25) collate latin1_general_ci NOT NULL,
  `date` int(11) NOT NULL,
  `text` text collate latin1_general_ci NOT NULL,
  `html` tinyint(1) NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `nicaw_polls`
-- 

CREATE TABLE `nicaw_polls` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `question` varchar(225) collate latin1_general_ci NOT NULL,
  `options` varchar(225) collate latin1_general_ci NOT NULL,
  `results` varchar(128) collate latin1_general_ci NOT NULL,
  `startdate` int(11) NOT NULL,
  `enddate` int(11) NOT NULL,
  `minlevel` int(11) NOT NULL default '1',
  `hidden` tinyint(1) NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `nicaw_recovery`
-- 

CREATE TABLE `nicaw_recovery` (
  `accno` int(11) unsigned NOT NULL,
  `email` varchar(60) collate latin1_general_ci NOT NULL default '',
  `date` int(11) NOT NULL default '0',
  `ip` varchar(16) collate latin1_general_ci NOT NULL default '',
  `key` char(32) collate latin1_general_ci NOT NULL,
  KEY `accno` (`accno`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `nicaw_votes`
-- 

CREATE TABLE `nicaw_votes` (
  `id` int(11) unsigned NOT NULL,
  `accno` int(11) NOT NULL,
  `ip` varchar(16) collate latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------