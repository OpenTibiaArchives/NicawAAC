<?
##################################################
#                 CONFIGURATION                  #
##################################################
# This is a configuration file for Nicaw SQL     #
# Please read comments carefully to avoid errors #
##################################################

# Set data directory of your OT server
# Please use / to separate folders and put / in the end
$cfg['dirdata'] = 'c:/otserv/devland2/data/';

$cfg['house_file'] = 'world/SadTeamMapper-house.xml';

# You can choose between 'mysql' and 'sqlite'
# SQLite will be available in next versions
$cfg['DB_Type'] = 'mysql';

# SQLite explained
# ================
# It is an PHP extension and does not require any SQL server.
# Database will be saved in a single file in data directory.
# You will need OTserv with SQLite support and SQLite database 
# browser to access it.
# http://en.wikipedia.org/wiki/SQLite_Database_Browser

# MySQL settings
$cfg['SQL_Server'] = 'localhost';
$cfg['SQL_User'] = 'root';
$cfg['SQL_Password'] = 'pass';
$cfg['SQL_Database'] = 'otserv';

# MD5 is hashing algorithm that makes passwords safer. 
# It must correspond to your OTServ configuration!
$cfg['md5passwords'] = false;

# Skin files can be found in skins folder.
# Each css file represents a skin
$cfg['skin'] = 'inferno';

# In case you want to upload skins somewhere else
$cfg['skin_url'] = 'skins/';

# CAPTCHA is used to prevent automated software from flooding server with accounts
$cfg['use_captcha'] = true;

# IP checking and session timeout
$cfg['secure_session'] = true;

# Maximum number of characters on account
$cfg['maxchars'] = 10;

# Players per highscore page
$cfg['ranks_per_page'] = 50;

# This access and above will not be in highscores
$cfg['ranks_access'] = 2;

# Home page
$cfg['start_page'] = 'notes.php';

# Name shown in window title
$cfg['server_name'] = 'Nicaw SQL';

# Server ip and port for getting status. 
# In most cases localhost should be used
$cfg['server_ip'] = 'localhost';
$cfg['server_port'] = 7171;

# Allow teleportation to temple
$cfg['char_repair'] = false;

# Email can be validated by sending login details
# For email functions to work, SMTP server must be configured correctly
$cfg['Email_Validate'] = false;

# Allow email based account recovery?
$cfg['Email_Recovery'] = false;

# Enable extension=php_openssl.dll in php.ini in order to use gmail
$cfg['SMTP_Host'] = 'ssl://smtp.gmail.com';
$cfg['SMTP_Port'] = 465;
$cfg['SMTP_Auth'] = true;
$cfg['SMTP_User'] = 'storm.ots@gmail.com';
$cfg['SMTP_Password'] = 'pass';
$cfg['SMTP_From'] = 'storm.ots@gmail.com';

# Whether to show skills in character search
$cfg['show_skills'] = true;

$cfg['skill_names'] = array('fist', 'club', 'sword', 'axe', 'distance', 'shielding', 'fishing');

# This is a perl regex. Do not change unless you know what you're doing!
$cfg['name_format'] = "/^[A-Z][a-z]{1,20}([ '-][A-Za-z][a-z]{1,15}){0,3}$/";

# Banned names
$cfg['invalid_names'] = array('^gm','^god','admin','fuck','gamemaster');

# Accounts that are allowed to access admin panel
$cfg['admin_accounts'] = array();

# Allow local connections to admin panel, even if account not listed ?
$cfg['admin_local'] = true;

# Count player as member only if level above
$cfg['guild_level'] = 20;

##################################################
#                 Town Config                    #
##################################################
$id = 11;
$cfg['temple'][$id]['name'] = 'Devland';
$cfg['temple'][$id]['x'] = 438;
$cfg['temple'][$id]['y'] = 503;
$cfg['temple'][$id]['z'] = 7;
$cfg['temple'][$id]['enabled'] = true;

