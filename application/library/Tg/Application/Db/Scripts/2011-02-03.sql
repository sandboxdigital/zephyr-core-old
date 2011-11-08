CREATE TABLE `file_folder` (
  `id` int(11) NOT NULL auto_increment,
  `left` int(11) default NULL,
  `right` int(11) default NULL,
  `name` varchar(100) default NULL,
  `title` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=latin1;

CREATE TABLE `file_folder_file` (
  `id` int(11) NOT NULL auto_increment,
  `folder_id` int(11) NOT NULL default '0',
  `file_id` int(11) NOT NULL default '0',
  `name` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;

INSERT INTO `file_folder` (`id`,`left`,`right`,`name`,`title`)
VALUES
	(1, 1, 2, 'root', 'Categories');
