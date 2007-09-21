CREATE TABLE `nicaw_logs` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `ip` varchar(16) collate latin1_general_ci NOT NULL,
  `account` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `action` tinytext collate latin1_general_ci NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `account` (`account`)
);

-- --------------------------------------------------------

CREATE TABLE `nicaw_news` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(64) collate latin1_general_ci NOT NULL,
  `creator` varchar(25) collate latin1_general_ci NOT NULL,
  `date` int(11) NOT NULL,
  `text` text collate latin1_general_ci NOT NULL,
  `html` tinyint(1) NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
);

-- --------------------------------------------------------

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
);

-- --------------------------------------------------------

CREATE TABLE `nicaw_recovery` (
  `accno` int(11) unsigned NOT NULL,
  `email` varchar(60) collate latin1_general_ci NOT NULL default '',
  `date` int(11) NOT NULL default '0',
  `ip` varchar(16) collate latin1_general_ci NOT NULL default '',
  `key` char(32) collate latin1_general_ci NOT NULL,
  KEY `accno` (`accno`)
);

-- --------------------------------------------------------

CREATE TABLE `nicaw_votes` (
  `id` int(11) unsigned NOT NULL,
  `accno` int(11) NOT NULL,
  `ip` varchar(16) collate latin1_general_ci NOT NULL
);

-- --------------------------------------------------------