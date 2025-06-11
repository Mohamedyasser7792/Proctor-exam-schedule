CREATE DATABASE  IF NOT EXISTS `exam_schedule` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `exam_schedule`;
-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: exam_schedule
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `authorized_absence_days_per_day`
--

DROP TABLE IF EXISTS `authorized_absence_days_per_day`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `authorized_absence_days_per_day` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number_of_days` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authorized_absence_days_per_day`
--

LOCK TABLES `authorized_absence_days_per_day` WRITE;
/*!40000 ALTER TABLE `authorized_absence_days_per_day` DISABLE KEYS */;
/*!40000 ALTER TABLE `authorized_absence_days_per_day` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_halls`
--

DROP TABLE IF EXISTS `exam_halls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_halls` (
  `hall_id` int(11) NOT NULL AUTO_INCREMENT,
  `hall_name` varchar(100) NOT NULL,
  `number_of_students` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`hall_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_halls`
--

LOCK TABLES `exam_halls` WRITE;
/*!40000 ALTER TABLE `exam_halls` DISABLE KEYS */;
INSERT INTO `exam_halls` VALUES (19,'مدرج 7',100,'2025-06-10 23:09:39','2025-06-10 23:09:39'),(20,'مدرج 8',100,'2025-06-10 23:09:48','2025-06-10 23:09:48'),(21,'سيمنار 5',150,'2025-06-10 23:09:54','2025-06-10 23:10:05'),(22,'سيمنار 4',50,'2025-06-10 23:10:17','2025-06-10 23:10:17'),(23,'الفيلا',80,'2025-06-10 23:10:27','2025-06-10 23:10:27'),(24,'مدرج 4',150,'2025-06-10 23:10:36','2025-06-10 23:10:36');
/*!40000 ALTER TABLE `exam_halls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_schedule`
--

DROP TABLE IF EXISTS `exam_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_schedule` (
  `exam_id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_day` varchar(50) NOT NULL,
  `exam_date` date NOT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `duration` int(11) DEFAULT 60,
  PRIMARY KEY (`exam_id`),
  KEY `subject_id` (`subject_id`),
  KEY `group_id` (`group_id`),
  CONSTRAINT `exam_schedule_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `study_groups` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_schedule`
--

LOCK TABLES `exam_schedule` WRITE;
/*!40000 ALTER TABLE `exam_schedule` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_schedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_schedule_teaching_assistants`
--

DROP TABLE IF EXISTS `exam_schedule_teaching_assistants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exam_schedule_teaching_assistants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_id` int(11) NOT NULL,
  `ta_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `exam_id` (`exam_id`),
  KEY `ta_id` (`ta_id`),
  CONSTRAINT `exam_schedule_teaching_assistants_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exam_schedule` (`exam_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `exam_schedule_teaching_assistants_ibfk_2` FOREIGN KEY (`ta_id`) REFERENCES `teaching_assistants` (`ta_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_schedule_teaching_assistants`
--

LOCK TABLES `exam_schedule_teaching_assistants` WRITE;
/*!40000 ALTER TABLE `exam_schedule_teaching_assistants` DISABLE KEYS */;
/*!40000 ALTER TABLE `exam_schedule_teaching_assistants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `last_exam_schedule`
--

DROP TABLE IF EXISTS `last_exam_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `last_exam_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_id` int(11) DEFAULT NULL,
  `subgroup_id` int(11) DEFAULT NULL,
  `hall_id` int(11) DEFAULT NULL,
  `ta_ids` varchar(255) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `exam_id` (`exam_id`),
  KEY `subgroup_id` (`subgroup_id`),
  KEY `hall_id` (`hall_id`),
  CONSTRAINT `last_exam_schedule_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exam_schedule` (`exam_id`),
  CONSTRAINT `last_exam_schedule_ibfk_2` FOREIGN KEY (`subgroup_id`) REFERENCES `subgroup` (`subgroup_id`),
  CONSTRAINT `last_exam_schedule_ibfk_3` FOREIGN KEY (`hall_id`) REFERENCES `exam_halls` (`hall_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `last_exam_schedule`
--

LOCK TABLES `last_exam_schedule` WRITE;
/*!40000 ALTER TABLE `last_exam_schedule` DISABLE KEYS */;
/*!40000 ALTER TABLE `last_exam_schedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login` (
  `login_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`login_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login`
--

LOCK TABLES `login` WRITE;
/*!40000 ALTER TABLE `login` DISABLE KEYS */;
INSERT INTO `login` VALUES (2,'admin','$2b$10$0KDldBtKanC7Ri9Wh478ZedFmOZmKImBOHT4ScasQQN46TLCaoHrW','2025-06-10 16:24:26','2025-06-10 16:24:26');
/*!40000 ALTER TABLE `login` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `scheduling_errors`
--

DROP TABLE IF EXISTS `scheduling_errors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scheduling_errors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_id` int(11) DEFAULT NULL,
  `error_message` varchar(255) DEFAULT NULL,
  `unassigned_students` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scheduling_errors`
--

LOCK TABLES `scheduling_errors` WRITE;
/*!40000 ALTER TABLE `scheduling_errors` DISABLE KEYS */;
/*!40000 ALTER TABLE `scheduling_errors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `study_groups`
--

DROP TABLE IF EXISTS `study_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `study_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(50) NOT NULL,
  `number_of_groups` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `study_groups`
--

LOCK TABLES `study_groups` WRITE;
/*!40000 ALTER TABLE `study_groups` DISABLE KEYS */;
INSERT INTO `study_groups` VALUES (19,'حاسبات 1',250,'2025-06-10 23:08:41','2025-06-10 23:08:41'),(20,'حاسبات 2',150,'2025-06-10 23:08:50','2025-06-10 23:08:50'),(21,'حاسبات 3',300,'2025-06-10 23:09:00','2025-06-10 23:09:00'),(22,'حسابات 4',150,'2025-06-10 23:09:16','2025-06-10 23:09:16');
/*!40000 ALTER TABLE `study_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `study_subjects`
--

DROP TABLE IF EXISTS `study_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `study_subjects` (
  `subject_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(100) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`subject_id`),
  KEY `group_id` (`group_id`),
  CONSTRAINT `study_subjects_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `study_groups` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `study_subjects`
--

LOCK TABLES `study_subjects` WRITE;
/*!40000 ALTER TABLE `study_subjects` DISABLE KEYS */;
INSERT INTO `study_subjects` VALUES (119,'Lo Des',19,'2025-06-10 20:13:16','2025-06-10 20:13:16'),(120,'Phys 2',19,'2025-06-10 20:13:29','2025-06-10 20:13:29'),(121,'Prog 1',19,'2025-06-10 20:13:40','2025-06-10 20:13:40'),(122,'probab 1',19,'2025-06-10 20:14:41','2025-06-10 20:14:41'),(123,'creati',19,'2025-06-10 20:14:56','2025-06-10 20:14:56'),(124,'Math 2',19,'2025-06-10 20:15:17','2025-06-10 20:15:17'),(125,'Micro',19,'2025-06-10 20:15:36','2025-06-10 20:15:36'),(126,'Intro web',19,'2025-06-10 20:15:48','2025-06-10 20:15:48'),(127,'DS',20,'2025-06-10 20:16:04','2025-06-10 20:16:04'),(128,'NEY W',20,'2025-06-10 20:16:27','2025-06-10 20:16:27'),(129,'Soft 1',20,'2025-06-10 20:16:45','2025-06-10 20:16:45'),(130,'Information eco',20,'2025-06-10 20:17:03','2025-06-10 20:17:03'),(131,'Intro Ope REs',20,'2025-06-10 20:17:23','2025-06-10 20:17:23'),(132,'Machin',20,'2025-06-10 20:17:35','2025-06-10 20:17:35'),(133,'Coputer org 1',20,'2025-06-10 20:17:52','2025-06-10 20:17:52'),(134,'OS',20,'2025-06-10 20:18:06','2025-06-10 20:18:06'),(135,'Numerical',20,'2025-06-10 20:19:11','2025-06-10 20:19:11'),(136,'Digital Signal Processing',20,'2025-06-10 20:19:45','2025-06-10 20:19:45'),(137,'Human Rights and Ethics- Old',20,'2025-06-10 20:20:08','2025-06-10 20:20:08'),(138,'Information Ethics- Old',21,'2025-06-10 20:20:30','2025-06-10 20:20:30'),(139,'Web Engineering (1)-Old',21,'2025-06-10 20:20:52','2025-06-10 20:20:52'),(140,'omputer Networks (2)-Old',21,'2025-06-10 20:21:21','2025-06-10 20:21:21');
/*!40000 ALTER TABLE `study_subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subgroup`
--

DROP TABLE IF EXISTS `subgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subgroup` (
  `subgroup_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `capacity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`subgroup_id`),
  KEY `group_id` (`group_id`),
  CONSTRAINT `subgroup_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `study_groups` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subgroup`
--

LOCK TABLES `subgroup` WRITE;
/*!40000 ALTER TABLE `subgroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `subgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ta_day_offs`
--

DROP TABLE IF EXISTS `ta_day_offs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ta_day_offs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ta_id` int(11) NOT NULL,
  `day_off` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ta_id` (`ta_id`),
  CONSTRAINT `ta_day_offs_ibfk_1` FOREIGN KEY (`ta_id`) REFERENCES `teaching_assistants` (`ta_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ta_day_offs`
--

LOCK TABLES `ta_day_offs` WRITE;
/*!40000 ALTER TABLE `ta_day_offs` DISABLE KEYS */;
INSERT INTO `ta_day_offs` VALUES (27,96,'2004-01-19','2025-06-10 19:58:37','2025-06-10 19:58:37'),(28,97,'2006-10-16','2025-06-10 19:59:20','2025-06-10 19:59:20'),(29,98,'2006-10-17','2025-06-10 20:00:06','2025-06-10 20:00:06'),(30,99,'2004-01-21','2025-06-10 20:00:32','2025-06-10 20:00:32'),(31,100,'2006-10-18','2025-06-10 20:01:08','2025-06-10 20:01:08'),(32,101,'2009-07-14','2025-06-10 20:01:30','2025-06-10 20:01:30'),(33,102,'2002-04-09','2025-06-10 20:01:51','2025-06-10 20:01:51'),(34,103,'2001-06-09','2025-06-10 20:02:24','2025-06-10 20:02:24'),(35,104,'2006-10-19','2025-06-10 20:02:52','2025-06-10 20:02:52'),(36,104,'2000-06-27','2025-06-10 20:02:52','2025-06-10 20:02:52'),(37,104,'2008-09-13','2025-06-10 20:02:52','2025-06-10 20:02:52'),(38,105,'2007-10-01','2025-06-10 20:03:13','2025-06-10 20:03:13'),(39,106,'2009-07-15','2025-06-10 20:03:55','2025-06-10 20:03:55'),(40,106,'2000-06-27','2025-06-10 20:03:55','2025-06-10 20:03:55'),(41,106,'2006-11-30','2025-06-10 20:03:55','2025-06-10 20:03:55'),(42,107,'2003-03-25','2025-06-10 20:04:30','2025-06-10 20:04:30'),(43,108,'2002-05-24','2025-06-10 20:04:56','2025-06-10 20:04:56'),(44,109,'2009-08-29','2025-06-10 20:05:29','2025-06-10 20:05:29'),(45,110,'2005-02-18','2025-06-10 20:06:10','2025-06-10 20:06:10'),(46,111,'2008-09-16','2025-06-10 20:06:53','2025-06-10 20:06:53'),(47,112,'2002-04-13','2025-06-10 20:07:43','2025-06-10 20:07:43'),(48,112,'2009-08-30','2025-06-10 20:07:43','2025-06-10 20:07:43');
/*!40000 ALTER TABLE `ta_day_offs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teaching_assistants`
--

DROP TABLE IF EXISTS `teaching_assistants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teaching_assistants` (
  `ta_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` enum('Basic','Reserve') DEFAULT 'Basic',
  `role` enum('Teaching Assistant','Doctor') DEFAULT 'Teaching Assistant',
  `join_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `assignments_count` int(11) DEFAULT 0,
  PRIMARY KEY (`ta_id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teaching_assistants`
--

LOCK TABLES `teaching_assistants` WRITE;
/*!40000 ALTER TABLE `teaching_assistants` DISABLE KEYS */;
INSERT INTO `teaching_assistants` VALUES (96,'ربيع','Basic','Teaching Assistant','2023-06-10','2025-06-10 19:58:37','2025-06-10 19:58:37',0),(97,'فاطمه','Basic','Teaching Assistant','2024-06-10','2025-06-10 19:59:20','2025-06-10 19:59:20',0),(98,'تقي','Basic','Teaching Assistant','2024-07-10','2025-06-10 20:00:06','2025-06-10 20:00:06',0),(99,'رضوا','Basic','Teaching Assistant','2023-08-10','2025-06-10 20:00:32','2025-06-10 20:00:32',0),(100,'عبدالرحمن','Basic','Teaching Assistant','2025-01-10','2025-06-10 20:01:08','2025-06-10 20:01:08',0),(101,'تسبيح','Basic','Teaching Assistant','2025-03-10','2025-06-10 20:01:30','2025-06-10 20:01:30',0),(102,'مصطفي','Basic','Teaching Assistant','2025-02-10','2025-06-10 20:01:51','2025-06-10 20:01:51',0),(103,'ايمان','Reserve','Doctor','2023-10-10','2025-06-10 20:02:24','2025-06-10 20:02:24',0),(104,'محمد ايهاب','Basic','Teaching Assistant','2025-04-10','2025-06-10 20:02:52','2025-06-10 20:02:52',0),(105,'زياد محمد','Basic','Teaching Assistant','2025-03-10','2025-06-10 20:03:13','2025-06-10 20:03:13',0),(106,'مروه','Basic','Doctor','2022-03-10','2025-06-10 20:03:55','2025-06-10 20:03:55',0),(107,'رودينا','Basic','Doctor','2020-07-10','2025-06-10 20:04:30','2025-06-10 20:04:30',0),(108,'محمد عاشور','Basic','Doctor','2019-07-10','2025-06-10 20:04:56','2025-06-10 20:04:56',0),(109,'وفاء سامي','Basic','Doctor','2018-09-10','2025-06-10 20:05:29','2025-06-10 20:05:29',0),(110,'ايه','Basic','Teaching Assistant','2024-07-10','2025-06-10 20:06:10','2025-06-10 20:06:10',0),(111,'محمد خليل','Basic','Teaching Assistant','2024-11-10','2025-06-10 20:06:53','2025-06-10 20:06:53',0),(112,'محمد سالم','Reserve','Doctor','2025-03-10','2025-06-10 20:07:43','2025-06-10 20:07:43',0);
/*!40000 ALTER TABLE `teaching_assistants` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-11  2:25:14
