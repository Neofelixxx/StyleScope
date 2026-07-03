-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2026 at 06:09 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gr08`
--

-- --------------------------------------------------------

--
-- Table structure for table `cbr_analysis`
--

CREATE TABLE `cbr_analysis` (
  `cbr_id` int(11) NOT NULL,
  `consistency_score` decimal(5,2) DEFAULT NULL,
  `formality_result` varchar(10) DEFAULT NULL,
  `analysis_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `item_id` int(11) NOT NULL,
  `matric_no` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `search_log`
--

CREATE TABLE `search_log` (
  `log_id` int(11) NOT NULL,
  `matric_no` varchar(20) DEFAULT NULL,
  `search_type` varchar(10) DEFAULT NULL,
  `search_query` varchar(255) DEFAULT NULL,
  `results_count` int(11) DEFAULT 0,
  `search_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `matric_no` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `group_no` varchar(10) NOT NULL,
  `photoStu` varchar(255) NOT NULL,
  `photoStu_date` date NOT NULL,
  `outfit_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`matric_no`, `full_name`, `group_no`, `photoStu`, `photoStu_date`, `outfit_description`) VALUES
('B0231241', 'Wadi', 'GR01', 'uploads/B0231241/B0231241_20260518_184121_Gemini_Generated_Image_yeej1vyeej1vyeej.jpeg', '0000-00-00', NULL),
('B032220052', 'MIYA AOYON', 'GW-04', 'uploads/B032220052/p_B032220052_1.jpeg', '2026-05-04', NULL),
('B032220063', 'JANNATUL FERDOUSI NAHIN', 'GW03', 'uploads/1779238439_PHOTO CAMERON.jpeg', '2026-02-16', NULL),
('B032310055', 'AHMAD NAZRAN BIN SHAWALUDDIN', 'GR01', 'uploads/1779247428_WhatsApp Image 2026-05-20 at 11.14.06 AM.jpeg', '2026-05-20', NULL),
('B032310080', 'NUR ASYIQIN BINTI ABDULLAH', 'GR07', 'uploads/1779246670_cikin.jpg', '2026-05-16', NULL),
('B032310134', 'NUR WAHIDA BINTI NORIZIADY', 'GR06', 'uploads/B032310134/p_B032310134_1.jpg', '2025-12-25', 'black dress'),
('B032310153', 'Umar Ashraffi bin Adnan', 'GW09', 'uploads/B032310153/p_B032310153_1.png', '2026-06-12', NULL),
('B032310177', 'AHMAD KHURRAIZY BIN KHUZAINOL', 'G1S2', 'uploads/1779247722_WhatsApp Image 2026-05-20 at 11.24.55 AM.jpeg', '2022-06-11', NULL),
('B032310193', 'SHARIFAH YASMIN BINTI SYD KHALIL', 'GK02', 'uploads/B032310193/p_B032310193_1.jpeg', '2026-04-11', NULL),
('B032310211', 'Fatin Nur Faqihah Bt Md Radzi', 'GK02', 'uploads/B032310211/p_B032310211_1.JPG', '2026-04-10', NULL),
('B032310246', 'FARAH AQILAH BINTI MOHD YANI ', 'GR03', 'uploads/1779247041_photo_2026-05-20_11-12-10.jpg', '2025-12-19', NULL),
('B032310253', 'MUHAMMAD HALAL BIN ACHIM', 'GR04', 'uploads/1779247512_11.jpg', '2026-04-01', NULL),
('B032310305', 'HO SIN RUO', 'GR01', 'uploads/1779238176_Photo.jpg', '2026-05-09', NULL),
('B032310308', 'TAY FUI POH', 'S1G2', 'uploads/1779247152_Photo.jpg', '2025-01-29', NULL),
('B032310326', 'IRFAN HAZIQ BIN ROSIDI', '3BITDS1G1', 'uploads/1779238204_WhatsApp Image 2026-05-20 at 8.45.08 AM.jpeg', '2026-02-24', NULL),
('B032310345', 'NADIA AMANI BINTI ZANON', 'GR08', 'uploads/1779247257_IMG_9923.jpeg', '2026-04-05', NULL),
('B032310348', 'NUR AINA BINTI FAKHRUDDIN', 'GW06', 'uploads/1779238338_6082461663475994309.jpg', '2025-10-17', NULL),
('B032310358', 'NUR HANNAH FATINI BINTI MOHD AZAHAR', 'GR07', 'uploads/B032310358/B032310358_20260520_062239_IMG_9916.jpeg', '2026-02-17', NULL),
('B032310374', 'NUR ELIZA BINTI ANTHONY', 'GR01', 'uploads/B032310374/B032310374_20260611_054653_matric card.jpg', '2026-04-09', NULL),
('B032310381', 'NUR SYARMIMI ALIA HUSNA BINTI ZAIPOLBAHARI', 'GR05', 'uploads/B032310381/p_B032310381_1.jpg', '2026-04-18', NULL),
('B032310390', 'TOH SHUAI TING', 'GW07', 'uploads/1779254816_DSC_0218_Cropped.jpg', '2020-02-10', NULL),
('B032310418', 'IRFAH NADIAH BINTI HAMDAN', 'S1G1', 'uploads/1779238370_Irfah Nadiah binti Hamdan.jpg', '2026-03-22', NULL),
('B032310424', 'SITI NURATIQAH BINTI ABU BAKAR', 'GR08', 'uploads/1779272491_64262.jpg', '2026-03-21', NULL),
('B032310465', 'EILYA FILZAH PUTRI BINTI ABDULLAH', 'GR01', 'uploads/B032310465/B032310465_20260520_025021_photo_6082285234809409561_y.jpg', '2026-03-21', NULL),
('B032310479', 'NURZAFIRAH ANIS BINTI MOHD ZAINI', 'GR06', 'uploads/1779251305_WhatsApp Image 2026-05-20 at 11.49.52 AM.jpeg', '2026-05-20', NULL),
('B032310496', 'POK WAI YAN', 'GW08', 'uploads/1779238100_personal_imagejpg.jpg', '2025-12-25', 'black shirt'),
('B032310499', 'WARDINA SAFEERA BINTI IBRAHIM', 'GR09', 'uploads/1779248172_5d2090ff-0322-401d-9962-d91bff7bf8ba.jpeg', '2026-04-05', NULL),
('B032310509', 'AINNUR ATHIRAH BINTI ROSLI', 'GW01', 'uploads/1779237889_FullSizeRender.jpeg', '2026-03-21', NULL),
('B032310514', 'MARSYA KAMILIA BINTI YUSRIZAL', 'GR02', 'uploads/1779275687_WhatsApp Image 2026-05-20 at 6.50.06 PM.jpeg', '2026-05-20', NULL),
('B032310515', 'CHENG KAH HOOI', 'GW01', 'uploads/1779238720_photo_2026-04-03_08-45-48.jpg', '2026-04-03', 'white casual shirt'),
('B032310529', 'WONG ZHI WEI', 'GR01', 'uploads/1779246899_19799e75-4bb3-4c8c-8c39-bb7721febf42.jpeg', '2026-03-30', NULL),
('B032310540', 'NUR BATRISYIA BALQIS BINTI MOHD FERDAUS', 'S1G1', 'uploads/1779238283_IMG_0719.jpeg', '2023-09-18', NULL),
('B032310571', 'AZRI NURUL QAISARA BINTI AZMAN', 'GR01', 'uploads/1779247331_IMG_0084.jpeg', '2026-04-21', NULL),
('B032310587', 'IZZATUL WAHIDAH BT AMIR', 'GW02', 'uploads/1779238601_DSC05727.jpeg', '2026-02-24', NULL),
('B032310592', 'TAN WEI PIN', 'GR07', 'uploads/B032310592/B032310592_20260520_055302_Screenshot 2024-05-09 141645.png', '2026-05-20', NULL),
('B032310638', 'MUHAMMAD ARIFUDDIN BIN AZMAN', 'GS04', 'uploads/1779256674_WhatsApp Image 2026-04-30 at 14.53.54.jpeg', '2024-05-20', NULL),
('B032310639', 'AMNA NAJWA BINTI ALIAS', 'GR02', 'uploads/1779249254_IMG20260404224147.jpg', '2026-04-11', NULL),
('B032310641', 'MIZA BINTI MOHAMAD RADZI', 'GW04', 'uploads/B032310641/p_B032310641_1.jpeg', '2026-05-22', NULL),
('B032310648', 'AMEERAH MAISARAH BINTI ROSZAINI', 'GR04', 'uploads/1779250207_BA63B6BA-CAF0-412F-97F6-F2A99F427238.png', '2026-05-15', NULL),
('B032310653', 'WAN NUR ADLIN SYAUQINA BINTI WAN AHMAD FADILLAH', 'GW07', 'uploads/1779241762_B032310653_.jpeg', '2026-03-24', 'purple blouse'),
('B032310655', 'ANIQ AFIFI BIN SARLI', 'GW01', 'uploads/1779238486_IMG_0182.jpeg', '2026-03-23', NULL),
('B032310661', 'Muhammad Syameel Amni Bin Mohd Saiful Amri', 'GW06', 'uploads/1779238469_WhatsApp Image 2026-05-20 at 8.47.26 AM.jpeg', '2026-05-20', NULL),
('B032310664', 'LEGASHEENEE JAGATHISAN', 'GR05', 'uploads/1779248332_photo_2026-05-20_11-09-33.jpg', '2025-12-29', NULL),
('B032310674', 'NUR IZZATI BINTI ZAIDI', 'GW07', 'uploads/1779238111_zati5.jpg', '2024-06-19', NULL),
('B032310712', 'NUR ANIZA BINTI MOHD YUSOF', 'GR02', 'uploads/1779247634_gambarrr.png', '2024-05-07', NULL),
('B032310715', 'NUR FASIHAH BINTI JUHARI', 'S1G1', 'uploads/1779238558_IMG_0768.jpeg', '2026-01-14', NULL),
('b032310716', 'Adam Dzahir', 'BITD S1G2', 'uploads/b032310716/p_b032310716_1.jpg', '2026-05-18', NULL),
('B032310735', 'NURUL IZZATI NADHIRAH BINTI ISKANDAR FAIDZAL', 'GW07', 'uploads/1779238924_photo_2026-05-20_08-52-53.jpg', '2026-05-06', NULL),
('B032310739', 'Nik Arlina binti Nik Abdul Rahman', 'GS09', 'uploads/B032310739/p_B032310739_1.jpeg', '2026-06-10', NULL),
('B032310742', 'MUHAMMAD MUHAIMIN AIMAN BIN MOHD ROSLI', 'GW05', 'uploads/1779238670_WhatsApp Image 2026-05-20 at 08.48.15.jpeg', '2026-05-20', 'black t-shirt'),
('B032310833', 'PRIYADASHWINI A/P YOHESWARAN', 'GR01', 'uploads/1779238619_photo_2026-05-20_08-44-48.jpg', '2025-02-01', NULL),
('B032310838', 'SITI AISYAH ALLYSA BINTI MOHD NAZRI', 'GR08', 'uploads/1779239587_Gambar saya.jpg', '2024-12-20', NULL),
('B032310855', 'NUR SHAFIQAH BINTI SHARIP', 'GW02', 'uploads/1779238372_photo_2026-05-20_08-57-11.jpg', '2026-05-20', NULL),
('B032310858', 'IRDINA SYAFIAH BINTI NORAZMAN', 'GW09', 'uploads/1779238632_Personal Picture.jpeg', '2026-02-10', NULL),
('B032320103', 'Imran bin Azlan', 'GR03', 'uploads/1779247137_FF1DFEA8-DF19-4BBB-B1D5-765525AB3EFF.jpeg', '2026-04-01', NULL),
('B032410001', 'ABDUL MALIK BIN MUSTAPHA', 'GK01', 'uploads/B032410001/p_B032410001_1.jpeg', '2026-12-18', 'red blouse'),
('B032410002', 'Muhammad Haikal Bin Mahadzir', 'GK01', 'uploads/B032410002/p_B032410002_1.jpeg', '2026-05-15', 'wearing green t-shirt'),
('B032410003', 'KHAIRUL AMRI BIN SHAMSUL ANUAR', 'GR04', 'uploads/B032410003/B032410003_20260520_052608_Photo.jpeg', '2025-03-31', NULL),
('B032410176', 'NURUL AIN NASUHA BINTI REDUAN', 'GS04', 'uploads/B032410176/p_B032410176_1.jpg', '2026-06-10', NULL),
('B032410181', 'NUR SHAZLEEN AZIEM BINTI MAT TAN SALLEH', 'GW08', 'uploads/1779238408_6082288039423054003.jpg', '2026-05-20', NULL),
('B032410182', 'Nur Anis Hazwani binti Abdul Halim', 'GS03', 'uploads/B032410182/p_B032410182_1.jpg', '2026-06-09', NULL),
('B032410183', 'AIN SURIANI BINTI ZULKEFLI', 'GR01', 'uploads/B032410183/B032410183_20260610_115658_AIN.jpeg', '2026-05-05', 'black dress'),
('B032410184', 'KHAIRUL WAJIHAH BINTI KHAIRUDDIN', 'GW04', 'uploads/1779239082_WhatsApp Image 2026-05-20 at 8.40.42 AM.jpeg', '2026-05-20', NULL),
('B032410185', 'MUAMMAD AIDIL AMANI BIN ABDUL RAHMAN', 'GK01', 'uploads/B032410185/B032410185_20260521_054832_gambo.jpeg', '2026-01-17', 'black formal shirt'),
('b032410186', 'ADAM BIN AZMI', 'GK01', 'uploads/b032410186/p_b032410186_1.jpeg', '2025-07-20', NULL),
('B032410187', 'MUHAMMAD NUR AZAM BIN MOHD FUAD', 'GR01', 'uploads/B032410187/B032410187_20260519_025305_gambar(2).jpg', '2026-05-19', NULL),
('B032410188', 'IQMA AQILAH BINTI ABDUL RAHMAN', 'GS03', 'uploads/B032410188/p_B032410188_1.jpeg', '2026-05-19', NULL),
('B032410189', 'AZRA NATALIA BINTI ABDULLAH', 'GR03', 'uploads/1779247943_IMG_2627.jpeg', '2026-03-29', NULL),
('B032410191', 'VAANISHAH A/P SANTHYRESAN', 'GR01', 'uploads/B032410191/p_B032410191_1.jpeg', '2026-06-03', NULL),
('B032410192', 'BRITNEY NGIENG FANG YII', 'GS01', 'uploads/1779152763_2.jpg', '2023-08-11', NULL),
('B032410195', 'NUR INSYIRAH BINTI EDIE AMER', 'GR01', 'uploads/1779152151_SYIRA.jpeg', '2026-05-19', NULL),
('B032410196', 'NIK NURLYANA SYAKINAH BINTI NIK NORAZAHARI', 'GW05', 'uploads/1779238567_6082389357701566082.jpg', '2026-05-20', NULL),
('B032410197', 'SITI MAISARAH BINTI ADZMI', 'GR08', 'uploads/B032410197/p_B032410197_1.jpeg', '2025-12-18', NULL),
('B032410200', 'Muhammad Rukaini Aidil', 'GK01', 'uploads/B032410200/B032410200_20260521_055841_IMG_1406.png', '2026-02-09', NULL),
('B032410202', 'MUHAMMAD AFIQ HAZIM BIN ABD AZIZ', 'GS01', 'uploads/1779151965_WhatsApp Image 2026-05-19 at 08.40.28.jpeg', '2026-05-19', NULL),
('B032410811', 'Kaviarasan A/L Rajeanthiran', 'GS05', 'uploads/1779238784_Kavi.jpeg', '2026-05-20', 'blue shirt with blank pant'),
('B032410813', 'NURMAISARAH BINTI MOHD NOR', 'GW08', 'uploads/1779238641_photo_2026-05-20_08-45-45.jpg', '2026-05-20', NULL),
('B032410815', 'HUDA NAJIHAH BINTI SUHAIMI', 'GS02', 'uploads/1779151815_PHOTO_B032410815_HUDA NAJIHAH BINTI SUHAIMI.jpeg', '2026-04-13', NULL),
('B032410816', 'SUFIANA ADLIN BINTI BAHAROM', 'GR09', 'uploads/1779239437_DJI_20260214123312_0334_D.JPG', '2026-02-14', 'wearing black shirt'),
('B032410817', 'NUR AINA MAISARA BINTI ASRI', 'GR01', 'uploads/B032410817/p_B032410817_1.jpg', '2026-05-21', NULL),
('B032410818', 'MUHAMMAD HAMDI BIN HASNIM', 'GR01', 'uploads/B032410818/B032410818_20260520_220059_WhatsApp Image 2026-05-21 at 3.58.29 AM.jpeg', '2026-05-20', NULL),
('B032410970', 'MUHAMMAD ASY-SYAKUR DANIEL BIN SUHAIMI', 'GR02', 'uploads/1779247425_WhatsApp Image 2026-05-20 at 11.11.28.jpeg', '2026-05-20', NULL),
('B032420034', 'FARAH DAMIA BINTI MOHAMAD NIZAN', 'GR03', 'uploads/1779248025_photo_2026-05-20_11-30-32.jpg', '2020-07-29', NULL),
('B032420039', 'HANNAN SAFFIYAH BT MOHD IDRIS', 'GS05', 'uploads/B032420039/p_B032420039_1.jpeg', '2026-05-19', NULL),
('B032420041', 'HENG HUEY JIN', 'GR04', 'uploads/B032420041/p_B032420041_1.jpeg', '2026-05-21', NULL),
('B032420045', 'IZZHILMY BIN SHAMSUL BAHRI', 'GS03', 'uploads/B032420045/p_B032420045_1.jpg', '2026-06-10', NULL),
('B032420059', 'MOHAMAD ZARIL AIDID BIN RASHID', 'GK02', 'uploads/B032420059/p_B032420059_1.jpeg', '2026-05-21', NULL),
('B032420082', 'MUHAMMAD FARHAN BIN MOHD RISHA', 'GS04', 'uploads/1779153456_gmbrmatriks.jpg', '2025-03-07', NULL),
('B032420087', 'MUHAMMAD HAIKAL BIN JOHARI', 'GS05', 'uploads/1779151786_pfpicture.jpg', '2025-07-01', NULL),
('B032420099', 'MUHAMMAD TAUFIQ BIN MOHD ARIFIN', 'GS02', 'uploads/1779151173_gambaq.jpg', '2025-03-07', NULL),
('B032420117', 'NUR MAHIRAH MAISARAH BINTI MOHD IDRIS ', 'GS02', 'uploads/1779152676_1000012397.jpg', '2026-05-19', NULL),
('B032420121', 'NUR SAJIDAH BINTI ZANIAN', 'GS04', 'uploads/1779151265_gambar.jpg', '2026-05-19', NULL),
('B032420127', 'Nureen Amini Binti Fairuz', 'GK02', 'uploads/B032420127/p_B032420127_1.jpeg', '2021-02-18', NULL),
('B032420128', 'NURHANIM NABILA BINTI AB RAZAK', 'GS05', 'uploads/1779152441_IMG_6991.JPG', '2026-04-18', NULL),
('B032420131', 'Nurin Zuhairah Binti Azhar', 'GS04', 'uploads/B032420131/p_B032420131_1.jpg', '2026-05-19', NULL),
('B032420146', 'SASHVINI A/P SHANMUGAM ', 'GR09', 'uploads/1779248373_IMG_2043.jpeg', '2025-10-20', NULL),
('B032420152', 'SITI SYAZLINDA BINTI MOHMAD ZIN', 'GR09', 'uploads/1779246798_WhatsApp Image 2026-04-29 at 11.45.09 AM.jpeg', '2026-05-20', NULL),
('B032420153', 'SUHAIL AMANI BINTI MOHD IKBAL', 'GS01', 'uploads/1779151828_photo_6210501076725731025_w.jpg', '2026-05-19', NULL),
('B032420156', 'SYAHINDAH BINTI AZMI', 'GR06', 'uploads/1779249482_WhatsApp Image 2026-05-20 at 11.27.14 AM.jpeg', '2026-05-03', NULL),
('B032420159', 'TENGKU UMAIRAH KHADIJAH BINTI TENGKU RITHAUDDEN', 'GR07', 'uploads/1779248052_IMG_9759.JPG', '2026-03-24', NULL),
('B032510266', 'Mohamad Faiz Bin Mohd Roshidi', 'GS04', 'uploads/1779151948_GAMBO.jpeg', '2026-05-19', NULL),
('B032510277', 'IZZAH NADHIRAH BINTI ISHAK', 'GS03', 'uploads/1779151980_photo_2026-05-19_08-22-13.jpg', '2026-05-19', NULL),
('B032510280', 'MUHAMMAD KAMIL BIN MOHD ALIASHAK', 'GR05', 'uploads/B032510280/B032510280_20260520_053801_1000202011.jpg', '2025-09-07', 'white t-shirt'),
('B032510289', 'A\'ISYAH NUR ANIEYS NAJIHAH BINTI SHAMSUL ANIS', 'GR01', 'uploads/B032510289/B032510289_20260520_052424_IMG_5818.jpeg', '2026-05-20', NULL),
('B032510300', 'NADIA BINTI SHAHRUL AZMEE', 'GS05', 'uploads/1779151315_2.jpg', '2026-04-10', NULL),
('B032510301', 'MUHAMMAD AMMAR HARITH BIN JASRI', 'GS02', 'uploads/1779151840_pic.jpg', '2026-04-18', NULL),
('B032510304', 'PUTERI NORSHUHADA HARRIS BINTI MD HALIM HARRIS', 'GS01', 'uploads/1779156456_puteri norshuhada.jpeg', '2025-03-11', NULL),
('B032510830', 'SARANESWARY A/P SANDRAN', 'GR06', 'uploads/1779247010_me.jpeg', '2026-05-20', NULL),
('P02165', 'Norlizam', 'GR06', 'uploads/P02165/P02165_20260518_190218_Gemini_Generated_Image_jbvm2ijbvm2ijbvm.jpeg', '0000-00-00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_analytics`
--

CREATE TABLE `student_analytics` (
  `analytics_id` int(11) NOT NULL,
  `matric_no` varchar(20) DEFAULT NULL,
  `search_count` int(11) DEFAULT 0,
  `cbr_count` int(11) DEFAULT 0,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cbr_analysis`
--
ALTER TABLE `cbr_analysis`
  ADD PRIMARY KEY (`cbr_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `matric_no` (`matric_no`);

--
-- Indexes for table `search_log`
--
ALTER TABLE `search_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `matric_no` (`matric_no`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`matric_no`);

--
-- Indexes for table `student_analytics`
--
ALTER TABLE `student_analytics`
  ADD PRIMARY KEY (`analytics_id`),
  ADD KEY `matric_no` (`matric_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cbr_analysis`
--
ALTER TABLE `cbr_analysis`
  MODIFY `cbr_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `search_log`
--
ALTER TABLE `search_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_analytics`
--
ALTER TABLE `student_analytics`
  MODIFY `analytics_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cbr_analysis`
--
ALTER TABLE `cbr_analysis`
  ADD CONSTRAINT `cbr_analysis_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `fashion_item` (`item_id`),
  ADD CONSTRAINT `cbr_analysis_ibfk_2` FOREIGN KEY (`matric_no`) REFERENCES `student` (`matric_no`);

--
-- Constraints for table `search_log`
--
ALTER TABLE `search_log`
  ADD CONSTRAINT `search_log_ibfk_1` FOREIGN KEY (`matric_no`) REFERENCES `student` (`matric_no`);

--
-- Constraints for table `student_analytics`
--
ALTER TABLE `student_analytics`
  ADD CONSTRAINT `student_analytics_ibfk_1` FOREIGN KEY (`matric_no`) REFERENCES `student` (`matric_no`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
