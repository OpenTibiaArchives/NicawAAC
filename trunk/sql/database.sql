-- --------------------------------------------------------

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
);

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
);

-- --------------------------------------------------------

-- 
-- Table structure for table `nicaw_poll_options`
-- 

CREATE TABLE `nicaw_poll_options` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `poll_id` int(10) unsigned NOT NULL,
  `option` varchar(255) collate latin1_general_ci NOT NULL,
  UNIQUE KEY `id` (`id`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `nicaw_poll_questions`
-- 

CREATE TABLE `nicaw_poll_questions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `question` varchar(255) collate latin1_general_ci NOT NULL,
  `startdate` int(10) unsigned NOT NULL,
  `enddate` int(10) unsigned NOT NULL,
  `minlevel` int(10) unsigned NOT NULL,
  `hidden` tinyint(1) NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `nicaw_poll_votes`
-- 

CREATE TABLE `nicaw_poll_votes` (
  `option_id` int(11) unsigned NOT NULL,
  `accno` int(11) NOT NULL,
  `ip` varchar(16) collate latin1_general_ci NOT NULL
);

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