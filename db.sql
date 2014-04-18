/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping structure for table auth_sessions
CREATE TABLE IF NOT EXISTS `auth_sessions` (
  `id` varchar(128) NOT NULL DEFAULT '',
  `user_id` int(11) DEFAULT NULL,
  `expiry` datetime NOT NULL,
  `active` tinyint(4) NOT NULL,
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `FK_auth_sessions_auth_users` FOREIGN KEY (`user_id`) REFERENCES `auth_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

# Data exporting was unselected.


# Dumping structure for table auth_users
CREATE TABLE IF NOT EXISTS `auth_users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `repeat` int(11) NOT NULL DEFAULT '1',
  `passwd` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(5120) DEFAULT NULL,
  `phone` varchar(16) DEFAULT NULL,
  `gender` char(1) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `verify` varchar(255) DEFAULT NULL,
  `expiry` datetime DEFAULT NULL,
  `ctime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data` text,
  `profile` varchar(1024) DEFAULT NULL,
  `photo` varchar(1024) DEFAULT NULL,
  `address` varchar(1024) DEFAULT NULL,
  `country` varchar(128) DEFAULT NULL,
  `region` varchar(128) DEFAULT NULL,
  `city` varchar(128) DEFAULT NULL,
  `zip` varchar(16) DEFAULT NULL,
  `google` varchar(512) DEFAULT NULL,
  `facebook` varchar(512) DEFAULT NULL,
  `linkedin` varchar(512) DEFAULT NULL,
  `twitter` varchar(512) DEFAULT NULL,
  `stg_used` int(11) NOT NULL DEFAULT '0',
  `stg_max` int(11) NOT NULL DEFAULT '157286400',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `google` (`google`),
  UNIQUE KEY `facebook` (`facebook`),
  UNIQUE KEY `linkedin` (`linkedin`),
  UNIQUE KEY `twitter` (`twitter`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

# Data exporting was unselected.


# Dumping structure for table data_map
CREATE TABLE `demo_note` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `value` TEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name` (`name`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT

# Data exporting was unselected.
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

