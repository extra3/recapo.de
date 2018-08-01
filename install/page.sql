/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50616
Source Host           : localhost:3306
Source Database       : bachelorarbeit

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2014-08-20 16:56:46
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for page
-- ----------------------------
DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `index` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` text COLLATE utf8_unicode_ci,
  `route` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of page
-- ----------------------------
INSERT INTO `page` VALUES ('1', 'content', '<p>\r\nVerantwortlich für die Inhalte dieser Seite ist:<br />\r\n</p>\r\n<p>\r\n<div class=\"btn-group\">\r\n  <a class=\"btn btn-primary\" href=\"http://www.cs.uni-paderborn.de/fachgebiete/fg-mci/personen/szwillus.html\">Prof. Dr. Gerd Szwillus</a>\r\n  <a class=\"btn btn-primary\" href=\"http://www.cs.uni-paderborn.de/fachgebiete/fg-mci.html\">FG MCI</a>\r\n  <a class=\"btn btn-primary\" href=\"https://www.cs.uni-paderborn.de/impressum.html\">Institut für Informatik</a>\r\n</div>\r\n</p>\r\n', '/impressum');
INSERT INTO `page` VALUES ('2', 'title', 'Impressum', '/impressum');
INSERT INTO `page` VALUES ('3', 'content', '<p>\r\nDie personenbezogenen Daten die vom Probanden während eines Experiments optional eingegeben werden können und die Experimentergebnisse werden bis auf die ID des Projekts unabhängig von einander gespeichert. Verknüpft werden Die Daten können nur vom Projektersteller und von den Verantwortlichen dieser Seite eingesehen werden. Die Daten werden von den Verantwortlichen dieser Seite nicht an Dritte weitergegeben.\r\n</p>', '/privacy');
INSERT INTO `page` VALUES ('4', 'title', 'Datenschutzerklärung', '/privacy');
INSERT INTO `page` VALUES ('5', 'title', 'Spezifikationen', '/help');
INSERT INTO `page` VALUES ('6', 'content', 'Aufgelistet sind die Spezifikationen für die einzelnen Ex- und Importdateien.\r\n<div class=\"panel panel-default\">\r\n  <div class=\"panel-heading\"><h3> Informationsarchitektur</h3></div>\r\n  <div class=\"panel-body\">\r\n\r\n  </div>\r\n</div>', '/help');
