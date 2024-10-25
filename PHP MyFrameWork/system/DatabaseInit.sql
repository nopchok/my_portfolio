DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) auto_increment NOT NULL,
  `role` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ACTIVE_FLAG` char(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y' COMMENT 'Y=ACTIVE,N=NO ACTIVE,D=DELETE',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- ALTER TABLE `role` ADD UNIQUE `unique_index`(`role`);
INSERT INTO `role` (`role`) VALUES ('Dev');
INSERT INTO `role` (`role`) VALUES ('Admin');
INSERT INTO `role` (`role`) VALUES ('User');



DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) auto_increment NOT NULL,
  `text` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `icon_class` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `sequence` int(11) DEFAULT 0,
  `parent_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ACTIVE_FLAG` char(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y' COMMENT 'Y=ACTIVE,N=NO ACTIVE,D=DELETE',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `menu` (`sequence`,`text`,`icon_class`,`url`,`parent_id`) VALUES (0,'Test',null,'test','');
INSERT INTO `menu` (`sequence`,`text`,`icon_class`,`url`,`parent_id`) VALUES (10,'Dev','fa fa-user-astronaut',null,'');
INSERT INTO `menu` (`sequence`,`text`,`icon_class`,`url`,`parent_id`) VALUES (20,'Menu',null,'dev/menu',2);
INSERT INTO `menu` (`sequence`,`text`,`icon_class`,`url`,`parent_id`) VALUES (30,'Role',null,'dev/role',2);
INSERT INTO `menu` (`sequence`,`text`,`icon_class`,`url`,`parent_id`) VALUES (40,'Admin','fa fa-user-tie',null,'');
INSERT INTO `menu` (`sequence`,`text`,`icon_class`,`url`,`parent_id`) VALUES (50,'User',null,'admin/user',5);


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) auto_increment NOT NULL,
  `user_id` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `role_id` int(11) NOT NULL,
  `ACTIVE_FLAG` char(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y' COMMENT 'Y=ACTIVE,N=NO ACTIVE,D=DELETE',
  PRIMARY KEY (`ID`),
  CHECK(`role_id` > '')
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- ALTER TABLE `user` ADD UNIQUE `unique_index`(`username`);
INSERT INTO user (`user_id`, `username`, `password`, `first_name`, `last_name`, `email`, `role_id`, `active_flag`) VALUES ('USR00000','admin','password','admin','admin','admin@admin.com','1','Y');



DROP TABLE IF EXISTS `permission`;
CREATE TABLE `permission` (
  `id` int(11) auto_increment NOT NULL,
  `menu_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `role_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `permission` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'E=EDIT, V=VIEW, N=NONE',
  `ACTIVE_FLAG` char(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y' COMMENT 'Y=ACTIVE,N=NO ACTIVE,D=DELETE',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- ALTER TABLE `permission` ADD UNIQUE `unique_index`(`menu_id`, `role_id`);
INSERT INTO `permission` (`menu_id`,`role_id`,`permission`) VALUES (1, 1, 'E');
INSERT INTO `permission` (`menu_id`,`role_id`,`permission`) VALUES (2, 1, 'E');
INSERT INTO `permission` (`menu_id`,`role_id`,`permission`) VALUES (3, 1, 'E');
INSERT INTO `permission` (`menu_id`,`role_id`,`permission`) VALUES (4, 1, 'E');
INSERT INTO `permission` (`menu_id`,`role_id`,`permission`) VALUES (5, 1, 'E');
INSERT INTO `permission` (`menu_id`,`role_id`,`permission`) VALUES (6, 1, 'E');
INSERT INTO `permission` (`menu_id`,`role_id`,`permission`) VALUES (1, 2, 'N');
INSERT INTO `permission` (`menu_id`,`role_id`,`permission`) VALUES (2, 2, 'N');
INSERT INTO `permission` (`menu_id`,`role_id`,`permission`) VALUES (3, 2, 'E');
INSERT INTO `permission` (`menu_id`,`role_id`,`permission`) VALUES (4, 2, 'E');
INSERT INTO `permission` (`menu_id`,`role_id`,`permission`) VALUES (5, 2, 'E');
INSERT INTO `permission` (`menu_id`,`role_id`,`permission`) VALUES (6, 2, 'E');