$cfg['temple'][1]['name'] = 'Venore';
$cfg['temple'][2]['name'] = 'Edron';
$cfg['temple'][3]['name'] = 'Carlin';
$cfg['temple'][4]['name'] = 'Thais';
$cfg['temple'][5]['name'] = 'Ab\'Dendriel';
$cfg['temple'][6]['name'] = 'Kazordoon';
$cfg['temple'][7]['name'] = '';
$cfg['temple'][8]['name'] = 'Darashia';
$cfg['temple'][9]['name'] = 'Port Hope';
$cfg['temple'][10]['name'] = 'Liberty Bay';

##################################################
#                 Vocation Config                #
##################################################

################# No Vocation ####################
$id = 0;
$cfg['vocations'][$id]['name'] = 'No Vocation';
$cfg['vocations'][$id]['level'] = 1;
$cfg['vocations'][$id]['maglevel'] = 0;
$cfg['vocations'][$id]['health'] = 150;
$cfg['vocations'][$id]['mana'] = 0;
$cfg['vocations'][$id]['cap'] = 400;
$cfg['vocations'][$id]['enabled'] = false;

$cfg['vocations'][$id]['look'][0] = 138;
$cfg['vocations'][$id]['look'][1] = 130;

$cfg['vocations'][$id]['skills'][0] = 1;
$cfg['vocations'][$id]['skills'][1] = 1;
$cfg['vocations'][$id]['skills'][2] = 1;
$cfg['vocations'][$id]['skills'][3] = 1;
$cfg['vocations'][$id]['skills'][4] = 1;
$cfg['vocations'][$id]['skills'][5] = 1;
$cfg['vocations'][$id]['skills'][6] = 1;

$cfg['vocations'][$id]['equipment'][3] = 3939;
$cfg['vocations'][$id]['equipment'][4] = 2650;
$cfg['vocations'][$id]['equipment'][5] = 2382;
$cfg['vocations'][$id]['equipment'][10] = 2050;

################# Sorcerer #######################
$id = 1;
$cfg['vocations'][$id]['name'] = 'Sorcerer';
$cfg['vocations'][$id]['level'] = 8;
$cfg['vocations'][$id]['maglevel'] = 0;
$cfg['vocations'][$id]['health'] = 185;
$cfg['vocations'][$id]['mana'] = 40;
$cfg['vocations'][$id]['cap'] = 470;
$cfg['vocations'][$id]['enabled'] = true;

$cfg['vocations'][$id]['look'][0] = 138;
$cfg['vocations'][$id]['look'][1] = 130;

$cfg['vocations'][$id]['skills'][0] = 10;
$cfg['vocations'][$id]['skills'][1] = 10;
$cfg['vocations'][$id]['skills'][2] = 10;
$cfg['vocations'][$id]['skills'][3] = 10;
$cfg['vocations'][$id]['skills'][4] = 10;
$cfg['vocations'][$id]['skills'][5] = 10;
$cfg['vocations'][$id]['skills'][6] = 10;

$cfg['vocations'][$id]['equipment'][1] = 2480;
$cfg['vocations'][$id]['equipment'][2] = 2172;
$cfg['vocations'][$id]['equipment'][3] = 2000;
$cfg['vocations'][$id]['equipment'][4] = 2464;
$cfg['vocations'][$id]['equipment'][6] = 2530;
$cfg['vocations'][$id]['equipment'][7] = 2468;
$cfg['vocations'][$id]['equipment'][8] = 2643;

################# Druid ##########################
$id = 2;
$cfg['vocations'][$id]['name'] = 'Druid';
$cfg['vocations'][$id]['level'] = 8;
$cfg['vocations'][$id]['maglevel'] = 0;
$cfg['vocations'][$id]['health'] = 185;
$cfg['vocations'][$id]['mana'] = 40;
$cfg['vocations'][$id]['cap'] = 470;
$cfg['vocations'][$id]['enabled'] = true;

$cfg['vocations'][$id]['look'][0] = 138;
$cfg['vocations'][$id]['look'][1] = 130;

