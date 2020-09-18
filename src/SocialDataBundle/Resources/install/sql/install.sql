CREATE TABLE `social_data_connector_engine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `configuration` longtext COMMENT '(DC2Type:object)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_EA791E1F999517A` (`name`)
) DEFAULT CHARACTER SET UTF8MB4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB;

CREATE TABLE `social_data_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `connector` int(11) DEFAULT NULL,
  `object_id` int(11) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_45821D5C148C456E` (`connector`),
  CONSTRAINT `FK_45821D5C148C456E` FOREIGN KEY (`connector`) REFERENCES `social_data_connector_engine` (`id`) ON DELETE CASCADE
) DEFAULT CHARACTER SET UTF8MB4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB;
