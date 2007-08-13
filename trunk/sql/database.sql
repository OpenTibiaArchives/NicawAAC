-- phpMyAdmin SQL Dump
-- version 2.9.1.1
-- http://www.phpmyadmin.net
-- Import this to your otserv database

CREATE TABLE `nicaw_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ip` VARCHAR(16) NOT NULL,
  `account` INT(11) NOT NULL,
  `date` INT(11) NOT NULL,
  `action` TINYTEXT NOT NULL,
  UNIQUE KEY (`id`),
  KEY(`account`)
);
CREATE TABLE `nicaw_polls` (
  `id` int(11) NOT NULL,
  `question` varchar(225) NOT NULL,
  `options` varchar(225) NOT NULL,
  `results` varchar(128) NOT NULL,
  `startdate` int(11) NOT NULL,
  `enddate` int(11) NOT NULL,
  `minlevel` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`)
);
INSERT INTO `nicaw_polls` (`question`, `options`, `results`, `startdate`, `enddate`, `minlevel`) VALUES ('Do you enjoy playing here?', 'Yes;No', '0;0', 1164931200, 1259625600, 10);
CREATE TABLE `nicaw_votes` (
  `id` int(11) NOT NULL,
  `accno` int(11) NOT NULL,
  `ip` varchar(16) NOT NULL
);
CREATE TABLE `nicaw_accounts` (
  `accno` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `rlname` varchar(50) NOT NULL default '',
  `location` varchar(50) NOT NULL default '',
  `ip` varchar(16) NOT NULL default '',
  `blocked` tinyint(1) NOT NULL default '0',
  `comment` tinytext NOT NULL default '',
  KEY `accno` (`accno`)
);
CREATE TABLE `nicaw_recovery` (
  `accno` int(11) NOT NULL,
  `email` varchar(60) NOT NULL default'',
  `date` int(11) NOT NULL default'0',
  `ip` varchar(16) NOT NULL default'',
  `key` char(32) NOT NULL,
  KEY `accno` (`accno`)
);

CREATE TABLE `nicaw_news` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(64) collate latin1_general_ci NOT NULL,
  `creator` varchar(25) collate latin1_general_ci NOT NULL,
  `date` int(11) NOT NULL,
  `text` text collate latin1_general_ci NOT NULL,
  `html` tinyint(1) NOT NULL default '0',
  KEY `id` (`id`)
);