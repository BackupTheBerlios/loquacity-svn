CREATE TABLE IF NOT EXISTS `__pfx__comments`(
	`commentid` SERIAL PRIMARY KEY,
	`parentid` integer unsigned NOT NULL default '0',
	`postid` integer unsigned NOT NULL default '0',
	`title` varchar(255) NOT NULL default '',
	`type` enum('comment','trackback') NOT NULL default 'comment',
	`posttime` bigint default NULL,
	`postername` varchar(100) NOT NULL default '',
	`posteremail` varchar(100) NOT NULL default '',
	`posterwebsite` varchar(255) NOT NULL default '',
	`posternotify` bit(1) NOT NULL default '0',
	`pubemail` bit(1) NOT NULL default '0',
	`pubwebsite` bit(1) NOT NULL default '0',
	`ip` varchar(16) NOT NULL default '',
	`commenttext` text NOT NULL,
	`deleted` enum('true','false') NOT NULL default 'false',
	`onhold` bit(1) NOT NULL default '0',
	FULLTEXT KEY `commenttext` (`commenttext`)
);

CREATE TABLE IF NOT EXISTS `__pfx__config` (
	`id` SERIAL PRIMARY KEY,
	`name` varchar(50) NOT NULL default '',
	`value` varchar(255) NOT NULL default ''
);


CREATE TABLE IF NOT EXISTS `__pfx__plugins` (
	`id` SERIAL PRIMARY KEY,
	`type` varchar(50) NOT NULL default 'admin',
	`name` varchar(60) NOT NULL default '',
	`ordervalue` decimal(3,2) NOT NULL default '50.00',
	`nicename` varchar(127) NOT NULL default '',
	`description` text NOT NULL,
	`template` varchar(100) NOT NULL default '',
	`help` text NOT NULL,
	`authors` varchar(255) NOT NULL default '',
	`licence` varchar(50) NOT NULL default ''
);

CREATE TABLE IF NOT EXISTS `__pfx__posts` (
	`postid` SERIAL PRIMARY KEY,
	`title` varchar(255) NOT NULL default '',
	`body` text NOT NULL,
	`posttime` bigint NOT NULL default '0',
	`modifytime` bigint NOT NULL default '0',
	`status` enum('live','draft') NOT NULL default 'live',
	`modifier` varchar(30) NOT NULL default '',
	`sections` varchar(255) NOT NULL default '',
	`ownerid` integer NOT NULL default '0',
	`hidefromhome` bit(1) NOT NULL default '0',
	`allowcomments` enum('allow','timed','disallow') NOT NULL default 'allow',
	`autodisabledate` bigint NOT NULL default '0',
	`commentcount` bigint NOT NULL default '0',
	KEY ownerid (ownerid)
);

CREATE TABLE IF NOT EXISTS `__pfx__sections` (
	`sectionid` SERIAL PRIMARY KEY,
	`nicename` varchar(255) NOT NULL default '',
	`name` varchar(60) NOT NULL default ''
);

CREATE TABLE IF NOT EXISTS `__pfx__referers` (
	`visitID` SERIAL PRIMARY KEY,
	`visitTime` timestamp NOT NULL,
	`visitURL` char(250) default NULL,
	`referingURL` char(250) default NULL,
	`baseDomain` char(250) default NULL
);

CREATE TABLE IF NOT EXISTS `__pfx__authors` (
	id SERIAL PRIMARY KEY,
	nickname varchar(20) NOT NULL default '',
	email varchar(100) NOT NULL default '',
	password varchar(40) NOT NULL default '',
	secret_question varchar(60) NOT NULL default  '',
	secret_answer varchar(60) NOT NULL default  '',
	fullname varchar(50) NOT NULL default '',
	url varchar(50) NOT NULL default '',
	icq integer unsigned NOT NULL default '0',
	profession varchar(30) NOT NULL default '',
	likes text NOT NULL,
	dislikes text NOT NULL,
	location varchar(25) NOT NULL default '',
	aboutme text NOT NULL
);

CREATE TABLE IF NOT EXISTS `__pfx__rss` (
	`id` SERIAL PRIMARY KEY,
	`url` text NOT NULL,
	`input_charset` text NOT NULL
);

