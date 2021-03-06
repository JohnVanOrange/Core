SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `images` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uid` varchar(6) character set utf8 collate utf8_bin NOT NULL,
  `c_link` varchar(256) collate utf8_unicode_ci default NULL,
  `animated` tinyint(1) NOT NULL default '0',
  `display` tinyint(1) NOT NULL default '1',
  `nsfw` tinyint(1) NOT NULL default '0',
  `approved` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `imgur_history` (
  `id` varchar(7) character set utf8 collate utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `report_types` (
  `id` int(3) unsigned NOT NULL auto_increment,
  `value` varchar(32) collate utf8_unicode_ci NOT NULL,
  `public` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

INSERT INTO `report_types` (`id`, `value`, `public`) VALUES
(1, 'NSFW', 1),
(2, 'Copyright Violation', 1),
(3, 'Lame', 1),
(4, 'Illegal Content', 1);

CREATE TABLE IF NOT EXISTS `sessions` (
  `user_id` int(8) unsigned NOT NULL,
  `sid` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `last_access` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  KEY `sid` (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `tags` (
  `tag_id` int(8) unsigned NOT NULL,
  `image_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `tag_image` (`tag_id`,`image_id`),
  KEY `image_id` (`image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `tag_list` (
  `id` int(8) unsigned NOT NULL auto_increment,
  `name` varchar(64) collate utf8_unicode_ci NOT NULL,
  `basename` varchar(64) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `basename` (`basename`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(10) unsigned NOT NULL DEFAULT '1',
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `resources` (
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ip` varchar(15) collate utf8_unicode_ci default NULL,
  `image` varchar(6) character set utf8 collate utf8_bin NOT NULL,
  `user_id` int(8) unsigned default NULL,
  `tag_id` int(8) unsigned DEFAULT NULL,
  `value` int(8) default NULL,
  `type` varchar(8) collate utf8_unicode_ci NOT NULL,
  `public` tinyint(1) NOT NULL default '0',
  `unauth_user` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  KEY `image` (`image`),
  KEY `user_id` (`user_id`),
  KEY `value_type` (`value`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `ads` (
  `id` int(8) NOT NULL auto_increment,
  `title` varchar(2068) collate utf8_unicode_ci NOT NULL,
  `ASIN` varchar(10) collate utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `media` (
  `uid` varchar(6) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `format` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `hash` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `width` smallint(5) unsigned NOT NULL,
  `height` smallint(5) unsigned NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `type` enum('thumb','primary') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'primary',
  `storage` int(3) NOT NULL DEFAULT '1',
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `name` varchar(16) collate utf8_unicode_ci NOT NULL,
  `value` varchar(96) collate utf8_unicode_ci default NULL,
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `settings` (`name`, `value`) VALUES
('web_root', 'http://example.com/'),
('site_name', 'John VanDefault'),
('branch', 'stable'),
('theme', 'jvo'),
('icon_set', 'orange_slice'),
('google_analytics', NULL),
('app_link', NULL),
('show_brazz', '0'),
('show_social', '1'),
('show_jvon', '1'),
('tags_need_auth', '0'),
('disable_upload', '0'),
('fb_app_id', NULL),
('amazon_aff', NULL),
('403_image', '403.jpg'),
('404_image', '404.jpg');

CREATE TABLE IF NOT EXISTS `storage` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `type` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `endpoint` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `access_key` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `secret_key` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `bucket` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

INSERT INTO `storage` (`id`, `type`, `path`) VALUES
(1, 'local', 'media.example.com');
