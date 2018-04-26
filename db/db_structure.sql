/*
SQLyog Ultimate v11.28 (64 bit)
MySQL - 5.5.58-0+deb7u1 : Database - c3fpvspots
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`c3fpvspots` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `c3fpvspots`;



/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(50) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `phone` varchar(200) COLLATE utf8_czech_ci DEFAULT NULL,
  `photo_id` int(50) DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `changed_on` datetime NOT NULL,
  `visited_on` datetime NOT NULL,
  `public` smallint(1) unsigned NOT NULL DEFAULT '0',
  `page_facebook` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `page_googleplus` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `page_website` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `page_content` text COLLATE utf8_czech_ci,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `role` varchar(150) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;



/*Table structure for table `buddies` */

DROP TABLE IF EXISTS `buddies`;

CREATE TABLE `buddies` (
  `id` int(50) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(50) unsigned NOT NULL,
  `buddy_user_id` int(50) unsigned NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

/*Data for the table `buddies` */

/*Table structure for table `drones` */

DROP TABLE IF EXISTS `drones`;

CREATE TABLE `drones` (
  `id` int(50) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(50) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `type` int(10) NOT NULL,
  `description` text COLLATE utf8_czech_ci,
  `created_on` datetime NOT NULL,
  `changed_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `drones_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


/*Table structure for table `drones_photos` */

DROP TABLE IF EXISTS `drones_photos`;

CREATE TABLE `drones_photos` (
  `drone_entity_id` int(50) NOT NULL,
  `photo_entity_id` int(50) NOT NULL,
  PRIMARY KEY (`drone_entity_id`,`photo_entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


/*Table structure for table `drones_ratings` */

DROP TABLE IF EXISTS `drones_ratings`;

CREATE TABLE `drones_ratings` (
  `id` int(50) unsigned NOT NULL AUTO_INCREMENT,
  `drone_id` int(50) unsigned NOT NULL,
  `ip_address` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `rate` enum('1','2','3','4','5') COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=171 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


/*Table structure for table `drones_types` */

DROP TABLE IF EXISTS `drones_types`;

CREATE TABLE `drones_types` (
  `id` int(50) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `desc` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


/*Table structure for table `events_types` */

DROP TABLE IF EXISTS `events_types`;

CREATE TABLE `events_types` (
  `id` int(50) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

/*Table structure for table `events` */

DROP TABLE IF EXISTS `events`;

CREATE TABLE `events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `event_type_id` int(10) unsigned NOT NULL,
  `event_date` datetime NOT NULL,
  `map_place` varchar(255) COLLATE utf8_czech_ci NOT NULL DEFAULT '''0;0;0''',
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_czech_ci,
  `is_private` smallint(1) NOT NULL DEFAULT '0',
  `max_users` int(50) NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL,
  `changed_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_type_id` (`event_type_id`),
  CONSTRAINT `events_ibfk_1` FOREIGN KEY (`event_type_id`) REFERENCES `events_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


/*Table structure for table `events_photos` */

DROP TABLE IF EXISTS `events_photos`;

CREATE TABLE `events_photos` (
  `event_entity_id` int(50) NOT NULL,
  `photo_entity_id` int(50) NOT NULL,
  PRIMARY KEY (`event_entity_id`,`photo_entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;



/*Table structure for table `events_users` */

DROP TABLE IF EXISTS `events_users`;

CREATE TABLE `events_users` (
  `event_entity_id` int(50) NOT NULL,
  `user_entity_id` int(50) NOT NULL,
  PRIMARY KEY (`event_entity_id`,`user_entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

/*Data for the table `events_users` */

/*Table structure for table `photos` */

DROP TABLE IF EXISTS `photos`;

CREATE TABLE `photos` (
  `id` int(50) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `filesize` int(50) NOT NULL,
  `mimetype` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `filehash` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


/*Table structure for table `places` */

DROP TABLE IF EXISTS `places`;

CREATE TABLE `places` (
  `id` int(50) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(50) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `description` text COLLATE utf8_czech_ci NOT NULL,
  `plus_desc` varchar(255) COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  `minus_desc` varchar(255) COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  `map_place` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `created_on` datetime NOT NULL,
  `changed_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `places_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=352 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


/*Table structure for table `places_photos` */

DROP TABLE IF EXISTS `places_photos`;

CREATE TABLE `places_photos` (
  `place_entity_id` int(50) NOT NULL,
  `photo_entity_id` int(50) NOT NULL,
  PRIMARY KEY (`place_entity_id`,`photo_entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;




/*Table structure for table `wall_messages` */

DROP TABLE IF EXISTS `wall_messages`;

CREATE TABLE `wall_messages` (
  `id` int(50) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(50) NOT NULL,
  `parent_id` int(50) DEFAULT NULL,
  `message` text COLLATE utf8_czech_ci NOT NULL,
  `created_on` datetime NOT NULL,
  `changed_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


/*Table structure for table `videos` */

DROP TABLE IF EXISTS `videos`;

CREATE TABLE `videos` (
  `id` int(50) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(50) unsigned NOT NULL,
  `link` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `name` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `desc` varchar(250) COLLATE utf8_czech_ci DEFAULT NULL,
  `type` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `created_on` datetime NOT NULL,
  `changed_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
