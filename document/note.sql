/*
 Navicat Premium Data Transfer

 Source Server         : DevServer
 Source Server Type    : MySQL
 Source Server Version : 50722
 Source Host           : localhost:3306
 Source Schema         : note

 Target Server Type    : MySQL
 Target Server Version : 50722
 File Encoding         : 65001

 Date: 01/05/2019 13:41:03
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for wt_admin
-- ----------------------------
DROP TABLE IF EXISTS `wt_admin`;
CREATE TABLE `wt_admin`  (
  `admin_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '管理员编号',
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户名',
  `realname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '真实姓名',
  `password` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `type` tinyint(4) NOT NULL DEFAULT 0 COMMENT '管理员类型（0.普通管理员 1.超级管理员）',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态 0.无效 1.有效',
  `is_del` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否删除 1.已删除 0.未删除',
  `create_time` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`admin_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for wt_admin_group
-- ----------------------------
DROP TABLE IF EXISTS `wt_admin_group`;
CREATE TABLE `wt_admin_group`  (
  `admin_group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '管理员组编号',
  `admin_group_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '管理员组名',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态 0.无效 1.有效',
  `is_del` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否删除 1.已删除 0.未删除',
  PRIMARY KEY (`admin_group_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员组表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for wt_book
-- ----------------------------
DROP TABLE IF EXISTS `wt_book`;
CREATE TABLE `wt_book`  (
  `book_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '书籍编号',
  `book_key` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '书籍标识',
  `book_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '书名',
  `book_subname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '副名',
  `author` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '作者',
  `pic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '图片',
  `read_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '在线阅读路径',
  `is_del` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否删除 1.已删除 0.未删除',
  `create_time` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`book_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '书籍表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for wt_comment
-- ----------------------------
DROP TABLE IF EXISTS `wt_comment`;
CREATE TABLE `wt_comment`  (
  `comment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '评论编号',
  `book_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '书籍编号',
  `realname` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '昵称',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '邮箱',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '评论内容',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态 0.未审核 1.审核通过 -1.审核失败',
  `ip_addr` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT 'IP地址',
  `is_del` tinyint(4) DEFAULT 0 COMMENT '是否删除 1.已删除 0.未删除',
  `create_time` datetime(0) DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`comment_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '评论信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for wt_config
-- ----------------------------
DROP TABLE IF EXISTS `wt_config`;
CREATE TABLE `wt_config`  (
  `config_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '配置编号',
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '配置标题',
  `marker` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '配置标识',
  `config_value` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '配置值',
  `is_del` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否删除 1.已删除 0.未删除',
  `create_time` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`config_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 81 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '系统配置信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for wt_file
-- ----------------------------
DROP TABLE IF EXISTS `wt_file`;
CREATE TABLE `wt_file`  (
  `file_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '附件编号',
  `mark` char(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '文件标识',
  `filename_src` varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '原始文件名',
  `size` bigint(20) UNSIGNED NOT NULL COMMENT '文件大小',
  `mime_type` varchar(160) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '文件Mime类型',
  `local_path` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '服务器存放路径',
  `createtime` datetime(0) DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `is_del` tinyint(3) NOT NULL DEFAULT 0 COMMENT '状态 0.未删除 1.已删除',
  PRIMARY KEY (`file_id`) USING BTREE,
  UNIQUE INDEX `mark`(`mark`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1305 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '文件信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for wt_function
-- ----------------------------
DROP TABLE IF EXISTS `wt_function`;
CREATE TABLE `wt_function`  (
  `function_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '功能编号',
  `module_name` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '模块名称',
  `function_name` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '功能名称',
  `module` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '模块',
  `controller` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '控制器',
  `action` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '动作',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态 1.启用 0.禁用',
  `is_del` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否删除 1.已删除 0.未删除',
  PRIMARY KEY (`function_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '功能信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for wt_info
-- ----------------------------
DROP TABLE IF EXISTS `wt_info`;
CREATE TABLE `wt_info`  (
  `info_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '信息编号',
  `book_id` int(11) NOT NULL COMMENT '书籍编号',
  `type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '类型',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '知识点名称',
  `introduce` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '描述',
  `pic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '图片',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '连接',
  `is_del` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否删除 1.已删除 0.未删除',
  `create_time` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`info_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 25 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '知识点信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for wt_purview_admin_group_function
-- ----------------------------
DROP TABLE IF EXISTS `wt_purview_admin_group_function`;
CREATE TABLE `wt_purview_admin_group_function`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '编号',
  `admin_group_id` int(10) UNSIGNED NOT NULL COMMENT '管理员组编号',
  `function_id` int(10) UNSIGNED NOT NULL COMMENT '功能编号',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态 1.有效 -1.无效',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for wt_relation_admin_group
-- ----------------------------
DROP TABLE IF EXISTS `wt_relation_admin_group`;
CREATE TABLE `wt_relation_admin_group`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '关系编号',
  `admin_id` int(10) UNSIGNED NOT NULL COMMENT '管理员编号',
  `admin_group_id` int(10) UNSIGNED NOT NULL COMMENT '管理员组编号',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态 -1.无效 1.有效',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `admin_id_admin_group_id`(`admin_id`, `admin_group_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
