CREATE TABLE IF NOT EXISTS `civicrm_migration_memoria` (
  `id` int(11) NOT NULL,
  `usergroup_id` int(11) NOT NULL,
  `usergroup_name` varchar(255) DEFAULT NULL,
  `usergroup_filter` varchar(255) DEFAULT NULL,
  `status` enum('notmigrated','queued','migrating','testmigrated','migrated','error') NOT NULL DEFAULT 'notmigrated',
  `migration_type` enum('test','live') DEFAULT NULL,
  `migration_spgeo` int(11) DEFAULT NULL,
  `migration_users` int(1) DEFAULT NULL,
  `added` int(11) DEFAULT NULL,
  `migrated` int(11) DEFAULT NULL,
  `log` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=222 DEFAULT CHARSET=utf8;


ALTER TABLE `civicrm_migration_memoria` ADD UNIQUE KEY `usergroup_id` (`usergroup_id`), ADD KEY `status` (`status`);
