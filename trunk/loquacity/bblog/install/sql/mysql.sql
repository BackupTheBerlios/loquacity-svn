--Creating Tables 

CREATE TABLE `__pfx__comments` (
	`commentid` int(10) unsigned NOT NULL auto_increment,
	`parentid` int(10) unsigned NOT NULL default '0',
	`postid` int(10) unsigned NOT NULL default '0',
	`title` varchar(255) NOT NULL default '',
	`type` enum('comment','trackback') NOT NULL default 'comment',
	`posttime` int(11) default NULL,
	`postername` varchar(100) NOT NULL default '',
	`posteremail` varchar(100) NOT NULL default '',
	`posterwebsite` varchar(255) NOT NULL default '',
	`posternotify` tinyint(1) NOT NULL default '0',
	`pubemail` tinyint(1) NOT NULL default '0',
	`pubwebsite` tinyint(1) NOT NULL default '0',
	`ip` varchar(16) NOT NULL default '',
	`commenttext` text NOT NULL,
	`deleted` enum('true','false') NOT NULL default 'false',
	`onhold` tinyint(1) NOT NULL default '0',
	PRIMARY KEY  (`commentid`),
	FULLTEXT KEY `commenttext` (`commenttext`)
) TYPE=MyISAM;

CREATE TABLE `__pfx__config` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(50) NOT NULL default '',
	`value` varchar(255) NOT NULL default '',
	PRIMARY KEY  (`id`)
) TYPE=MyISAM;


CREATE TABLE `__pfx__plugins` (
	`id` int(11) NOT NULL auto_increment,
	`type` varchar(50) NOT NULL default 'admin',
	`name` varchar(60) NOT NULL default '',
	`ordervalue` decimal(3,2) NOT NULL default '50.00',
	`nicename` varchar(127) NOT NULL default '',
	`description` text NOT NULL,
	`template` varchar(100) NOT NULL default '',
	`help` mediumtext NOT NULL,
	`authors` varchar(255) NOT NULL default '',
	`licence` varchar(50) NOT NULL default '',
	PRIMARY KEY  (`id`)
) TYPE=MyISAM;

CREATE TABLE `__pfx__posts` (
	`postid` int(10) unsigned NOT NULL auto_increment,
	`title` varchar(255) NOT NULL default '',
	`body` mediumtext NOT NULL,
	`posttime` int(11) NOT NULL default '0',
	`modifytime` int(11) NOT NULL default '0',
	`status` enum('live','draft') NOT NULL default 'live',
	`modifier` varchar(30) NOT NULL default '',
	`sections` varchar(255) NOT NULL default '',
	`ownerid` int(10) NOT NULL default '0',
	`hidefromhome` tinyint(1) NOT NULL default '0',
	`allowcomments` enum('allow','timed','disallow') NOT NULL default 'allow',
	`autodisabledate` int(11) NOT NULL default '0',
	`commentcount` int(11) NOT NULL default '0',
	PRIMARY KEY  (`postid`),
	KEY ownerid (ownerid)
) TYPE=MyISAM;


CREATE TABLE `__pfx__sections` (
	`sectionid` int(11) NOT NULL auto_increment,
	`nicename` varchar(255) NOT NULL default '',
	`name` varchar(60) NOT NULL default '',
	PRIMARY KEY  (`sectionid`)
) TYPE=MyISAM;



CREATE TABLE `__pfx__referers` (
	`visitID` int(11) NOT NULL auto_increment,
	`visitTime` timestamp(14) NOT NULL,
	`visitURL` char(250) default NULL,
	`referingURL` char(250) default NULL,
	`baseDomain` char(250) default NULL,
	PRIMARY KEY  (`visitID`)
) TYPE=MyISAM;

CREATE TABLE __pfx__authors (
	id int(10) NOT NULL auto_increment,
	nickname varchar(20) NOT NULL default '',
	email varchar(100) NOT NULL default '',
	password varchar(20) NOT NULL default '',
	fullname varchar(50) NOT NULL default '',
	url varchar(50) NOT NULL default '',
	icq int(10) unsigned NOT NULL default '0',
	profession varchar(30) NOT NULL default '',
	likes text NOT NULL,
	dislikes text NOT NULL,
	location varchar(25) NOT NULL default '',
	aboutme text NOT NULL,
	PRIMARY KEY  (id)
) TYPE=MyISAM;



CREATE TABLE `__pfx__rss` (
	`id` int(11) NOT NULL auto_increment,
	`url` text NOT NULL,
	`input_charset` text NOT NULL,
	PRIMARY KEY  (`id`)
) TYPE=MyISAM;


