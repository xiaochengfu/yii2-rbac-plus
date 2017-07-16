/*
Navicat MySQL Data Transfer

Source Server         : aftdy
Source Server Version : 50717
Source Host           : 121.42.155.115:3306
Source Database       : yii-rbac-plus

Target Server Type    : MYSQL
Target Server Version : 50717
File Encoding         : 65001

Date: 2017-07-16 16:33:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for pa_oauth_assignment
-- ----------------------------
DROP TABLE IF EXISTS `pa_oauth_assignment`;
CREATE TABLE `pa_oauth_assignment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `special_user` tinyint(1) unsigned DEFAULT '2' COMMENT '1>特色用户2>正常用户',
  `table_extend` tinyint(3) unsigned DEFAULT '0' COMMENT '2>seller 3>employee',
  `created_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pa_oauth_item
-- ----------------------------
DROP TABLE IF EXISTS `pa_oauth_item`;
CREATE TABLE `pa_oauth_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `type` tinyint(1) unsigned DEFAULT NULL COMMENT '1>角色2>权限',
  `condition` int(10) DEFAULT NULL,
  `belong` tinyint(1) unsigned DEFAULT NULL COMMENT '1>后台2>前台',
  `rule_name` varchar(64) DEFAULT NULL,
  `description` text,
  `data` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pa_oauth_item_child
-- ----------------------------
DROP TABLE IF EXISTS `pa_oauth_item_child`;
CREATE TABLE `pa_oauth_item_child` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `permission` varchar(64) NOT NULL,
  `special_user` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '1>特殊用户2>正常用户',
  `menu_id` text,
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '扩展条件',
  `condition` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1581 DEFAULT CHARSET=utf8;
