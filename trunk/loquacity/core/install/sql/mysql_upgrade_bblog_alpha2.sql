ALTER TABLE `__pfx__comments` CONVERT TO CHARACTER SET __charset__;
ALTER TABLE `__pfx__comments` ADD INDEX (`deleted`);
ALTER TABLE `__pfx__comments` ADD INDEX (`parentid`);
ALTER TABLE `__pfx__config` CONVERT TO CHARACTER SET __charset__;
ALTER TABLE `__pfx__plugins` CONVERT TO CHARACTER SET __charset__;
ALTER TABLE `__pfx__posts` CONVERT TO CHARACTER SET __charset__;
ALTER TABLE `__pfx__sections` CONVERT TO CHARACTER SET __charset__;
ALTER TABLE `__pfx__referers` CONVERT TO CHARACTER SET __charset__;
ALTER TABLE `__pfx__authors` CONVERT TO CHARACTER SET __charset__;
ALTER TABLE `__pfx__rss` CONVERT TO CHARACTER SET __charset__;
ALTER TABLE `__pfx__links` CONVERT TO CHARACTER SET __charset__;
ALTER TABLE `__pfx__categories` CONVERT TO CHARACTER SET __charset__;

UPDATE `__pfx__config` SET `value`='__loq_version__' WHERE `name`='VERSION';
UPDATE `__pfx__config` SET `value`='' WHERE `name`='PING';