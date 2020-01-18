/*
 Navicat Premium Data Transfer

 Source Server         : 虚拟机zzh
 Source Server Type    : MySQL
 Source Server Version : 50728
 Source Host           : 192.168.5.31:3306
 Source Schema         : bananaWechatAdmin

 Target Server Type    : MySQL
 Target Server Version : 50728
 File Encoding         : 65001

 Date: 18/01/2020 19:35:11
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for bwa_admin
-- ----------------------------
DROP TABLE IF EXISTS `bwa_admin`;
CREATE TABLE `bwa_admin`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `role_id` int(11) UNSIGNED NOT NULL COMMENT '用户权限id',
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密码',
  `nickname` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `platform_id_list` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '所属平台的id列表，逗号分隔',
  `game_type_id_list` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '所属游戏类型的id列表，逗号分隔',
  `game_id_list` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '所属游戏的id列表，逗号分隔',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态(-1：禁用，1：正常)',
  `last_login_time` int(11) NOT NULL DEFAULT 0 COMMENT '最后登录时间',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '注册时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for bwa_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `bwa_admin_log`;
CREATE TABLE `bwa_admin_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '用户标识',
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `type` smallint(6) NOT NULL DEFAULT 0 COMMENT '操作类型',
  `request_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '请求数据',
  `ip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'ip地址',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `normal_uid`(`uid`) USING BTREE COMMENT '用户标识二级索引',
  INDEX `normal_username`(`username`) USING BTREE COMMENT '用户名二级索引',
  INDEX `normal_op_type`(`type`) USING BTREE COMMENT '操作类型二级索引'
) ENGINE = InnoDB AUTO_INCREMENT = 366 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for bwa_admin_permission
-- ----------------------------
DROP TABLE IF EXISTS `bwa_admin_permission`;
CREATE TABLE `bwa_admin_permission`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '权限id',
  `title` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '权限标题',
  `uri` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '权限uri',
  `state` tinyint(1) NOT NULL DEFAULT 1 COMMENT '该记录是否有效1：有效、0：无效',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级权限ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 52 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for bwa_admin_role
-- ----------------------------
DROP TABLE IF EXISTS `bwa_admin_role`;
CREATE TABLE `bwa_admin_role`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '角色id',
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '角色名',
  `state` tinyint(1) NOT NULL DEFAULT 1 COMMENT '该记录是否有效1：有效、0：无效',
  `desc` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `type` tinyint(1) NOT NULL COMMENT '1:超级管理员 2:管理者 3:普通客服 4:工单客服',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for bwa_admin_role_permission
-- ----------------------------
DROP TABLE IF EXISTS `bwa_admin_role_permission`;
CREATE TABLE `bwa_admin_role_permission`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `role_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色id',
  `permission_list` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '权限id列表，用\',\'隔开',
  `create_time` int(10) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `role_id`(`role_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for bwa_official_account
-- ----------------------------
DROP TABLE IF EXISTS `bwa_official_account`;
CREATE TABLE `bwa_official_account`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `app_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '公众号appid',
  `app_secret` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '公众号的appsecret',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '推荐公众号名称',
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '说明',
  `img` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '公众号图标',
  `platform_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属平台的id',
  `game_type_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属游戏类型的id',
  `game_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属游戏的id',
  `account_username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `account_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `update_time` int(10) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `create_time` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