CREATE TABLE IF NOT EXISTS `__pfx__links` (
	linkid SERIAL PRIMARY KEY,
	nicename varchar(255) NOT NULL,
	url varchar(255) NOT NULL default '',
	category bigint NOT NULL, 
	position integer NOT NULL default '10'
);

CREATE TABLE IF NOT EXISTS `__pfx__categories` (
	categoryid SERIAL PRIMARY KEY,
	name varchar(60) NOT NULL
);


INSERT INTO `__pfx__rss` VALUES (9, '', '');
INSERT INTO `__pfx__rss` VALUES (8, '', '');
INSERT INTO `__pfx__rss` VALUES (7, '', '');
INSERT INTO `__pfx__rss` VALUES (6, '', '');
INSERT INTO `__pfx__rss` VALUES (5, '', '');
INSERT INTO `__pfx__rss` VALUES (4, '', '');
INSERT INTO `__pfx__rss` VALUES (3, '', '');
INSERT INTO `__pfx__rss` VALUES (2, '', '');
INSERT INTO `__pfx__rss` VALUES (1, 'http://www.loquacity.info/rdf.php', 'I88592');

INSERT INTO `__pfx__config` (`name`, `value`) VALUES
	('EMAIL', '__email_address__'),
	('BLOGNAME', '__blog_name__'),
	('TEMPLATE', 'lines'),
	('DB_TEMPLATES', 'false'),
	('DEFAULT_MODIFIER', 'simple'),
	('CHARSET', 'UTF-8'),
	('VERSION', '__loq_version__'),
	('DIRECTION', 'LTR'),
	('DEFAULT_STATUS', 'live'),
	('PING',''),
	('COMMENT_TIME_LIMIT','1'),
	('NOTIFY','false'),
	('BLOG_DESCRIPTION', '__blog_description__'),
	('COMMENT_MODERATION','urlonly'),
	('META_DESCRIPTION','__blog_description__'),
	('META_KEYWORDS','work,life,play,web design'),
	('LAST_MODIFIED', UNIX_TIMESTAMP()),
	('CAPTCHA_ENABLE','true'),
	('CAPTCHA_WIDTH','200'),
	('CAPTCHA_HEIGHT','50'),
	('CAPTCHA_CHARACTERS','5'),
	('CAPTCHA_LINES','70'),
	('CAPTCHA_ENABLE_SHADOWS','false'),
	('CAPTCHA_OWNER_TEXT','false'),
	('CAPTCHA_CHARACTER_SET','A-Z'),
	('CAPTCHA_CASE_INSENSITIVE','false'),
	('CAPTCHA_BACKGROUND',''),
	('CAPTCHA_MIN_FONT','16'),
	('CAPTCHA_MAX_FONT','25'),
	('CAPTCHA_USE_COLOR','false'),
	('CAPTCHA_GRAPHIC_TYPE','jpg');
	
INSERT INTO `__pfx__categories` VALUES (1,'Navigation');
INSERT INTO `__pfx__categories` VALUES (2,'Blogs I read');

INSERT INTO `__pfx__links` VALUES (1,'Home','__blog_url__',1,20);
INSERT INTO `__pfx__links` VALUES (2,'Archives','__blog_url__archives.php',1,30);
INSERT INTO `__pfx__links` VALUES (3,'RSS 2.0 Feed','__blog_url__feed.php',1,40);


INSERT INTO `__pfx__authors` (`nickname`,`password`,`secret_question`,`secret_answer`,`email`,`fullname`) VALUES
('__login_name__',SHA1('__login_password__'),'__secret_question__','__secret_answer__','__email_address__','__author_name__');


INSERT INTO `__pfx__posts` (`postid`, `title`, `body`, `posttime`, `modifytime`, `status`, `modifier`, `sections`, `commentcount`,`ownerid`) VALUES (1, 'First Post', '[b]This is the first post of Loquacity.[/b][p]You may delete this post in the admin section. Make sure you have deleted the install file and changed the admin password.[/p] [p]Be sure to visit the [url=http://forum.loquacity.info]Loqucity forum[/url] if you have any questions, comments, bug reports etc.[/p] [p]Happy blogging![/p]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 'live', 'bbcode', '', 0, 1);

INSERT INTO `__pfx__sections` (`sectionid`, `nicename`, `name`) VALUES
	(1, 'News', 'news'),
	(2, 'Work', 'work'),
	(3, 'Play', 'play');
