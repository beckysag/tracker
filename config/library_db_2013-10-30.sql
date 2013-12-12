# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: localhost (MySQL 5.5.29)
# Database: library_db
# Generation Time: 2013-10-31 00:02:03 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table accessories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `accessories`;

CREATE TABLE `accessories` (
  `acc_id` int(11) NOT NULL AUTO_INCREMENT,
  `acc_item` int(11) NOT NULL,
  `acc_name` varchar(255) NOT NULL,
  `acc_description` varchar(255) DEFAULT NULL,
  `acc_quantity` int(11) DEFAULT NULL,
  PRIMARY KEY (`acc_id`),
  KEY `acc_item` (`acc_item`),
  CONSTRAINT `accessories_ibfk_1` FOREIGN KEY (`acc_item`) REFERENCES `items` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `accessories` WRITE;
/*!40000 ALTER TABLE `accessories` DISABLE KEYS */;

INSERT INTO `accessories` (`acc_id`, `acc_item`, `acc_name`, `acc_description`, `acc_quantity`)
VALUES
	(1,1,'12v power cord',NULL,1),
	(2,1,'external serial port cables',NULL,3),
	(3,1,'SATA power cord',NULL,2),
	(4,1,'SATA data cable',NULL,2);

/*!40000 ALTER TABLE `accessories` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table item_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `item_types`;

CREATE TABLE `item_types` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) NOT NULL,
  `type_description` varchar(255) NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `item_types` WRITE;
/*!40000 ALTER TABLE `item_types` DISABLE KEYS */;

INSERT INTO `item_types` (`type_id`, `type_name`, `type_description`)
VALUES
	(1,'hardware','Computer Hardware'),
	(2,'computer','Computer'),
	(3,'mobile','Smartphone'),
	(4,'book','Book'),
	(5,'game','Game');

/*!40000 ALTER TABLE `item_types` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `items`;

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_code` varchar(255) NOT NULL,
  `item_type` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_description` varchar(255) DEFAULT NULL,
  `item_features` text,
  `item_condition` varchar(255) DEFAULT NULL,
  `item_model` varchar(255) DEFAULT NULL,
  `item_os` varchar(255) DEFAULT NULL,
  `item_pages` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `item_type` (`item_type`),
  CONSTRAINT `items_ibfk_1` FOREIGN KEY (`item_type`) REFERENCES `item_types` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;

INSERT INTO `items` (`item_id`, `item_code`, `item_type`, `item_name`, `item_description`, `item_features`, `item_condition`, `item_model`, `item_os`, `item_pages`)
VALUES
	(1,'AKB2519374',1,'Intel Atom D510 Mini-ITX','industrial control board','2GB of RAM, 32GB CF card for storage','New','AIMB-212D-S6A1E',NULL,NULL),
	(2,'350077-52-323751-3',3,'Samsung Galaxy SIII',NULL,NULL,'Like new',NULL,'Android',NULL);

/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table loans
# ------------------------------------------------------------

DROP TABLE IF EXISTS `loans`;

CREATE TABLE `loans` (
  `loan_id` int(11) NOT NULL AUTO_INCREMENT,
  `loan_item` int(11) NOT NULL,
  `loan_user` bigint(20) NOT NULL,
  `loan_out` date NOT NULL,
  `loan_in` date NOT NULL,
  PRIMARY KEY (`loan_id`),
  KEY `loan_item` (`loan_item`),
  KEY `loan_user` (`loan_user`),
  CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`loan_item`) REFERENCES `items` (`item_id`),
  CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`loan_user`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `user_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s name',
  `user_password_hash` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s password in salted and hashed format',
  `user_email` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s email',
  `user_type` int(11) DEFAULT NULL,
  `user_fname` varchar(255) NOT NULL,
  `user_lname` varchar(255) NOT NULL,
  `user_key` varchar(255) DEFAULT '',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='user data';

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`user_id`, `user_name`, `user_password_hash`, `user_email`, `user_type`, `user_fname`, `user_lname`, `user_key`)
VALUES
	(0,'testuser','password','',2,'','',''),
	(1,'testadmin','password','',1,'','',''),
	(2,'test','$2y$10$hxmonGEFBcknJU/Z08DOhOKkhRIbAqKVuvuS0sBlTA1ZQzN0S8MF2','test@me.com',NULL,'','',''),
	(3,'newuser','$2y$10$bPhRsiEfjqV1CdCN/8Tnfe1j0QJ6gDDSpUqqRyXRpgoGp7.jzXeea','me@me.com',NULL,'','','');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
