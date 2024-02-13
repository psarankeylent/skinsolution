-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 18, 2022 at 05:47 AM
-- Server version: 10.3.34-MariaDB-0+deb10u1
-- PHP Version: 7.3.19-1~deb10u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `enhance_mage`
--

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `prescription_id` int(11) NOT NULL,
  `zeelify_prescription_id` int(11) NOT NULL,
  `prescription_flow` varchar(50) NOT NULL,
  `prescription_class` varchar(45) DEFAULT NULL,
  `linked_prescription_ids` varchar(555) DEFAULT NULL,
  `ingredient` varchar(150) DEFAULT NULL,
  `strength` varchar(150) DEFAULT NULL,
  `status` smallint(1) NOT NULL DEFAULT 1,
  `daily` smallint(1) NOT NULL DEFAULT 0,
  `last_udpate_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `flow_url` varchar(100) DEFAULT NULL,
  `ndc` varchar(50) DEFAULT NULL,
  `dosage` varchar(1000) DEFAULT NULL,
  `drug_text` varchar(500) DEFAULT NULL,
  `dosage_data` varchar(50) DEFAULT NULL,
  `price_per_dose` varchar(25) DEFAULT NULL,
  `dosage_intro` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`prescription_id`, `zeelify_prescription_id`, `prescription_flow`, `prescription_class`, `linked_prescription_ids`, `ingredient`, `strength`, `status`, `daily`, `last_udpate_date`, `flow_url`, `ndc`, `dosage`, `drug_text`, `dosage_data`, `price_per_dose`, `dosage_intro`) VALUES
(1, 3, 'Hairloss', 'Finasteride', NULL, NULL, '1mg', 1, 0, '2021-03-12 03:17:53', '/consultation/hairloss', '16729008910', '', '', NULL, NULL, NULL),
(2, 73, 'Erectile Dysfunction', 'Sildenafil', NULL, NULL, '25mg', 1, 0, '2021-03-12 03:19:29', '/consultation/ed', 'sil-50-mg', '', 'Sildenafil (Generic Viagra®)', NULL, '$2 per dose', 'A starting dose of 50mg is the most common for members who are new to the medication. Based on the information you provide, a physician will determine if this dose (or another) is appropriate'),
(3, 77, 'Erectile Dysfunction', 'Sildenafil', NULL, NULL, '50mg', 1, 0, '2021-03-12 03:19:29', '/consultation/ed', 'sil-100-mg', '', 'Sildenafil (Generic Viagra®)', NULL, '$4 per dose', 'A starting dose of 50mg is the most common for members who are new to the medication. Based on the information you provide, a physician will determine if this dose (or another) is appropriate'),
(4, 78, 'Erectile Dysfunction', 'Sildenafil', NULL, NULL, '100mg', 1, 0, '2021-03-12 03:20:55', '/consultation/ed', 'SILD-25', '', 'Sildenafil (Generic Viagra®)', NULL, '$6 per dose', 'A starting dose of 50mg is the most common for members who are new to the medication. Based on the information you provide, a physician will determine if this dose (or another) is appropriate'),
(10, 79, 'Erectile Dysfunction', 'Tadalafil', NULL, NULL, '5mg', 1, 0, '2021-03-12 03:22:01', '/consultation/ed', 'TAD-5mg', '[\"Daily\",\"As-needed\"]', 'Tadalifil (Generic Cialis®)', 'As-needed', '$4 per dose', 'A starting dose of 10mg is the most common for members who are new to the medication. Based on the information you provide, a physician will determine if this dose (or another) is appropriate'),
(11, 81, 'Erectile Dysfunction', 'Tadalafil', NULL, NULL, '10mg', 1, 0, '2021-03-12 03:22:01', '/consultation/ed', 'TAD-10mg', '[\"As-needed\"]', 'Tadalifil (Generic Cialis®)', 'As-needed', '$5 per dose', 'A starting dose of 10mg is the most common for members who are new to the medication. Based on the information you provide, a physician will determine if this dose (or another) is appropriate'),
(12, 82, 'Erectile Dysfunction', 'Tadalafil', NULL, NULL, '20mg', 1, 0, '2021-03-12 03:22:31', '/consultation/ed', 'TAD-20mg', '[\"As-needed\"]', 'Tadalifil (Generic Cialis®)', 'As-needed', '$7.50 per dose', 'A starting dose of 10mg is the most common for members who are new to the medication. Based on the information you provide, a physician will determine if this dose (or another) is appropriate'),
(13, 84, 'Erectile Dysfunction', 'Tadalafil', NULL, NULL, '2.5mg', 1, 0, '2021-03-12 03:20:55', '/consultation/ed', 'TAD-2.5mg', '[\"Daily\",\"As-needed\"]', 'Tadalifil (Generic Cialis®)', 'Daily', '$3 per dose', 'A starting dose of 10mg is the most common for members who are new to the medication. Based on the information you provide, a physician will determine if this dose (or another) is appropriate'),
(14, 75, 'Erectile Dysfunction', 'Tadalafil', NULL, NULL, '5mg', 1, 0, '2021-03-12 03:22:01', '/consultation/ed', 'TAD-5mg-daily', '[\"Daily\",\"As-needed\"]', 'Tadalifil (Generic Cialis®)', 'Daily', '$4 per dose', 'A starting dose of 10mg is the most common for members who are new to the medication. Based on the information you provide, a physician will determine if this dose (or another) is appropriate'),
(81, 83, 'ED Consultation', 'Consultation Only', NULL, NULL, NULL, 1, 0, '2022-02-04 06:38:10', '/consultation/ed', 'ED-CONSULT', ' ', ' ', NULL, NULL, NULL),
(83, 85, 'Hairloss', 'Consultation Only', NULL, NULL, NULL, 1, 0, '2021-03-12 03:17:53', '/consultation/hairloss', 'HG-CONSULT', '', '', NULL, NULL, NULL),
(88, 88, 'Hairloss', 'Premium Topical Serum', NULL, 'Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%)', 'Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%)', 1, 0, '2022-04-07 04:57:06', NULL, 'HG-SERUM-MTL', NULL, NULL, NULL, NULL, NULL),
(89, 89, 'Hairloss', 'Premium Topical Serum', '88', 'Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%), Fluocinonide (0.1%)', 'Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%), Fluocinonide (0.1%)', 1, 0, '2022-04-07 04:57:12', NULL, 'HG-SERUM-MTLF', NULL, NULL, NULL, NULL, NULL),
(90, 90, 'Hairloss', 'Premium Topical Serum', '88', 'Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%), Ketoconazole (2%)', 'Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%), Ketoconazole (2%)', 1, 0, '2022-04-07 04:57:14', NULL, 'HG-SERUM-MTLK', NULL, NULL, NULL, NULL, NULL),
(91, 91, 'Hairloss', 'Premium Topical Serum', '88,89,90', 'Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%), Fluocinonide (0.1%), Ketoconazole (2%)', 'Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%), Fluocinonide (0.1%), Ketoconazole (2%)', 1, 0, '2022-04-07 04:57:16', NULL, 'HG-SERUM-MTLFK', NULL, NULL, NULL, NULL, NULL),
(92, 92, 'Hairloss', 'Finasteride & Premium Topical Serum', '1,88', 'Finasteride 1mg, Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%)', 'Finasteride 1mg, Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%)', 1, 0, '2022-04-07 04:57:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(93, 93, 'Hairloss', 'Finasteride & Premium Topical Serum', '1,88,92', 'Finasteride 1mg, Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%), Fluocinonide (0.1%) ', 'Finasteride 1mg, Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%), Fluocinonide (0.1%) ', 1, 0, '2022-04-07 04:57:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(94, 94, 'Hairloss', 'Finasteride & Premium Topical Serum', '1,88,92', 'Finasteride 1mg, Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%), Ketoconazole (2%)', 'Finasteride 1mg, Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%), Ketoconazole (2%)', 1, 0, '2022-04-07 04:57:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(95, 95, 'Hairloss', 'Finasteride & Premium Topical Serum', '1,88,92,93,94', 'Finasteride 1mg, Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%), Fluocinonide (0.1%), Ketoconazole (2%) ', 'Finasteride 1mg, Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%), Fluocinonide (0.1%), Ketoconazole (2%) ', 1, 0, '2022-04-07 04:57:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(96, 96, 'Hairloss', 'Advanced Topical Serum', '88,89,99', 'Finasteride (0.1%), Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%), Fluocinonide (0.1%) ', 'Finasteride (0.1%), Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%), Fluocinonide (0.1%) ', 1, 0, '2022-04-07 04:57:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(97, 97, 'Hairloss', 'Advanced Topical Serum', '88,90,99', 'Finasteride (0.1%), Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%), Ketoconazole (2%)', 'Finasteride (0.1%), Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%), Ketoconazole (2%)', 1, 0, '2022-04-07 04:57:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(98, 98, 'Hairloss', 'Advanced Topical Serum', '88,89,90,99', 'Finasteride (0.1%), Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%), Fluocinonide (0.1%), Ketoconazole (2%) ', 'Finasteride (0.1%), Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%), Fluocinonide (0.1%), Ketoconazole (2%) ', 1, 0, '2022-04-07 04:57:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(99, 99, 'Hairloss', 'Advanced Topical Serum', '', 'Finasteride (0.1%), Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%)', 'Finasteride (0.1%), Minoxidil(6%), Tretinoin(0.025%), Latanaprost(0.1%)', 1, 0, '2022-04-07 04:57:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`prescription_id`),
  ADD KEY `idx_prescriptions_zeelify_prescription_id` (`zeelify_prescription_id`),
  ADD KEY `idx_prescriptions_prescription_class` (`prescription_class`),
  ADD KEY `idx_prescriptions_prescription_class_ingredient_strength` (`prescription_class`,`ingredient`,`strength`),
  ADD KEY `idx_prescriptions_status` (`status`),
  ADD KEY `idx_prescriptions_ndc` (`ndc`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `prescription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
