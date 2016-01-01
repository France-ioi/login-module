
CREATE TABLE IF NOT EXISTS `user_badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idUser` int(11) NOT NULL,
  `sBadge` varchar(63) NOT NULL,
  PRIMARY KEY (`id`)
);
CREATE INDEX `ix_user_badges_idUser` USING btree ON `user_badges` (`idUser`);
ALTER TABLE `user_badges` ADD CONSTRAINT `fk_user_badges_idUser`
    FOREIGN KEY `ix_user_badges_idUser` (`idUser`)
    REFERENCES `users`(`id`) ON DELETE CASCADE;

