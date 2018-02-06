/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : suteng

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-02-06 15:18:41
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for st_repair
-- ----------------------------
DROP TABLE IF EXISTS `st_repair`;
CREATE TABLE `st_repair` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `truename` char(11) NOT NULL COMMENT '保修人',
  `phone` char(12) NOT NULL COMMENT '报修人电话',
  `reason` varchar(255) DEFAULT NULL COMMENT '保修原因',
  `device_code` varchar(255) NOT NULL COMMENT '设备编码',
  `content` text NOT NULL COMMENT '问题描述',
  `picpath` varchar(255) NOT NULL COMMENT '图片路径',
  `uid` int(11) NOT NULL COMMENT '报修人ID 用户ID',
  `address` varchar(255) NOT NULL COMMENT '维修地址',
  `date` varchar(255) NOT NULL,
  `addtime` int(11) NOT NULL COMMENT '报修时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态0：未处理 1：已处理',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
