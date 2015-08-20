
CREATE TABLE IF NOT EXISTS `civicrm_migration_memoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usergroup_id` int(11) NOT NULL,
  `usergroup_name` varchar(255) DEFAULT NULL,
  `usergroup_filter` varchar(255) DEFAULT NULL,
  `status` enum('none','queued','migrating','migrated','readonly') NOT NULL DEFAULT 'notmigrated',
  `added` int(11) DEFAULT NULL,
  `migrated` int(11) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `civicrm_migration_memoria` ADD UNIQUE KEY `usergroup_id` (`usergroup_id`), ADD KEY `status` (`status`);
