/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 50553
 Source Host           : localhost:3306
 Source Schema         : zjcsinterface

 Target Server Type    : MySQL
 Target Server Version : 50553
 File Encoding         : 65001

 Date: 10/10/2019 11:08:35
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for how_festival
-- ----------------------------
DROP TABLE IF EXISTS `festival`;
CREATE TABLE `festival`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `year` smallint(4) NOT NULL COMMENT '年',
  `festival` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `addtime` bigint(10) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of how_festival
-- ----------------------------
INSERT INTO `festival` VALUES (1, 2019, '[{\"name\":\"\\u5143\\u65e6\",\"time\":\"2018-12-30\",\"fatalism\":\"3\",\"repair\":[\"2019-12-29\"]},{\"name\":\"\\u6625\\u8282\",\"time\":\"2019-02-04\",\"fatalism\":\"7\",\"repair\":[\"2019-02-02\",\"2019-02-03\"]},{\"name\":\"\\u6e05\\u660e\\u8282\",\"time\":\"2019-04-05\",\"fatalism\":\"3\",\"repair\":[]},{\"name\":\"\\u52b3\\u52a8\\u8282\",\"time\":\"2019-05-01\",\"fatalism\":\"4\",\"repair\":[\"2019-04-28\",\"2019-05-05\"]},{\"name\":\"\\u7aef\\u5348\\u8282\",\"time\":\"2019-06-07\",\"fatalism\":\"3\",\"repair\":[]},{\"name\":\"\\u4e2d\\u79cb\\u8282\",\"time\":\"2019-09-13\",\"fatalism\":\"3\",\"repair\":[]},{\"name\":\"\\u56fd\\u5e86\\u8282\",\"time\":\"2019-10-01\",\"fatalism\":\"7\",\"repair\":[\"2019-09-29\",\"2019-10-12\"]}]', 1558582312);

SET FOREIGN_KEY_CHECKS = 1;