$cfg['vocations'][$id]['skills'][0] = 10;
$cfg['vocations'][$id]['skills'][1] = 10;
$cfg['vocations'][$id]['skills'][2] = 10;
$cfg['vocations'][$id]['skills'][3] = 10;
$cfg['vocations'][$id]['skills'][4] = 10;
$cfg['vocations'][$id]['skills'][5] = 10;
$cfg['vocations'][$id]['skills'][6] = 10;

$cfg['vocations'][$id]['equipment'][1] = 2480;
$cfg['vocations'][$id]['equipment'][2] = 2172;
$cfg['vocations'][$id]['equipment'][3] = 2000;
$cfg['vocations'][$id]['equipment'][4] = 2464;
$cfg['vocations'][$id]['equipment'][6] = 2530;
$cfg['vocations'][$id]['equipment'][7] = 2468;
$cfg['vocations'][$id]['equipment'][8] = 2643;

################# Palladin #######################
$id = 3;
$cfg['vocations'][$id]['name'] = 'Paladin';
$cfg['vocations'][$id]['level'] = 8;
$cfg['vocations'][$id]['maglevel'] = 0;
$cfg['vocations'][$id]['health'] = 185;
$cfg['vocations'][$id]['mana'] = 40;
$cfg['vocations'][$id]['cap'] = 470;
$cfg['vocations'][$id]['enabled'] = true;

$cfg['vocations'][$id]['look'][0] = 137;
$cfg['vocations'][$id]['look'][1] = 129;

$cfg['vocations'][$id]['skills'][0] = 10;
$cfg['vocations'][$id]['skills'][1] = 10;
$cfg['vocations'][$id]['skills'][2] = 10;
$cfg['vocations'][$id]['skills'][3] = 10;
$cfg['vocations'][$id]['skills'][4] = 10;
$cfg['vocations'][$id]['skills'][5] = 10;
$cfg['vocations'][$id]['skills'][6] = 10;

$cfg['vocations'][$id]['equipment'][1] = 2480;
$cfg['vocations'][$id]['equipment'][2] = 2172;
$cfg['vocations'][$id]['equipment'][3] = 2000;
$cfg['vocations'][$id]['equipment'][4] = 2464;
$cfg['vocations'][$id]['equipment'][6] = 2530;
$cfg['vocations'][$id]['equipment'][7] = 2468;
$cfg['vocations'][$id]['equipment'][8] = 2643;

################# Knight #########################
$id = 4;
$cfg['vocations'][$id]['name'] = 'Knight';
$cfg['vocations'][$id]['level'] = 8;
$cfg['vocations'][$id]['maglevel'] = 0;
$cfg['vocations'][$id]['health'] = 185;
$cfg['vocations'][$id]['mana'] = 40;
$cfg['vocations'][$id]['cap'] = 470;
$cfg['vocations'][$id]['enabled'] = true;

$cfg['vocations'][$id]['look'][0] = 139;
$cfg['vocations'][$id]['look'][1] = 131;

$cfg['vocations'][$id]['skills'][0] = 10;
$cfg['vocations'][$id]['skills'][1] = 10;
$cfg['vocations'][$id]['skills'][2] = 10;
$cfg['vocations'][$id]['skills'][3] = 10;
$cfg['vocations'][$id]['skills'][4] = 10;
$cfg['vocations'][$id]['skills'][5] = 10;
$cfg['vocations'][$id]['skills'][6] = 10;

$cfg['vocations'][$id]['equipment'][1] = 2480;
$cfg['vocations'][$id]['equipment'][2] = 2172;
$cfg['vocations'][$id]['equipment'][3] = 2000;
$cfg['vocations'][$id]['equipment'][4] = 2464;
$cfg['vocations'][$id]['equipment'][6] = 2530;
$cfg['vocations'][$id]['equipment'][7] = 2468;
$cfg['vocations'][$id]['equipment'][8] = 2643;

################# Other IDs ######################

$cfg['vocations'][5]['name'] = 'Master Sorcerer';
$cfg['vocations'][6]['name'] = 'Elder Druid';
$cfg['vocations'][7]['name'] = 'Royal Paladin';
$cfg['vocations'][8]['name'] = 'Elite Knight';