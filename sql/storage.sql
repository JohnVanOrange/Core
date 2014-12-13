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
(1, 'local', 'media.example.com',);

ALTER TABLE  `media` ADD  `storage` INT( 3 ) NOT NULL DEFAULT  '1';