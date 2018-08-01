ALTER TABLE `project` ADD `blurArticleText` BOOLEAN NULL DEFAULT 0;
ALTER TABLE `project` ADD `textAlign` VARCHAR(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'left';
ALTER TABLE `project` ADD `bannerFile` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

INSERT INTO `section` (`ID`, `section`, `description`, `shortcut`) VALUES (NULL, 'AsideRight', 'Erzeugt eine Navigation am rechten Rand des Layouts, in der Linkelemente vertikal angeordnet werden.', 'i:');
UPDATE `section` SET `section` = 'AsideLeft' WHERE `section`.`section` = 'Aside';

CREATE TABLE `recapo`.`image` ( `ID` INT(11) NOT NULL AUTO_INCREMENT , `userID` INT(11) NOT NULL , `projectID` INT(11) NOT NULL , `flag` ENUM('image') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'image' , `sha1` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL , `extension` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL , `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL , `caption` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL , `targetWidth` INT(3) NOT NULL DEFAULT '50' , `horizontalAlign` VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'left' , PRIMARY KEY (`ID`)) ENGINE = InnoDB;

ALTER TABLE `informationarchitecture` CHANGE `flag` `flag` ENUM('container','item','root','image') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'item';
ALTER TABLE `informationarchitecture` ADD `imageID` INT(11) NULL DEFAULT NULL AFTER `containerID`;
