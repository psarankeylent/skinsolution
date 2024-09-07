-- Adminer 4.8.1 MySQL 5.7.40 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE TABLE `medical_question_responses` (
  `response_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `response_type` varchar(45) DEFAULT NULL,
  `label` varchar(500) DEFAULT NULL,
  `placeholder` varchar(250) DEFAULT ' ',
  `response_info` varchar(250) DEFAULT NULL,
  `format` varchar(45) DEFAULT NULL,
  `priority` tinyint(2) DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT '0',
  `is_kickout` tinyint(1) DEFAULT '0',
  `choice_override` tinyint(2) DEFAULT '0',
  `followup_question_id` int(11) NOT NULL,
  `sequence` tinyint(2) DEFAULT NULL,
  `hide_on_load` tinyint(1) unsigned zerofill DEFAULT '0',
  `show_when_selected` tinyint(2) DEFAULT '0',
  `has_dependency` smallint(3) NOT NULL DEFAULT '0',
  `is_flow_end` smallint(3) NOT NULL DEFAULT '0',
  `flow_visibility` varchar(25) DEFAULT 'all',
  `next_followup_question_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`response_id`),
  KEY `fk_medical_question_responses_medical_question_groups1` (`group_id`),
  KEY `fk_medical_question_responses_medical_questions1` (`followup_question_id`),
  CONSTRAINT `fk_medical_question_responses_medical_question_groups1` FOREIGN KEY (`group_id`) REFERENCES `medical_question_groups` (`group_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_medical_question_responses_medical_questions1` FOREIGN KEY (`followup_question_id`) REFERENCES `medical_questions` (`question_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `medical_question_responses` (`response_id`, `group_id`, `response_type`, `label`, `placeholder`, `response_info`, `format`, `priority`, `is_required`, `is_kickout`, `choice_override`, `followup_question_id`, `sequence`, `hide_on_load`, `show_when_selected`, `has_dependency`, `is_flow_end`, `flow_visibility`, `next_followup_question_id`) VALUES
(1,	1,	'text',	'Date of Birth',	'MM/DD/YYYY. (18 years and older)',	NULL,	'date',	1,	1,	0,	0,	2,	1,	0,	NULL,	0,	0,	'all',	NULL),
(2,	1,	'statelist',	'Shipping state',	'',	NULL,	'statelist',	1,	1,	0,	0,	2,	2,	0,	NULL,	0,	0,	'all',	NULL),
(3,	1,	'submit',	'Submit',	NULL,	NULL,	'text',	1,	1,	0,	0,	2,	3,	0,	NULL,	0,	0,	'all',	NULL),
(4,	2,	'radio',	'Not started yet, I\'d like to prevent hair loss',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	4,	1,	0,	NULL,	0,	0,	'all',	NULL),
(5,	2,	'radio',	'In the last month',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	3,	2,	0,	NULL,	0,	0,	'all',	NULL),
(6,	2,	'radio',	'In the last 6 months to a year',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	3,	3,	0,	NULL,	0,	0,	'all',	NULL),
(7,	2,	'radio',	'Over a year ago',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	3,	4,	0,	NULL,	0,	0,	'all',	NULL),
(8,	3,	'radio',	'I have a receding hairline',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	4,	1,	0,	NULL,	0,	0,	'all',	NULL),
(9,	3,	'radio',	'I am getting bald spot on the crown of my head',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	4,	2,	0,	NULL,	0,	0,	'all',	NULL),
(10,	3,	'radio',	'Both hairline and crown',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	4,	3,	0,	NULL,	0,	0,	'all',	NULL),
(11,	3,	'radio',	'It\'s patchy with some odd hair loss all over my scalp',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	4,	4,	0,	NULL,	0,	0,	'all',	NULL),
(12,	4,	'choice',	'Dandruff',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	7,	1,	0,	NULL,	0,	0,	'all',	NULL),
(13,	4,	'choice',	'A sudden increase in hair loss',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	7,	2,	0,	NULL,	0,	0,	'all',	NULL),
(14,	4,	'choice',	'Losing body hair',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	7,	3,	0,	NULL,	0,	0,	'all',	NULL),
(15,	4,	'choice',	'Pain, itching, burning or bumps on the scalp',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	7,	4,	0,	NULL,	0,	0,	'all',	NULL),
(16,	4,	'choice',	'Red rings or other rashes on the scalp',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	7,	5,	0,	NULL,	0,	0,	'all',	NULL),
(17,	4,	'choice',	'Hair loss other than on your head',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	7,	6,	0,	NULL,	0,	0,	'all',	NULL),
(18,	4,	'choice',	'A diagnosis of scalp psoriasis or scalp eczema',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	7,	7,	0,	NULL,	0,	0,	'all',	NULL),
(19,	4,	'choice',	'No, I have not experienced any of these symptoms',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	5,	8,	0,	NULL,	0,	0,	'all',	NULL),
(20,	7,	'textarea',	NULL,	'Please type your answer here. This field is required.',	NULL,	NULL,	NULL,	1,	0,	0,	5,	1,	0,	NULL,	0,	0,	'all',	NULL),
(21,	7,	'combotextadd',	'Please list the medications you are currently taking for this condition.',	'Type each medication and click add',	NULL,	'combotextadd',	NULL,	NULL,	NULL,	0,	5,	2,	0,	NULL,	0,	0,	'all',	NULL),
(22,	7,	'submit',	'SAVE AND CONTINUE',	'',	NULL,	'textarea',	NULL,	NULL,	NULL,	0,	5,	3,	0,	NULL,	0,	0,	'all',	NULL),
(23,	5,	'radio',	'No',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	6,	1,	0,	NULL,	0,	0,	'all',	NULL),
(24,	5,	'radio',	'Yes',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	13,	2,	0,	NULL,	0,	0,	'all',	NULL),
(25,	6,	'choice',	'Kidney disease',	NULL,	NULL,	NULL,	1,	NULL,	0,	0,	14,	1,	0,	NULL,	0,	0,	'all',	NULL),
(26,	6,	'choice',	'Liver disease',	NULL,	NULL,	NULL,	1,	NULL,	NULL,	0,	14,	2,	0,	NULL,	0,	0,	'all',	NULL),
(27,	6,	'choice',	'Thyroid disease',	NULL,	NULL,	NULL,	1,	NULL,	NULL,	0,	14,	3,	0,	NULL,	0,	0,	'all',	NULL),
(28,	6,	'choice',	'Prostate enlargement (e.g., a weak stream, feeling an urgent need to urinate)',	NULL,	NULL,	NULL,	1,	NULL,	0,	0,	14,	4,	0,	NULL,	0,	0,	'all',	NULL),
(29,	6,	'choice',	'Cancer',	NULL,	NULL,	NULL,	9,	NULL,	1,	0,	22,	5,	0,	NULL,	0,	0,	'all',	NULL),
(30,	6,	'choice',	'HIV or any immune disease',	NULL,	NULL,	NULL,	9,	NULL,	1,	0,	22,	6,	0,	NULL,	0,	0,	'all',	NULL),
(31,	6,	'choice',	'Difficult and recurrent yeast or fungal infections',	NULL,	NULL,	NULL,	9,	NULL,	1,	0,	22,	7,	0,	NULL,	0,	0,	'all',	NULL),
(32,	6,	'choice',	'Rheumatological disorders or autoimmune disease (e.g., Lupus, discoid lupus, sarcoidosis, psoriatic arthritis)',	NULL,	NULL,	NULL,	9,	NULL,	1,	0,	22,	8,	0,	NULL,	0,	0,	'all',	NULL),
(33,	6,	'choice',	'Any other medical conditions or a history of prior surgeries',	NULL,	NULL,	NULL,	1,	NULL,	0,	0,	15,	9,	0,	NULL,	0,	0,	'all',	NULL),
(34,	6,	'choice',	'None',	NULL,	NULL,	NULL,	1,	NULL,	0,	1,	8,	10,	0,	NULL,	0,	0,	'all',	NULL),
(35,	16,	'textarea',	'Comments',	'',	NULL,	NULL,	NULL,	1,	0,	0,	16,	2,	0,	NULL,	0,	0,	'all',	NULL),
(36,	16,	'text',	'Email',	'',	NULL,	'email',	NULL,	0,	NULL,	0,	5,	1,	0,	NULL,	0,	0,	'all',	NULL),
(37,	16,	'submit',	'SUBMIT',	'',	NULL,	'',	NULL,	NULL,	1,	0,	22,	3,	0,	NULL,	0,	0,	'all',	NULL),
(38,	14,	'textarea',	NULL,	'Please type your answer here. This field is required.',	NULL,	NULL,	NULL,	1,	0,	0,	8,	1,	0,	NULL,	0,	0,	'all',	NULL),
(39,	14,	'combotextadd',	'Please list the medications you are currently taking for this condition.',	'ADD MEDICATION',	NULL,	'combotextadd',	NULL,	NULL,	NULL,	0,	8,	2,	0,	NULL,	0,	0,	'all',	NULL),
(40,	14,	'submit',	'SAVE AND CONTINUE',	'',	NULL,	'textarea',	NULL,	NULL,	NULL,	0,	8,	3,	0,	NULL,	0,	0,	'all',	NULL),
(41,	15,	'textarea',	NULL,	'Please type your answer here. This field is required.',	NULL,	NULL,	NULL,	1,	0,	0,	8,	1,	0,	NULL,	0,	0,	'all',	NULL),
(42,	15,	'combotextadd',	'Please list the medications you are currently taking for this condition.',	'ADD MEDICATION',	NULL,	'combotextadd',	NULL,	NULL,	NULL,	0,	8,	2,	0,	NULL,	0,	0,	'all',	NULL),
(43,	15,	'submit',	'SAVE AND CONTINUE',	'',	NULL,	'',	NULL,	NULL,	NULL,	0,	8,	3,	0,	NULL,	0,	0,	'all',	NULL),
(44,	8,	'radio',	'I don\'t take any medications',	'',	NULL,	NULL,	NULL,	0,	0,	0,	9,	2,	0,	NULL,	0,	0,	'all',	NULL),
(45,	8,	'combotextadd',	'',	'ADD MEDICATION',	NULL,	'combotextadd',	NULL,	NULL,	NULL,	0,	9,	1,	0,	NULL,	0,	0,	'all',	NULL),
(46,	8,	'submit',	'SAVE AND CONTINUE',	'',	NULL,	'comboradio',	NULL,	NULL,	NULL,	0,	9,	3,	0,	NULL,	0,	0,	'all',	NULL),
(47,	9,	'radio',	'No',	'',	NULL,	NULL,	NULL,	0,	0,	0,	10,	1,	0,	NULL,	0,	0,	'all',	NULL),
(48,	9,	'radio',	'Yes',	'',	NULL,	'',	NULL,	NULL,	NULL,	0,	10,	2,	0,	NULL,	50,	0,	'all',	NULL),
(49,	9,	'submit',	'SAVE AND CONTINUE',	'',	NULL,	'',	NULL,	NULL,	NULL,	0,	10,	4,	0,	NULL,	0,	0,	'all',	NULL),
(50,	9,	'textarea',	'Please list what you are allergic to and the reaction that each allergy causes.',	'',	NULL,	'',	NULL,	1,	NULL,	0,	10,	3,	1,	48,	0,	0,	'all',	NULL),
(51,	10,	'text',	'Physician Name:',	'FirstName LastName',	NULL,	'fullname',	1,	1,	0,	0,	11,	1,	0,	NULL,	0,	0,	'all',	NULL),
(52,	10,	'text',	'Phone Number:',	'(123) 456-7890',	NULL,	'phone',	1,	1,	0,	0,	11,	2,	0,	NULL,	0,	0,	'all',	NULL),
(53,	10,	'radio',	'I don\'t have one',	NULL,	NULL,	'individual',	2,	1,	0,	1,	11,	5,	0,	NULL,	0,	0,	'all',	NULL),
(54,	10,	'text',	'Email',	'',	NULL,	'email',	1,	1,	0,	0,	11,	3,	0,	NULL,	0,	0,	'all',	NULL),
(55,	10,	'submit',	'SAVE AND CONTINUE',	'',	NULL,	'comboradio',	1,	1,	0,	0,	11,	6,	0,	NULL,	0,	0,	'all',	NULL),
(56,	11,	'radio',	'No',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	12,	1,	0,	NULL,	0,	0,	'all',	NULL),
(57,	11,	'radio',	'Yes',	NULL,	NULL,	NULL,	9,	NULL,	1,	0,	22,	2,	0,	NULL,	0,	0,	'all',	NULL),
(59,	12,	'radio',	'No',	'',	NULL,	NULL,	NULL,	0,	0,	0,	17,	1,	0,	NULL,	0,	0,	'all',	NULL),
(60,	12,	'radio',	'Yes',	'',	NULL,	'',	NULL,	NULL,	NULL,	0,	17,	2,	0,	NULL,	0,	0,	'all',	NULL),
(61,	12,	'submit',	'SAVE AND CONTINUE',	'',	NULL,	'',	NULL,	NULL,	NULL,	0,	17,	4,	0,	NULL,	0,	0,	'all',	NULL),
(62,	12,	'textarea',	'',	'',	NULL,	'',	NULL,	1,	NULL,	0,	17,	3,	1,	60,	0,	0,	'all',	NULL),
(66,	13,	'textarea',	NULL,	'Please type your answer here. This field is required.',	NULL,	NULL,	NULL,	1,	0,	0,	6,	1,	0,	NULL,	0,	0,	'all',	NULL),
(67,	13,	'combotextadd',	'Please list the medications you are currently taking for this condition.',	'ADD MEDICATION',	NULL,	'combotextadd',	NULL,	NULL,	NULL,	0,	6,	2,	0,	NULL,	0,	0,	'all',	NULL),
(68,	13,	'submit',	'SAVE AND CONTINUE',	'',	NULL,	'textarea',	NULL,	NULL,	NULL,	0,	6,	3,	0,	NULL,	0,	0,	'all',	NULL),
(69,	17,	'choice',	'&nbsp;',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	17,	1,	0,	NULL,	0,	0,	'all',	NULL),
(70,	17,	'submit',	'I have read and agree to all of the above*',	'',	NULL,	'',	NULL,	NULL,	NULL,	0,	17,	2,	0,	NULL,	0,	0,	'all',	NULL),
(71,	18,	'button',	'Yes',	'',	NULL,	'',	NULL,	NULL,	NULL,	0,	19,	1,	0,	NULL,	0,	0,	'all',	NULL),
(72,	18,	'button',	'No',	'',	NULL,	'',	NULL,	NULL,	NULL,	0,	18,	2,	0,	NULL,	0,	1,	'all',	NULL),
(73,	19,	'textarea',	'',	'',	NULL,	'',	NULL,	1,	NULL,	0,	19,	2,	0,	NULL,	0,	0,	'all',	NULL),
(74,	19,	'submit',	'CONTINUE',	'',	NULL,	'',	NULL,	NULL,	NULL,	0,	19,	2,	0,	NULL,	0,	1,	'all',	NULL),
(75,	21,	'statepicker',	'Select your shipping state',	'',	NULL,	'statelist',	1,	1,	0,	0,	21,	2,	0,	NULL,	0,	0,	'all',	NULL),
(76,	21,	'submit',	'Next',	NULL,	NULL,	'text',	1,	1,	0,	0,	21,	3,	0,	NULL,	0,	0,	'all',	NULL),
(77,	10,	'choice',	'I would like a copy of my treatment record sent to my provider listed above and I authorize release of <a href=\"/media/docs/telehealth-informed-consent.pdf\" target_blank>Protected Health Information</a> (PHI) from Enhance MD',	'',	NULL,	'individual',	1,	1,	0,	0,	11,	4,	0,	NULL,	0,	0,	'all',	NULL),
(78,	900,	'radio',	'Yes, always',	' ',	NULL,	NULL,	NULL,	1,	0,	0,	910,	1,	0,	0,	0,	0,	'all',	NULL),
(79,	900,	'radio',	'Haven\'t thought about it',	' ',	NULL,	NULL,	NULL,	1,	0,	0,	910,	2,	0,	0,	0,	0,	'all',	NULL),
(80,	910,	'radio',	'Yes',	' ',	NULL,	NULL,	NULL,	1,	0,	0,	920,	1,	0,	0,	0,	0,	'all',	930),
(81,	910,	'radio',	'No',	' ',	NULL,	NULL,	NULL,	1,	0,	0,	920,	2,	0,	0,	0,	0,	'all',	930),
(82,	920,	'text',	'Email',	'Email address',	NULL,	'email',	1,	1,	0,	0,	930,	3,	0,	NULL,	0,	0,	'guest',	NULL),
(83,	920,	'text',	'Password',	'Password',	NULL,	'password',	1,	1,	0,	0,	930,	4,	0,	0,	0,	0,	'guest',	NULL),
(84,	920,	'submit',	'Start my visit',	' ',	NULL,	NULL,	NULL,	1,	0,	0,	930,	5,	0,	0,	0,	0,	'guest',	NULL),
(85,	920,	'choice',	'I am 18 years or older and agree to the <a href=\"/terms-of-service\" target=\"_blank\">Terms of Service</a> and <a href=\"/privacy-policy\" target=\"_blank\">Privacy Policy</a>',	' ',	NULL,	'legal',	1,	1,	0,	0,	930,	6,	0,	0,	0,	0,	'guest',	NULL),
(86,	930,	'text',	'Date of Birth',	'MM/DD/YYYY. (18 years and older)',	NULL,	'date',	1,	1,	0,	0,	940,	1,	0,	0,	0,	0,	'all',	NULL),
(87,	930,	'submit',	'Continue',	' ',	NULL,	NULL,	NULL,	1,	0,	0,	940,	2,	0,	0,	0,	0,	'new',	NULL),
(88,	940,	'submit',	'Continue',	NULL,	NULL,	'text',	1,	1,	0,	0,	950,	2,	0,	0,	0,	0,	'all',	NULL),
(89,	940,	'statepicker',	'Select your shipping state',	' ',	NULL,	'statelist',	NULL,	1,	0,	0,	950,	1,	0,	0,	0,	0,	'all',	NULL),
(95,	950,	'radio',	'Male',	' ',	NULL,	'radiobutton',	1,	1,	0,	0,	960,	1,	0,	0,	0,	0,	'all',	NULL),
(96,	950,	'radio',	'Female',	' ',	NULL,	'radiobutton',	1,	1,	0,	0,	960,	2,	0,	0,	0,	0,	'all',	NULL),
(97,	950,	'radio',	'Other',	' ',	NULL,	'radiobutton',	1,	1,	0,	0,	960,	3,	0,	0,	0,	0,	'all',	NULL),
(98,	960,	'radio',	'Male',	' ',	NULL,	'radiobutton',	1,	1,	0,	0,	2,	1,	0,	0,	0,	0,	'all',	NULL),
(99,	960,	'radio',	'Female',	' ',	NULL,	'radiobutton',	1,	1,	1,	1,	99,	2,	0,	0,	0,	1,	'all',	NULL),
(100,	920,	'text',	'First name (legal)',	'Legal first name',	NULL,	'forguestuser',	NULL,	1,	0,	0,	930,	1,	0,	0,	0,	0,	'guest',	NULL),
(101,	920,	'text',	'Last name (legal)',	'Legal last name',	NULL,	'forguestuser',	NULL,	1,	0,	0,	930,	2,	0,	0,	0,	0,	'guest',	NULL);

-- 2023-01-03 05:41:06