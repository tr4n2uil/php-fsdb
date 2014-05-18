/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


# Dumping structure for table auth_users
CREATE TABLE `auth_users` (
  `id` BIGINT(10) NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(40) NOT NULL,
  `type` INT(11) NOT NULL DEFAULT '0',
  `username` VARCHAR(255) NOT NULL,
  `repeat` INT(11) NOT NULL DEFAULT '1',
  `passwd` VARCHAR(255) NULL DEFAULT NULL,
  `email` VARCHAR(255) NULL DEFAULT NULL,
  `name` VARCHAR(255) NULL DEFAULT NULL,
  `title` VARCHAR(5120) NULL DEFAULT NULL,
  `phone` VARCHAR(16) NULL DEFAULT NULL,
  `gender` CHAR(1) NULL DEFAULT NULL,
  `dob` DATE NULL DEFAULT NULL,
  `verify` VARCHAR(255) NULL DEFAULT NULL,
  `expiry` DATETIME NULL DEFAULT NULL,
  `ctime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data` TEXT NULL,
  `profile` VARCHAR(1024) NULL DEFAULT NULL,
  `photo` VARCHAR(1024) NULL DEFAULT NULL,
  `address` VARCHAR(1024) NULL DEFAULT NULL,
  `country` VARCHAR(128) NULL DEFAULT NULL,
  `region` VARCHAR(128) NULL DEFAULT NULL,
  `city` VARCHAR(128) NULL DEFAULT NULL,
  `zip` VARCHAR(16) NULL DEFAULT NULL,
  `google` VARCHAR(512) NULL DEFAULT NULL,
  `facebook` VARCHAR(512) NULL DEFAULT NULL,
  `linkedin` VARCHAR(512) NULL DEFAULT NULL,
  `twitter` VARCHAR(512) NULL DEFAULT NULL,
  `stg_used` INT(11) NOT NULL DEFAULT '0',
  `stg_max` INT(11) NOT NULL DEFAULT '157286400',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `username` (`username`),
  UNIQUE INDEX `uuid` (`uuid`),
  UNIQUE INDEX `email` (`email`),
  UNIQUE INDEX `google` (`google`),
  UNIQUE INDEX `facebook` (`facebook`),
  UNIQUE INDEX `linkedin` (`linkedin`),
  UNIQUE INDEX `twitter` (`twitter`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT;
# Data exporting was unselected.


# Dumping structure for table auth_sessions
CREATE TABLE `auth_sessions` (
  `id` VARCHAR(128) NOT NULL DEFAULT '',
  `user_id` BIGINT(11) NULL DEFAULT NULL,
  `expiry` DATETIME NOT NULL,
  `active` TINYINT(4) NOT NULL,
  `ctime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `user_id` (`user_id`),
  CONSTRAINT `FK_auth_sessions_auth_users` FOREIGN KEY (`user_id`) REFERENCES `auth_users` (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT;
# Data exporting was unselected.


# Dumping structure for table data_map
CREATE TABLE `data_map` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(255) NOT NULL,
  `value` TEXT NULL,
  `owner` BIGINT(20) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `key` (`key`),
  INDEX `owner` (`owner`),
  CONSTRAINT `FK_data_map_auth_users` FOREIGN KEY (`owner`) REFERENCES `auth_users` (`id`) ON UPDATE SET NULL ON DELETE SET NULL
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT;

# Data exporting was unselected.
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

