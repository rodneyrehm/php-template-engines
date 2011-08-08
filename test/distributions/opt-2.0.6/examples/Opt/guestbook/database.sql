-- The database for the guestbook

CREATE TABLE IF NOT EXISTS `entries` (
  `id` int(11) NOT NULL auto_increment,
  `author` varchar(30) NOT NULL,
  `date` int(11) NOT NULL,
  `website` varchar(100) DEFAULT 'NULL',
  `email` varchar(100) NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;