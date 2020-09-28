CREATE TABLE `social_data_connector_engine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `configuration` longtext COMMENT '(DC2Type:object)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_EA791E1F999517A` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `social_data_feed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `connector` int(11) DEFAULT NULL,
  `configuration` longtext COMMENT '(DC2Type:object)',
  `creation_date` datetime NOT NULL,
  `wall` int(11) DEFAULT NULL,
  `persist_media` tinyint(1) NOT NULL,
  `publish_post_immediately` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AA59D23E148C456E` (`connector`),
  KEY `IDX_AA59D23E13F5EFF6` (`wall`),
  CONSTRAINT `FK_AA59D23E13F5EFF6` FOREIGN KEY (`wall`) REFERENCES `social_data_wall` (`id`),
  CONSTRAINT `FK_AA59D23E148C456E` FOREIGN KEY (`connector`) REFERENCES `social_data_connector_engine` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `social_data_feed_post` (
  `feed_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  PRIMARY KEY (`feed_id`,`post_id`),
  KEY `IDX_ADCA841C51A5BC03` (`feed_id`),
  CONSTRAINT `FK_ADCA841C51A5BC03` FOREIGN KEY (`feed_id`) REFERENCES `social_data_feed` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `social_data_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `connector_engine` int(11) DEFAULT NULL,
  `wall` int(11) DEFAULT NULL,
  `feed` int(11) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_45821D5C13F5EFF6` (`wall`),
  KEY `IDX_45821D5C234044AB` (`feed`),
  KEY `IDX_45821D5C3EEACC32` (`connector_engine`),
  CONSTRAINT `FK_45821D5C13F5EFF6` FOREIGN KEY (`wall`) REFERENCES `social_data_wall` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_45821D5C234044AB` FOREIGN KEY (`feed`) REFERENCES `social_data_feed` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_45821D5C3EEACC32` FOREIGN KEY (`connector_engine`) REFERENCES `social_data_connector_engine` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `social_data_wall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `creation_date` datetime NOT NULL,
  `data_storage` longtext COMMENT '(DC2Type:array)',
  `asset_storage` longtext COMMENT '(DC2Type:array)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9AEC7963999517A` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;