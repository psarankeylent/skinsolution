-- Adminer 4.8.1 MySQL 5.7.40 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE TABLE `medical_question_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `group_text` varchar(250) DEFAULT NULL,
  `group_subtext` varchar(250) DEFAULT NULL,
  `group_type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`group_id`),
  KEY `fk_medical_question_groups_medical_questions1` (`question_id`),
  CONSTRAINT `fk_medical_question_groups_medical_questions1` FOREIGN KEY (`question_id`) REFERENCES `medical_questions` (`question_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `medical_question_groups` (`group_id`, `question_id`, `group_text`, `group_subtext`, `group_type`) VALUES
(1,	1,	NULL,	NULL,	NULL),
(2,	2,	NULL,	NULL,	NULL),
(3,	3,	NULL,	NULL,	NULL),
(4,	4,	NULL,	NULL,	NULL),
(5,	5,	NULL,	NULL,	NULL),
(6,	6,	NULL,	NULL,	NULL),
(7,	7,	NULL,	NULL,	NULL),
(8,	8,	NULL,	NULL,	NULL),
(9,	9,	NULL,	NULL,	NULL),
(10,	10,	NULL,	NULL,	NULL),
(11,	11,	NULL,	NULL,	NULL),
(12,	12,	NULL,	NULL,	NULL),
(13,	13,	NULL,	NULL,	NULL),
(14,	14,	NULL,	NULL,	NULL),
(15,	15,	NULL,	NULL,	NULL),
(16,	16,	NULL,	NULL,	NULL),
(17,	17,	NULL,	NULL,	NULL),
(18,	18,	NULL,	NULL,	NULL),
(19,	19,	NULL,	NULL,	NULL),
(21,	21,	NULL,	NULL,	NULL),
(22,	22,	NULL,	NULL,	NULL),
(900,	900,	NULL,	NULL,	NULL),
(910,	910,	NULL,	NULL,	NULL),
(920,	920,	'<div class=\"login_register_link\" data-formtype=\"register\">Already have an account? <span id=\"enable_loginform\">Sign In</span></div><style>h4.grphd {\n    margin: 10px 0!important;\n}</style>',	NULL,	NULL),
(930,	930,	NULL,	NULL,	NULL),
(940,	940,	NULL,	NULL,	NULL),
(950,	950,	NULL,	NULL,	NULL),
(960,	960,	NULL,	NULL,	NULL);

-- 2023-01-03 05:40:10