CREATE TABLE __pfx__links (
	linkid int(11) NOT NULL auto_increment,
	nicename varchar(255) NOT NULL,
	url varchar(255) NOT NULL default '',
	category int(11) NOT NULL, 
	position int(8) NOT NULL default '10',
	PRIMARY KEY  (linkid)
) TYPE=MyISAM;


CREATE TABLE __pfx__categories (
	categoryid int(11) NOT NULL auto_increment,
	name varchar(60) NOT NULL,
	PRIMARY KEY  (categoryid)
) TYPE=MyISAM;

-- inserting data 
INSERT INTO `__pfx__rss` VALUES (9, '', '');
INSERT INTO `__pfx__rss` VALUES (8, '', '');
INSERT INTO `__pfx__rss` VALUES (7, '', '');
INSERT INTO `__pfx__rss` VALUES (6, '', '');
INSERT INTO `__pfx__rss` VALUES (5, '', '');
INSERT INTO `__pfx__rss` VALUES (4, '', '');
INSERT INTO `__pfx__rss` VALUES (3, '', '');
INSERT INTO `__pfx__rss` VALUES (2, '', '');
INSERT INTO `__pfx__rss` VALUES (1, 'http://www.loquacity.info/rdf.php', 'I88592');

INSERT INTO `__pfx__config` (`id`, `name`, `value`) VALUES
	(null, 'EMAIL', '".$config['email']."'),
	(null, 'BLOGNAME', '".$config['blogname']."'),
	(null, 'TEMPLATE', 'lines'),
	(null, 'DB_TEMPLATES', 'false'),
	(null, 'DEFAULT_MODIFIER', 'simple'),
	(null, 'CHARSET', 'UTF-8'),
	(null, 'VERSION', '0.76'),
	(null, 'DIRECTION', 'LTR'),
	(null, 'DEFAULT_STATUS', 'live'),
	(null, 'PING','bblog.com/ping.php'),
	(null, 'COMMENT_TIME_LIMIT','1'),
	(null, 'NOTIFY','false'),
	(null, 'BLOG_DESCRIPTION', '".$config['blogdescription']."'),
	(null, 'COMMENT_MODERATION','urlonly'),
	(null, 'META_DESCRIPTION','Some words about this blog'),
	(null, 'META_KEYWORDS','work,life,play,web design'),
	(null, 'LAST_MODIFIED', UNIX_TIMESTAMP();
	(null,'CAPTCHA_ENABLE','false'),
	(null,'CAPTCHA_WIDTH','200'),
	(null,'CAPTCHA_HEIGHT','50'),
	(null,'CAPTCHA_CHARACTERS','5'),
	(null,'CAPTCHA_LINES','70'),
	(null,'CAPTCHA_ENABLE_SHADOWS','false'),
	(null,'CAPTCHA_OWNER_TEXT','false'),
	(null,'CAPTCHA_CHARACTER_SET','A-Z'),
	(null,'CAPTCHA_CASE_INSENSITIVE','false'),
	(null,'CAPTCHA_BACKGROUND',''),
	(null,'CAPTCHA_MIN_FONT','16'),
	(null,'CAPTCHA_MAX_FONT','25'),
	(null,'CAPTCHA_USE_COLOR','false'),
	(null,'CAPTCHA_GRAPHIC_TYPE','jpg');
	
INSERT INTO __pfx__categories VALUES (1,'Navigation');
INSERT INTO __pfx__categories VALUES (2,'Blogs I read');

INSERT INTO __pfx__links VALUES (1,'Home','__url__',1,20);
INSERT INTO __pfx__links VALUES (2,'Archives','__url__archives.php',1,30);
INSERT INTO __pfx__links VALUES (3,'RSS 2.0 Feed','__url__rss.php?ver=2',1,40);


INSERT INTO `__pfx__authors` (`nickname`,`password`,`email`,`fullname`) VALUES
('__author__','__password__','__email__','__real_name__');


INSERT INTO `__pfx__posts` (`postid`, `title`, `body`, `posttime`, `modifytime`, `status`, `modifier`, `sections`, `commentcount`,`ownerid`) VALUES (1, 'First Post', '[b]This is the first post of Loquacity.[/b][p]You may delete this post in the admin section. Make sure you have deleted the install file and changed the admin password.[/p] [p]Be sure to visit the [url=http://forum.loquacity.info]Loqucity forum[/url] if you have any questions, comments, bug reports etc.[/p] [p]Happy bBlogging![/p]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 'live', 'bbcode', '', 0, 1);

INSERT INTO `__pfx__sections` (`sectionid`, `nicename`, `name`) VALUES
	(1, 'News', 'news'),
	(2, 'Work', 'work'),
	(3, 'Play', 'play');
