CREATE TABLE IF NOT EXISTS `users` (
`id` int(11) NOT NULL,
  `idUserGodfather` int(11) DEFAULT NULL,
  `sOpenIdIdentity` varchar(255) DEFAULT NULL COMMENT 'User''s Open Id Identity',
  `sLogin` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `sPasswordMd5` varchar(100) NOT NULL,
  `sSalt` varchar(32) NOT NULL,
  `sRecover` varchar(50) NOT NULL,
  `sRegistrationDate` date NOT NULL DEFAULT '0000-00-00',
  `sEmail` varchar(100) NOT NULL,
  `bEmailVerified` tinyint(1) NOT NULL,
  `sFirstName` varchar(100) NOT NULL COMMENT 'User''s first name',
  `sLastName` varchar(100) NOT NULL COMMENT 'User''s last name',
  `sCountryCode` char(3) NOT NULL DEFAULT '',
  `sTimeZone` varchar(100) NOT NULL,
  `sBirthDate` date NOT NULL DEFAULT '0000-00-00' COMMENT 'User''s birth date',
  `iGraduationYear` int(11) NOT NULL DEFAULT '0' COMMENT 'User''s high school graduation year',
  `iPrecision` int(11) NOT NULL DEFAULT '100',
  `iAutonomy` int(11) NOT NULL DEFAULT '100',
  `iPerseverance` int(11) NOT NULL DEFAULT '0',
  `iCharisma` int(11) NOT NULL DEFAULT '0',
  `iActivity` int(11) NOT NULL DEFAULT '0',
  `sLastLoginDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sLastActivityDate` datetime DEFAULT NULL COMMENT 'User''s last activity time on the website',
  `sLastIP` varchar(16) NOT NULL,
  `sSex` enum('Male','Female') DEFAULT NULL,
  `sAddress` mediumtext NOT NULL COMMENT 'User''s address',
  `sZipcode` longtext NOT NULL COMMENT 'User''s postal code',
  `sCity` longtext NOT NULL COMMENT 'User''s city',
  `sLandLineNumber` longtext NOT NULL COMMENT 'User''s phone number',
  `sCellPhoneNumber` longtext NOT NULL COMMENT 'User''s mobil phone number',
  `sDefaultLanguage` char(3) NOT NULL DEFAULT 'fr' COMMENT 'User''s default language',
  `bBasicEditorMode` tinyint(4) NOT NULL DEFAULT '1',
  `iMemberState` tinyint(4) NOT NULL DEFAULT '0',
  `bIsAdmin` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'TODO',
  `bIsTrainer` tinyint(4) NOT NULL DEFAULT '0',
  `bIsTeacher` tinyint(4) NOT NULL DEFAULT '0',
  `bNoRanking` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'TODO',
  `bNotifyNews` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'TODO',
  `iNotify` tinyint(4) NOT NULL,
  `nbSpacesForTab` int(11) NOT NULL DEFAULT '3',
  `nbHelpGiven` int(11) NOT NULL COMMENT 'TODO',
  `bPublicFirstName` tinyint(4) NOT NULL COMMENT 'Publicly show user''s first name',
  `bPublicLastName` tinyint(4) NOT NULL COMMENT 'Publicly show user''s first name',
  `sFreeText` mediumtext NOT NULL,
  `sWebSite` varchar(100) NOT NULL,
  `bPhotoAutoload` tinyint(1) NOT NULL DEFAULT '0',
  `sLangProg` varchar(30) DEFAULT 'Python',
  `iVersion` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE `users`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `nom` (`sLogin`(20)), ADD KEY `synchro` (`iVersion`), ADD KEY `sCountryCode` (`sCountryCode`), ADD KEY `idUserGodfather` (`idUserGodfather`), ADD KEY `sLangProg` (`sLangProg`);

 ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `auths` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `public_key` varchar(500) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `users_auths` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `idUser` bigint(20) NOT NULL,
  `idAuth` bigint(20) NOT NULL,
  `authStr` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;