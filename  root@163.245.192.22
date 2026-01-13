-- MySQL dump 10.13  Distrib 8.0.44, for Linux (x86_64)
--
-- Host: localhost    Database: faculty_portfolio
-- ------------------------------------------------------
-- Server version	8.0.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` bigint unsigned NOT NULL,
  `meta_json` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `audit_logs_entity_type_entity_id_index` (`entity_type`,`entity_id`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_offerings`
--

DROP TABLE IF EXISTS `class_offerings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_offerings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subject_id` bigint unsigned NOT NULL,
  `academic_year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `term` tinyint unsigned NOT NULL,
  `section` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assignment_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instructional_material` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `syllabus` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `faculty_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `class_offering_unique` (`subject_id`,`academic_year`,`term`,`section`),
  KEY `class_offerings_faculty_id_foreign` (`faculty_id`),
  CONSTRAINT `class_offerings_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `class_offerings_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=203 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_offerings`
--

LOCK TABLES `class_offerings` WRITE;
/*!40000 ALTER TABLE `class_offerings` DISABLE KEYS */;
INSERT INTO `class_offerings` VALUES (5,7,'2025-2026',1,'2A',NULL,NULL,NULL,4,'2025-11-12 03:15:06','2025-11-12 03:15:06'),(6,13,'2025-2026',1,'1A',NULL,NULL,NULL,4,'2025-11-12 03:15:06','2025-11-12 03:15:06'),(7,25,'2025-2026',1,'1','assignments/25/1762934434_RUIZ,J_TA&ROT.pdf','instructional_materials/25/1762948697_CMPE 102-Programming Logic and Design.pdf',NULL,1,'2025-11-12 04:43:18','2025-11-12 11:58:17'),(8,26,'2025-2026',1,'1','assignments/26/1762930329_LEQUIGAN_TA.pdf',NULL,NULL,8,'2025-11-12 05:49:37','2025-11-12 06:52:09'),(9,1,'2024-2025',1,'1A',NULL,NULL,NULL,3,'2025-11-12 06:11:59','2025-11-12 06:11:59'),(10,2,'2024-2025',1,'1B',NULL,NULL,NULL,3,'2025-11-12 06:11:59','2025-11-12 06:11:59'),(11,7,'2024-2025',1,'1',NULL,NULL,NULL,3,'2025-11-12 06:11:59','2025-11-12 06:11:59'),(12,25,'2024-2025',1,'1A',NULL,'instructional_materials/25/1762948669_CMPE 102-Programming Logic and Design.pdf',NULL,1,'2025-11-12 06:14:53','2025-11-12 11:57:49'),(13,26,'2024-2025',1,'1A',NULL,NULL,NULL,8,'2025-11-12 06:14:53','2025-11-12 06:14:53'),(32,27,'2025-2026',1,'1',NULL,NULL,NULL,9,'2025-11-12 07:01:06','2025-11-12 07:01:06'),(33,41,'2025-2026',1,'1',NULL,NULL,NULL,16,'2025-11-12 07:03:25','2025-11-12 07:03:25'),(34,45,'2025-2026',1,'2','assignments/45/1763037854_RUIZ, F_TA&ROT.pdf',NULL,NULL,6,'2025-11-12 07:08:53','2025-11-13 12:44:14'),(35,47,'2025-2026',1,'2','assignments/47/1763037418_FERNANDO_TA&ROT.pdf',NULL,NULL,13,'2025-11-12 07:09:28','2025-11-13 12:36:58'),(36,27,'2024-2025',1,'1',NULL,NULL,NULL,11,'2025-11-12 07:11:20','2025-11-12 07:11:20'),(37,32,'2024-2025',2,'1',NULL,NULL,NULL,12,'2025-11-12 07:15:17','2025-11-12 07:15:17'),(38,33,'2024-2025',2,'1',NULL,NULL,NULL,6,'2025-11-12 07:15:43','2025-11-12 07:15:43'),(39,34,'2024-2025',2,'1',NULL,NULL,NULL,9,'2025-11-12 07:16:02','2025-11-12 07:16:02'),(40,43,'2024-2025',1,'1',NULL,NULL,NULL,12,'2025-11-12 07:16:43','2025-11-12 07:16:43'),(41,44,'2024-2025',1,'1',NULL,NULL,NULL,1,'2025-11-12 07:17:21','2025-11-12 07:17:21'),(42,45,'2024-2025',1,'1',NULL,NULL,NULL,6,'2025-11-12 07:18:00','2025-11-12 07:18:00'),(43,46,'2024-2025',1,'1',NULL,NULL,NULL,17,'2025-11-12 07:19:42','2025-11-12 07:19:42'),(44,47,'2024-2025',1,'1',NULL,NULL,NULL,13,'2025-11-12 07:20:09','2025-11-12 07:20:09'),(45,51,'2024-2025',2,'1',NULL,NULL,NULL,1,'2025-11-12 07:20:38','2025-11-12 07:20:38'),(46,50,'2024-2025',2,'1',NULL,NULL,NULL,11,'2025-11-12 07:21:05','2025-11-12 07:21:05'),(47,52,'2024-2025',2,'1',NULL,NULL,NULL,10,'2025-11-12 07:21:24','2025-11-12 07:21:24'),(48,53,'2024-2025',2,'1',NULL,NULL,NULL,14,'2025-11-12 07:21:44','2025-11-12 07:21:44'),(49,54,'2024-2025',2,'1',NULL,NULL,NULL,11,'2025-11-12 07:22:21','2025-11-12 07:22:21'),(50,55,'2024-2025',2,'1',NULL,NULL,NULL,14,'2025-11-12 07:22:39','2025-11-12 07:22:39'),(51,61,'2024-2025',1,'1',NULL,NULL,NULL,17,'2025-11-12 07:25:22','2025-11-12 07:25:22'),(52,62,'2024-2025',1,'1',NULL,NULL,NULL,16,'2025-11-12 07:25:39','2025-11-12 07:25:39'),(53,63,'2024-2025',1,'1',NULL,NULL,NULL,10,'2025-11-12 07:25:54','2025-11-12 07:25:54'),(54,66,'2024-2025',1,'1',NULL,NULL,NULL,12,'2025-11-12 07:26:18','2025-11-12 07:26:18'),(55,64,'2024-2025',1,'1',NULL,NULL,NULL,1,'2025-11-12 07:26:42','2025-11-12 07:26:42'),(56,65,'2024-2025',1,'1',NULL,NULL,NULL,10,'2025-11-12 07:27:36','2025-11-12 07:27:36'),(57,68,'2024-2025',2,'1',NULL,NULL,NULL,9,'2025-11-12 07:29:18','2025-11-12 07:29:18'),(58,69,'2024-2025',2,'1',NULL,NULL,NULL,10,'2025-11-12 07:29:43','2025-11-12 07:29:43'),(59,70,'2024-2025',2,'1',NULL,NULL,NULL,10,'2025-11-12 07:30:02','2025-11-12 07:30:02'),(60,33,'2025-2026',2,'1',NULL,NULL,NULL,6,'2025-11-12 08:18:01','2025-11-12 08:18:01'),(61,61,'2025-2026',1,'2','assignments/61/1763037928_RUIZ,J_TA&ROT-1.pdf',NULL,NULL,1,'2025-11-12 08:22:43','2025-11-13 12:45:28'),(62,72,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(63,90,'2024-2025',1,'1',NULL,NULL,NULL,14,'2025-11-13 01:15:17','2025-11-13 01:28:25'),(64,73,'2024-2025',1,'1',NULL,NULL,NULL,7,'2025-11-13 01:15:17','2025-11-13 01:24:19'),(65,81,'2024-2025',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(66,82,'2024-2025',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(67,91,'2024-2025',1,'1',NULL,NULL,NULL,7,'2025-11-13 01:15:17','2025-11-13 01:29:42'),(68,92,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(69,98,'2024-2025',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(70,107,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(71,99,'2024-2025',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(72,74,'2024-2025',1,'1',NULL,NULL,NULL,18,'2025-11-13 01:15:17','2025-11-13 01:26:12'),(73,83,'2024-2025',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(74,93,'2024-2025',1,'1',NULL,NULL,NULL,18,'2025-11-13 01:15:17','2025-11-13 01:28:56'),(75,100,'2024-2025',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(76,101,'2024-2025',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(77,102,'2024-2025',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(78,106,'2024-2025',3,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(79,108,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(80,109,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(81,110,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(82,111,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(83,115,'2024-2025',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(84,116,'2024-2025',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(85,94,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(86,112,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(87,84,'2024-2025',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(88,75,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(89,85,'2024-2025',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(90,113,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(91,114,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(92,76,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(93,77,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(94,103,'2024-2025',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(95,78,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(96,86,'2024-2025',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(97,104,'2024-2025',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(98,79,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(99,87,'2024-2025',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(100,80,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(101,88,'2024-2025',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(102,95,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(103,105,'2024-2025',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(104,89,'2024-2025',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(105,96,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(106,97,'2024-2025',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:15:17','2025-11-13 01:15:17'),(125,72,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(126,90,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(127,73,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(129,82,'2025-2026',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(130,91,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(131,92,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(132,98,'2025-2026',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(133,107,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(134,99,'2025-2026',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(135,74,'2025-2026',1,'1','assignments/74/1763037741_RIOS_TA&ROT.pdf',NULL,NULL,18,'2025-11-13 01:17:14','2025-11-13 12:42:21'),(136,83,'2025-2026',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(137,93,'2025-2026',1,'1','assignments/93/1763037791_RIOS_TA&ROT.pdf',NULL,NULL,18,'2025-11-13 01:17:14','2025-11-13 12:43:11'),(138,100,'2025-2026',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(139,101,'2025-2026',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(140,102,'2025-2026',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(141,106,'2025-2026',3,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(142,108,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(143,109,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(144,110,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(145,111,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(146,115,'2025-2026',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(147,116,'2025-2026',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(148,94,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(149,112,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(150,84,'2025-2026',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(151,75,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(152,85,'2025-2026',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(153,113,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(154,114,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(155,76,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(156,77,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(157,103,'2025-2026',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(158,78,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(159,86,'2025-2026',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(160,104,'2025-2026',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(161,79,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(162,87,'2025-2026',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(163,80,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(164,88,'2025-2026',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(165,95,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(166,105,'2025-2026',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(167,89,'2025-2026',2,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(168,96,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(169,97,'2025-2026',1,'1',NULL,NULL,NULL,NULL,'2025-11-13 01:17:14','2025-11-13 01:17:14'),(189,81,'2024-2025',1,'1',NULL,NULL,NULL,7,'2025-11-13 01:27:04','2025-11-13 01:27:04'),(190,83,'2024-2025',1,'1',NULL,NULL,NULL,18,'2025-11-13 01:27:45','2025-11-13 01:27:45'),(191,98,'2024-2025',1,'1',NULL,NULL,NULL,13,'2025-11-13 01:30:22','2025-11-13 01:30:22'),(192,100,'2024-2025',1,'1',NULL,NULL,NULL,18,'2025-11-13 01:30:37','2025-11-13 01:30:37'),(193,43,'2025-2026',1,'2','assignments/43/1763037297_ANDAYA_TA.pdf',NULL,NULL,11,'2025-11-13 12:34:57','2025-11-13 12:34:57'),(194,66,'2025-2026',1,'1','assignments/66/1763037477_LEGASPI_TA&ROT.pdf',NULL,NULL,14,'2025-11-13 12:37:57','2025-11-13 12:37:57'),(195,66,'2025-2026',1,'2','assignments/66/1763037524_LEGASPI_TA&ROT.pdf',NULL,NULL,14,'2025-11-13 12:38:44','2025-11-13 12:38:44'),(196,43,'2025-2026',1,'1','assignments/43/1763037590_LIBED_TA.pdf',NULL,NULL,12,'2025-11-13 12:39:50','2025-11-13 12:39:50'),(197,25,'2025-2026',1,'2','assignments/25/1763037633_MANARANG_TA&ROT.pdf',NULL,NULL,10,'2025-11-13 12:40:33','2025-11-13 12:40:33'),(198,25,'2025-2026',1,'3','assignments/25/1763037659_MANARANG_TA&ROT.pdf',NULL,NULL,10,'2025-11-13 12:40:59','2025-11-13 12:40:59'),(199,63,'2025-2026',1,'2','assignments/63/1763037685_MANARANG_TA&ROT.pdf',NULL,NULL,10,'2025-11-13 12:41:25','2025-11-13 12:41:25'),(200,74,'2025-2026',1,'2','assignments/74/1763037764_RIOS_TA&ROT.pdf',NULL,NULL,18,'2025-11-13 12:42:44','2025-11-13 12:42:44'),(201,93,'2025-2026',1,'2','assignments/93/1763037819_RIOS_TA&ROT.pdf',NULL,NULL,18,'2025-11-13 12:43:39','2025-11-13 12:43:39'),(202,45,'2025-2026',1,'1','assignments/45/1763037887_RUIZ, F_TA&ROT.pdf',NULL,NULL,6,'2025-11-13 12:44:47','2025-11-13 12:44:47');
/*!40000 ALTER TABLE `class_offerings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_user`
--

DROP TABLE IF EXISTS `course_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `course_user` (
  `course_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `course_user_course_id_user_id_unique` (`course_id`,`user_id`),
  KEY `course_user_user_id_foreign` (`user_id`),
  CONSTRAINT `course_user_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_user`
--

LOCK TABLES `course_user` WRITE;
/*!40000 ALTER TABLE `course_user` DISABLE KEYS */;
INSERT INTO `course_user` VALUES (4,6,'2025-11-12 07:48:19','2025-11-12 07:48:19'),(9,6,'2025-11-12 07:48:19','2025-11-12 07:48:19');
/*!40000 ALTER TABLE `course_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `courses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courses_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES (1,'DCvET','Diploma in Civil Engineering Technology','2025-11-12 03:15:05','2025-11-12 03:15:05'),(2,'DCET','Diploma in Computer Engineering Technology','2025-11-12 03:15:05','2025-11-12 03:15:05'),(3,'DEET','Diploma in Electrical Engineering Technology','2025-11-12 03:15:05','2025-11-12 03:15:05'),(4,'DECET','Diploma in Electronics and Communications Eng. Tech.','2025-11-12 03:15:05','2025-11-12 03:15:05'),(5,'DICT','Diploma in Information and Communication Technology','2025-11-12 03:15:05','2025-11-12 03:15:05'),(6,'DMET','Diploma in Mechanical Engineering Technology','2025-11-12 03:15:05','2025-11-12 03:15:05'),(7,'DOMT','Diploma in Office Management Technology','2025-11-12 03:15:05','2025-11-12 03:15:05'),(8,'DRET','Diploma in Railway Engineering Technology','2025-11-12 03:15:05','2025-11-12 03:15:05'),(9,'DCPET','Diploma in Computer Engineering Technology','2025-11-12 04:30:54','2025-11-12 04:30:54');
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imports`
--

DROP TABLE IF EXISTS `imports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `imports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','processing','completed','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `stats_json` json DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imports`
--

LOCK TABLES `imports` WRITE;
/*!40000 ALTER TABLE `imports` DISABLE KEYS */;
/*!40000 ALTER TABLE `imports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_11_11_124027_add_two_factor_columns_to_users_table',1),(5,'2025_11_11_124045_create_personal_access_tokens_table',1),(6,'2025_11_11_131704_create_courses_table',1),(7,'2025_11_11_131710_create_subjects_table',1),(8,'2025_11_11_131717_create_class_offerings_table',1),(9,'2025_11_11_131722_create_portfolios_table',1),(10,'2025_11_11_131727_create_portfolio_items_table',1),(11,'2025_11_11_131730_create_reviews_table',1),(12,'2025_11_11_131734_create_audit_logs_table',1),(13,'2025_11_11_131737_create_imports_table',1),(14,'2025_11_11_132805_add_role_to_users_table',1),(15,'2025_11_12_042641_add_curriculum_fields_to_subjects_table',2),(16,'2025_11_12_043419_add_course_id_to_users_table',3),(17,'2025_11_12_062139_add_assignment_document_to_class_offerings_table',4),(18,'2025_11_12_074707_create_course_user_table',5),(19,'2025_11_12_113747_add_im_and_syllabus_to_class_offerings_table',6);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portfolio_items`
--

DROP TABLE IF EXISTS `portfolio_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `portfolio_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `portfolio_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metadata_json` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `portfolio_items_portfolio_id_type_index` (`portfolio_id`,`type`),
  CONSTRAINT `portfolio_items_portfolio_id_foreign` FOREIGN KEY (`portfolio_id`) REFERENCES `portfolios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portfolio_items`
--

LOCK TABLES `portfolio_items` WRITE;
/*!40000 ALTER TABLE `portfolio_items` DISABLE KEYS */;
INSERT INTO `portfolio_items` VALUES (1,6,'grade_sheets','GradeSheet-1.pdf','portfolios/1/6/grade_sheets/1762922906_GradeSheet-1.pdf','{\"size\": 11313, \"mime_type\": \"application/octet-stream\", \"original_name\": \"GradeSheet-1.pdf\"}','2025-11-12 04:48:26','2025-11-12 04:48:26'),(2,6,'faculty_assignment','RUIZ,J_TA&ROT.pdf','portfolios/1/6/faculty_assignment/1762929686_RUIZ,J_TA&ROT.pdf','{\"size\": 1075368, \"mime_type\": \"application/pdf\", \"original_name\": \"RUIZ,J_TA&ROT.pdf\"}','2025-11-12 06:41:26','2025-11-12 06:41:26'),(3,16,'major_exam','CPET 103 Midterm Exam Set A.doc','portfolios/9/16/major_exam/1763031194_CPET 103 Midterm Exam Set A.doc','{\"size\": 71168, \"mime_type\": \"application/msword\", \"original_name\": \"CPET 103 Midterm Exam Set A.doc\"}','2025-11-13 10:53:14','2025-11-13 10:53:14'),(4,16,'major_exam','CPET 103 Midterm Exam Set B.doc','portfolios/9/16/major_exam/1763031247_CPET 103 Midterm Exam Set B.doc','{\"size\": 114176, \"mime_type\": \"application/msword\", \"original_name\": \"CPET 103 Midterm Exam Set B.doc\"}','2025-11-13 10:54:07','2025-11-13 10:54:07'),(5,16,'syllabus','CPET 103 Web Technology and Programming 2 Syllabus - 2ndSem 2425_de_guzman.docx','portfolios/9/16/syllabus/1763031289_CPET 103 Web Technology and Programming 2 Syllabus - 2ndSem 2425_de_guzman.docx','{\"size\": 369218, \"mime_type\": \"application/vnd.openxmlformats-officedocument.wordprocessingml.document\", \"original_name\": \"CPET 103 Web Technology and Programming 2 Syllabus - 2ndSem 2425_de_guzman.docx\"}','2025-11-13 10:54:49','2025-11-13 10:54:49'),(6,16,'class_list','DCPET 1-1.pdf','portfolios/9/16/class_list/1763031330_DCPET 1-1.pdf','{\"size\": 23002, \"mime_type\": \"application/pdf\", \"original_name\": \"DCPET 1-1.pdf\"}','2025-11-13 10:55:30','2025-11-13 10:55:30'),(7,16,'class_list','DCPET 1-2.pdf','portfolios/9/16/class_list/1763031355_DCPET 1-2.pdf','{\"size\": 23293, \"mime_type\": \"application/pdf\", \"original_name\": \"DCPET 1-2.pdf\"}','2025-11-13 10:55:55','2025-11-13 10:55:55'),(8,16,'class_list','DCPET 1-3.pdf','portfolios/9/16/class_list/1763031362_DCPET 1-3.pdf','{\"size\": 24952, \"mime_type\": \"application/pdf\", \"original_name\": \"DCPET 1-3.pdf\"}','2025-11-13 10:56:02','2025-11-13 10:56:02'),(9,16,'faculty_assignment','Screenshot 2025-01-31 104829.png','portfolios/9/16/faculty_assignment/1763031424_Screenshot 2025-01-31 104829.png','{\"size\": 438371, \"mime_type\": \"image/png\", \"original_name\": \"Screenshot 2025-01-31 104829.png\"}','2025-11-13 10:57:04','2025-11-13 10:57:04'),(10,16,'grade_sheets','DCPET 1-1 GS.xlsx','portfolios/9/16/grade_sheets/1763031478_DCPET 1-1 GS.xlsx','{\"size\": 48346, \"mime_type\": \"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet\", \"original_name\": \"DCPET 1-1 GS.xlsx\"}','2025-11-13 10:57:58','2025-11-13 10:57:58'),(11,16,'grade_sheets','DCPET 1-2 GS.xlsx','portfolios/9/16/grade_sheets/1763031488_DCPET 1-2 GS.xlsx','{\"size\": 64011, \"mime_type\": \"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet\", \"original_name\": \"DCPET 1-2 GS.xlsx\"}','2025-11-13 10:58:08','2025-11-13 10:58:08'),(12,16,'grade_sheets','DCPET 1-3 GS.xlsx','portfolios/9/16/grade_sheets/1763031504_DCPET 1-3 GS.xlsx','{\"size\": 51655, \"mime_type\": \"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet\", \"original_name\": \"DCPET 1-3 GS.xlsx\"}','2025-11-13 10:58:24','2025-11-13 10:58:24'),(13,16,'sample_ims','CPET 103 - Web Technology and Programming 2 2ndSem2425_deguzman.docx','portfolios/9/16/sample_ims/1763031546_CPET 103 - Web Technology and Programming 2 2ndSem2425_deguzman.docx','{\"size\": 5435582, \"mime_type\": \"application/vnd.openxmlformats-officedocument.wordprocessingml.document\", \"original_name\": \"CPET 103 - Web Technology and Programming 2 2ndSem2425_deguzman.docx\"}','2025-11-13 10:59:06','2025-11-13 10:59:06'),(14,16,'acknowledgement','IMG20250613182845.jpg','portfolios/9/16/acknowledgement/1763031568_IMG20250613182845.jpg','{\"size\": 1197675, \"mime_type\": \"image/jpeg\", \"original_name\": \"IMG20250613182845.jpg\"}','2025-11-13 10:59:28','2025-11-13 10:59:28'),(15,16,'activity_rubrics','IMG20250623130007.jpg','portfolios/9/16/activity_rubrics/1763031599_IMG20250623130007.jpg','{\"size\": 2412623, \"mime_type\": \"image/jpeg\", \"original_name\": \"IMG20250623130007.jpg\"}','2025-11-13 10:59:59','2025-11-13 10:59:59'),(16,16,'attendance','IMG20250623104507.jpg','portfolios/9/16/attendance/1763031611_IMG20250623104507.jpg','{\"size\": 2263808, \"mime_type\": \"image/jpeg\", \"original_name\": \"IMG20250623104507.jpg\"}','2025-11-13 11:00:11','2025-11-13 11:00:11'),(17,16,'sample_quiz','CPET 103 Midterm Exam Set A.doc','portfolios/9/16/sample_quiz/1763031628_CPET 103 Midterm Exam Set A.doc','{\"size\": 71168, \"mime_type\": \"application/msword\", \"original_name\": \"CPET 103 Midterm Exam Set A.doc\"}','2025-11-13 11:00:28','2025-11-13 11:00:28'),(18,16,'tos','IMG20250613182901.jpg','portfolios/9/16/tos/1763031653_IMG20250613182901.jpg','{\"size\": 2166767, \"mime_type\": \"image/jpeg\", \"original_name\": \"IMG20250613182901.jpg\"}','2025-11-13 11:00:53','2025-11-13 11:00:53'),(19,19,'class_list','Section_Report_2026-01-06(1).xlsx','portfolios/10/19/class_list/1767771447_695e0d3713dfe_Section_Report_2026-01-06(1).xlsx','{\"size\": 10384, \"mime_type\": \"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet\", \"original_name\": \"Section_Report_2026-01-06(1).xlsx\"}','2026-01-07 07:37:27','2026-01-07 07:37:27'),(20,19,'class_list','Section_Report_2026-01-06(1).xlsx','portfolios/10/19/class_list/1767771460_695e0d44bff9c_Section_Report_2026-01-06(1).xlsx','{\"size\": 10384, \"mime_type\": \"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet\", \"original_name\": \"Section_Report_2026-01-06(1).xlsx\"}','2026-01-07 07:37:40','2026-01-07 07:37:40');
/*!40000 ALTER TABLE `portfolio_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portfolios`
--

DROP TABLE IF EXISTS `portfolios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `portfolios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `class_offering_id` bigint unsigned NOT NULL,
  `status` enum('draft','submitted','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `resubmission_count` int unsigned NOT NULL DEFAULT '0',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `portfolio_unique` (`user_id`,`class_offering_id`),
  KEY `portfolios_class_offering_id_foreign` (`class_offering_id`),
  CONSTRAINT `portfolios_class_offering_id_foreign` FOREIGN KEY (`class_offering_id`) REFERENCES `class_offerings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `portfolios_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portfolios`
--

LOCK TABLES `portfolios` WRITE;
/*!40000 ALTER TABLE `portfolios` DISABLE KEYS */;
INSERT INTO `portfolios` VALUES (4,4,6,'draft',0,NULL,NULL,'2025-11-12 03:59:55','2025-11-12 03:59:55'),(6,1,7,'draft',0,NULL,NULL,'2025-11-12 04:43:31','2025-11-12 04:43:31'),(7,8,8,'draft',0,NULL,NULL,'2025-11-12 05:49:58','2025-11-12 05:49:58'),(10,6,34,'draft',0,NULL,NULL,'2025-11-12 07:34:47','2025-11-12 07:34:47'),(11,6,38,'draft',0,NULL,NULL,'2025-11-12 07:34:57','2025-11-12 07:34:57'),(12,1,12,'draft',0,NULL,NULL,'2025-11-12 08:05:37','2025-11-12 08:05:37'),(13,1,61,'draft',0,NULL,NULL,'2025-11-12 08:23:26','2025-11-12 08:23:26'),(14,1,45,'draft',0,NULL,NULL,'2025-11-12 11:33:26','2025-11-12 11:33:26'),(15,9,39,'draft',0,NULL,NULL,'2025-11-13 10:46:41','2025-11-13 10:46:41'),(16,9,32,'approved',0,'2025-11-13 11:26:11','2025-11-13 11:27:46','2025-11-13 10:48:28','2025-11-13 11:27:46'),(17,14,194,'draft',0,NULL,NULL,'2025-11-13 12:52:27','2025-11-13 12:52:27'),(18,8,13,'draft',0,NULL,NULL,'2026-01-07 07:25:15','2026-01-07 07:25:15'),(19,10,197,'draft',0,NULL,NULL,'2026-01-07 07:33:33','2026-01-07 07:33:33');
/*!40000 ALTER TABLE `portfolios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `portfolio_id` bigint unsigned NOT NULL,
  `reviewer_id` bigint unsigned NOT NULL,
  `decision` enum('approved','rejected','changes_requested') COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_portfolio_id_foreign` (`portfolio_id`),
  KEY `reviews_reviewer_id_foreign` (`reviewer_id`),
  CONSTRAINT `reviews_portfolio_id_foreign` FOREIGN KEY (`portfolio_id`) REFERENCES `portfolios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (1,16,6,'changes_requested','check the attendance records','2025-11-13 11:05:01','2025-11-13 11:05:01'),(2,16,6,'approved','thank you','2025-11-13 11:27:46','2025-11-13 11:27:46');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('1f7LR4ICQpLNWaXzi3iWFDrZQlYBHtpRxR4tYtn7',NULL,'172.21.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoicEVONUJBNEh5ZDBNT0xWdnFad29jam83TWkwNXJJVjZXTlBVWGcyYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly9wb3J0Zm9saW8uaXRlY2hwb3J0Zm9saW8ueHl6L2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1767780841),('C63kj4iLyma2LsLixzkVyFf2vPAYztKBRVIgF6BY',6,'172.21.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZDZuU2ZFQUxSRFRlUDlXMk1NOTJ1TkpkeWJpRkZqbWYzclppY0o2TSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4MS9jaGFpci9yZXBvcnRzIjtzOjU6InJvdXRlIjtzOjE5OiJjaGFpci5yZXBvcnRzLmluZGV4Ijt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NjtzOjIxOiJwYXNzd29yZF9oYXNoX3NhbmN0dW0iO3M6NjA6IiQyeSQxMiQ5VDFJOGUxYnB5WENuZ1BvT1RKLlV1ZFJVYS91Tm04Uk0yQ2tuOUIzTE9jZFhSRzVZYzViVyI7fQ==',1767774945),('LiDT0shasRk4WFwDQ2KxMCvCQzW3ZFXzLYVRoixX',NULL,'172.21.0.1','Mozilla/5.0 (Android 16; Mobile; rv:146.0) Gecko/146.0 Firefox/146.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUmc1eTVlcHNITXN4ZXR4b0JIbndJemhBZWdqZjJoUmRjZ0R5bFNOdSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly9wb3J0Zm9saW8uaXRlY2hwb3J0Zm9saW8ueHl6L2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1767780871),('T5QVDgKuGMjkfXuMC4eTj3reXLskApOMZFZYzKfN',NULL,'172.21.0.1','curl/8.5.0','YToyOntzOjY6Il90b2tlbiI7czo0MDoib084OVhHWjlSSFlSaUs0aUYyOHJza0k0UzJqQk9POGtIMGRqUm5xYyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1767779547),('VFKhGdl5G21GsGUcGv7cCKh5H62n1eWXqKjnUNwP',NULL,'172.21.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVmd0TWlrZUpaTWRWdkNqaXh5SVdBRmd3bk94MVU4clpHeHBCSmRYZiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNToiaHR0cDovL2xvY2FsaG9zdDo4MDgxL2NoYWlyL3JlcG9ydHMiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czoyNzoiaHR0cDovL2xvY2FsaG9zdDo4MDgxL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1767781649);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint unsigned NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year_level` tinyint unsigned NOT NULL,
  `term` tinyint unsigned NOT NULL,
  `lec_hours` tinyint unsigned NOT NULL DEFAULT '0',
  `lab_hours` tinyint unsigned NOT NULL DEFAULT '0',
  `credit_units` tinyint unsigned NOT NULL DEFAULT '0',
  `tuition_hours` tinyint unsigned NOT NULL DEFAULT '0',
  `prereq` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coreq` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subjects_unique` (`course_id`,`code`,`year_level`,`term`),
  CONSTRAINT `subjects_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,5,'CS101','Introduction to Programming',NULL,1,1,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(2,5,'CS102','Computer Fundamentals',NULL,1,1,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(3,5,'MATH101','College Algebra',NULL,1,1,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(4,5,'CS103','Data Structures and Algorithms',NULL,1,2,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(5,5,'CS104','Web Development Fundamentals',NULL,1,2,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(6,5,'CS201','Database Management Systems',NULL,2,1,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(7,5,'CS202','Object-Oriented Programming',NULL,2,1,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(8,5,'CS203','Network Fundamentals',NULL,2,2,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(9,5,'CS204','Software Engineering',NULL,2,2,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(10,5,'CS301','System Administration',NULL,3,1,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(11,5,'CS302','Mobile Application Development',NULL,3,1,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(12,5,'CS303','Capstone Project',NULL,3,2,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(13,2,'CE101','Digital Logic Design',NULL,1,1,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(14,2,'CE102','Computer Programming I',NULL,1,1,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(15,2,'MATH101','Engineering Mathematics I',NULL,1,1,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(16,2,'CE103','Circuit Analysis',NULL,1,2,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(17,2,'CE201','Microprocessor Systems',NULL,2,1,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(18,2,'CE202','Embedded Systems',NULL,2,2,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(19,3,'EE101','Basic Electronics',NULL,1,1,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(20,3,'EE102','Electrical Circuits I',NULL,1,1,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(21,3,'EE103','Power Systems Fundamentals',NULL,1,2,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(22,3,'EE201','Industrial Electronics',NULL,2,1,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(23,3,'EE202','Electrical Machines',NULL,2,2,0,0,0,0,NULL,NULL,'2025-11-12 03:15:05','2025-11-12 03:15:05'),(24,9,'CHEM 015','Chemistry for Engineers','Chemistry for Engineers',1,1,3,3,4,6,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(25,9,'CMPE 102','Programming Logic and Design','Programming Logic and Design',1,1,0,6,2,6,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(26,9,'CMPE 105','Computer Hardware Fundamentals','Computer Hardware Fundamentals',1,1,0,6,2,6,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(27,9,'CPET 102','Web Technology and Programming','Web Technology and Programming',1,1,2,3,3,5,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(28,9,'ENSC 013','Engineering Drawing','Engineering Drawing',1,1,0,6,2,6,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(29,9,'MATH 101','Calculus 1','Calculus 1',1,1,3,0,3,3,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(30,9,'NSTP 001','National Service Training Program 1','National Service Training Program 1',1,1,3,0,3,0,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(31,9,'PATHFIT 1','Physical Activity Towards Health and Fitness 1','Physical Activity Towards Health and Fitness 1',1,1,2,0,2,2,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(32,9,'CMPE 103','Object Oriented Programming','Object Oriented Programming',1,2,0,6,2,6,'CMPE 102','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(33,9,'CPET 101','Visual Graphic Design','Visual Graphic Design',1,2,0,6,2,6,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(34,9,'CPET 103','Web Technology and Programming 2','Web Technology and Programming 2',1,2,2,3,3,5,'CPET 102','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(35,9,'ENSC 014','Computer-Aided Drafting','Computer-Aided Drafting',1,2,0,3,1,3,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(36,9,'GEED 005','Purposive Communication/Malayuning Komunikasyon','Purposive Communication/Malayuning Komunikasyon',1,2,3,0,3,3,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(37,9,'MATH 103','Calculus 2','Calculus 2',1,2,3,0,3,3,'MATH 101','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(38,9,'NSTP 002','National Service Training Program 2','National Service Training Program 2',1,2,3,0,3,0,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(39,9,'PATHFIT 2','Physical Activity Towards Health and Fitness 2','Physical Activity Towards Health and Fitness 2',1,2,2,0,2,2,'PATHFIT 1','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(40,9,'PHYS 013','Physics for Engineers (Calculus-based)','Physics for Engineers (Calculus-based)',1,2,3,3,4,6,'CHEM 015','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(41,9,'CMPE 101','Computer Engineering as a Discipline','Computer Engineering as a Discipline',2,1,1,0,1,1,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(42,9,'CMPE 104','Discrete Mathematics','Discrete Mathematics',2,1,3,0,3,3,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(43,9,'CMPE 201','Data Structures and Algorithms','Data Structures and Algorithms',2,1,0,6,2,6,'CMPE 103, CMPE 102','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(44,9,'CMPE-PC1','CPE Professional Course 1','CPE Professional Course 1',2,1,2,3,3,5,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(45,9,'CPET 201','2D Animation','2D Animation',2,1,0,6,2,6,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(46,9,'ECEN 011','Fundamentals of Electronic Circuits','Fundamentals of Electronic Circuits',2,1,3,3,4,6,'MATH 103','ELEN 012','2025-11-12 04:30:54','2025-11-12 04:30:54'),(47,9,'ELEN 012','Fundamentals of Electrical Circuits','Fundamentals of Electrical Circuits',2,1,3,3,4,6,'MATH 103, PHYS 013','ECEN 011','2025-11-12 04:30:54','2025-11-12 04:30:54'),(48,9,'GEED 032','Filipinolohiya at Pambansang Kaunlaran','Filipinolohiya at Pambansang Kaunlaran',2,1,3,0,3,3,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(49,9,'PATHFIT 3','Physical Activity Towards Health and Fitness 3','Physical Activity Towards Health and Fitness 3',2,1,2,0,2,2,'PATHFIT 2','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(50,9,'CMPE 202','Operating Systems','Operating Systems',2,2,3,0,3,3,'CMPE 201','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(51,9,'CMPE 304','Logic Circuits and Design','Logic Circuits and Design',2,2,3,3,4,6,'ECEN 011','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(52,9,'CMPE 306','Fundamentals of Mixed Signals and Sensors','Fundamentals of Mixed Signals and Sensors',2,2,3,0,3,3,'ECEN 011','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(53,9,'CMPE 401','Database Management Systems','Database Management Systems',2,2,0,6,2,6,'CMPE 201','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(54,9,'CMPE-PC2','CPE Professional Course 2','CPE Professional Course 2',2,2,2,3,3,5,'CMPE-PC1','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(55,9,'CPET 202','Computer Programming (JAVA)','Computer Programming (JAVA)',2,2,0,6,2,6,'CMPE 103','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(56,9,'GEED 008','Ethics/Etika','Ethics/Etika',2,2,3,0,3,3,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(57,9,'PATHFIT 4','Physical Activity Towards Health and Fitness 4','Physical Activity Towards Health and Fitness 4',2,2,2,0,2,2,'PATHFIT 3','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(58,9,'STAT 012','Engineering Data Analysis','Engineering Data Analysis',2,2,3,0,3,3,'MATH 101','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(59,9,'CPET 203','Practicum 1 (300 hours)','Practicum 1 (300 hours)',2,3,1,6,3,7,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(60,9,'CMPE 303','Computer Engineering Drafting and Design','Computer Engineering Drafting and Design',3,1,0,3,1,3,'ECEN 011','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(61,9,'CMPE 305','Data and Digital Communications','Data and Digital Communications',3,1,3,3,4,6,'ECEN 011','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(62,9,'CMPE 308','CPE Laws and Professional Practice','CPE Laws and Professional Practice',3,1,2,0,2,2,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(63,9,'CMPE 311','Microprocessors','Microprocessors',3,1,3,3,4,6,'CMPE 304','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(64,9,'CMPE-PC3','CPE Professional Course 3','CPE Professional Course 3',3,1,2,3,3,5,'CMPE-PC1, CMPE-PC2','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(65,9,'CPET 301','CPET Project Development 1','CPET Project Development 1',3,1,0,6,2,6,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(66,9,'CPET 302','Database Management System 2','Database Management System 2',3,1,0,6,2,6,'CMPE 401','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(67,9,'ENGL 012','Technical Communication','Technical Communication',3,1,3,0,3,3,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(68,9,'CPET 303','Practicum 2 (300 hours)','Practicum 2 (300 hours)',3,2,1,6,3,7,'CPET 203','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(69,9,'CPET 304','Seminars on Issues and Trends in CPET','Seminars on Issues and Trends in CPET',3,2,0,6,2,6,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(70,9,'CPET 305','CPET Project Development 2','CPET Project Development 2',3,2,0,6,2,6,'CPET 301','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(71,9,'ENSC 029','Technopreneurship 101','Technopreneurship 101',3,2,3,0,3,3,'','','2025-11-12 04:30:54','2025-11-12 04:30:54'),(72,4,'CHEM 015','Chemistry for Engineers','Chemistry for Engineers',1,1,3,3,4,6,NULL,NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(73,4,'ECEN 101','Basic Electronics 1','Basic Electronics 1',1,1,0,6,2,6,NULL,NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(74,4,'ECET 101','Consumer Electronics Servicing 1','Consumer Electronics Servicing 1',1,1,0,6,2,6,NULL,NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(75,4,'ENSC 013','Engineering Drawing','Engineering Drawing',1,1,0,6,2,6,NULL,NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(76,4,'GEED 005','Purposive Communication/Malayuning Komunikasyon','Purposive Communication/Malayuning Komunikasyon',1,1,3,0,3,3,NULL,NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(77,4,'GEED 008','Ethics/Etika','Ethics/Etika',1,1,3,0,3,3,NULL,NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(78,4,'MATH 101','Calculus 1','Calculus 1',1,1,3,0,3,3,NULL,NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(79,4,'NSTP 001','National Service Training Program 1','National Service Training Program 1',1,1,3,0,3,0,NULL,NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(80,4,'PATHFIT 1','Physical Activity Towards Health and Fitness 1','Physical Activity Towards Health and Fitness 1',1,1,2,0,2,2,NULL,NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(81,4,'ECEN 102','Basic Electronics 2','Basic Electronics 2',1,2,0,6,2,6,'ECEN 101','ECEN 201','2025-11-12 07:46:43','2025-11-12 07:46:43'),(82,4,'ECEN 201','Electronics 1: Electronic Devices and Circuits Theory','Electronics 1: Electronic Devices and Circuits Theory',1,2,3,3,4,6,NULL,NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(83,4,'ECET 102','Consumer Electronics Servicing 2','Consumer Electronics Servicing 2',1,2,2,3,3,5,'ECET 101',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(84,4,'ENGL 012','Technical Communication','Technical Communication',1,2,3,0,3,3,NULL,NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(85,4,'ENSC 014','Computer-Aided Drafting','Computer-Aided Drafting',1,2,0,3,1,3,'ENSC 013',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(86,4,'MATH 103','Calculus 2','Calculus 2',1,2,3,0,3,3,'MATH 101',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(87,4,'NSTP 002','National Service Training Program 2','National Service Training Program 2',1,2,3,0,3,0,NULL,NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(88,4,'PATHFIT 2','Physical Activity Towards Health and Fitness 2','Physical Activity Towards Health and Fitness 2',1,2,2,0,2,2,'PATHFIT 1',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(89,4,'PHYS 013','Physics for Engineers (Calculus-based)','Physics for Engineers (Calculus-based)',1,2,3,3,4,6,'CHEM 015',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(90,4,'CMPE 012','Computer Programming','Computer Programming',2,1,0,6,2,6,NULL,NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(91,4,'ECEN 202','Communications 1: Principles of Communication Systems','Communications 1: Principles of Communication Systems',2,1,3,3,4,6,'ECEN 201',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(92,4,'ECEN 204','Electronics 2: Electronic Circuit Analysis and Design','Electronics 2: Electronic Circuit Analysis and Design',2,1,3,3,4,6,'ECEN 201','ELEN 013','2025-11-12 07:46:43','2025-11-12 07:46:43'),(93,4,'ECET 201','Computer Maintenance and Repair','Computer Maintenance and Repair',2,1,2,3,3,5,'ECEN 102',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(94,4,'ELEN 013','Circuits 1','Circuits 1',2,1,3,3,4,6,NULL,'ECEN 204, PHYS 107','2025-11-12 07:46:43','2025-11-12 07:46:43'),(95,4,'PATHFIT 3','Physical Activity Towards Health and Fitness 3','Physical Activity Towards Health and Fitness 3',2,1,2,0,2,2,'PATHFIT 2',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(96,4,'PHYS 107','Physics 2','Physics 2',2,1,3,3,4,6,NULL,'ELEN 013','2025-11-12 07:46:43','2025-11-12 07:46:43'),(97,4,'STAT 012','Engineering Data Analysis','Engineering Data Analysis',2,1,3,0,3,3,'MATH 103',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(98,4,'ECEN 205','Digital Electronics 1: Logic Circuits and Switching Theory','Digital Electronics 1: Logic Circuits and Switching Theory',2,2,3,3,4,6,'ECEN 201',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(99,4,'ECEN 310','Electronics 3: Electronic Systems and Design','Electronics 3: Electronic Systems and Design',2,2,3,3,4,6,'ECEN 204',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(100,4,'ECET 202','AM/FM Broadcast System and Receivers','AM/FM Broadcast System and Receivers',2,2,2,3,3,5,'ECEN 202','ECET 203','2025-11-12 07:46:43','2025-11-12 07:46:43'),(101,4,'ECET 203','Television Broadcast System and Receivers','Television Broadcast System and Receivers',2,2,0,6,2,6,NULL,'ECET 202','2025-11-12 07:46:43','2025-11-12 07:46:43'),(102,4,'ECET 204','ECET Project Development 1','ECET Project Development 1',2,2,0,6,2,6,NULL,NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(103,4,'GEED 032','Filipinolohiya at Pambansang Kaunlaran','Filipinolohiya at Pambansang Kaunlaran',2,2,3,0,3,3,NULL,NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(104,4,'MATH 209','Differential Equations','Differential Equations',2,2,3,0,3,3,NULL,NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(105,4,'PATHFIT 4','Physical Activity Towards Health and Fitness 4','Physical Activity Towards Health and Fitness 4',2,2,2,0,2,2,'PATHFIT 3',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(106,4,'ECET 205','Practicum 1 (300 hours)','Practicum 1 (300 hours)',2,3,1,6,3,7,NULL,NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(107,4,'ECEN 206','Communications 2: Modulation & Coding Techniques','Communications 2: Modulation & Coding Techniques',3,1,3,3,4,6,'ECEN 202',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(108,4,'ECET 301','Mechatronics','Mechatronics',3,1,0,6,2,6,'ECEN 102',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(109,4,'ECET 302','Industrial Electronics','Industrial Electronics',3,1,2,3,3,5,'ECEN 204',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(110,4,'ECET 303','Photovoltaic (PV) Installation Design and Maintenance','Photovoltaic (PV) Installation Design and Maintenance',3,1,2,3,3,5,'ECET 101',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(111,4,'ECET 304','ECET Project Development 2','ECET Project Development 2',3,1,0,6,2,6,'ECET 204',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(112,4,'ELEN 014','Circuits 2','Circuits 2',3,1,3,3,4,6,'MATH 209, ELEN 013',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(113,4,'ENSC 027','Materials Science & Engineering','Materials Science & Engineering',3,1,3,0,3,3,'CHEM 015',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(114,4,'ENSC 029','Technopreneurship 101','Technopreneurship 101',3,1,3,0,3,3,NULL,NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(115,4,'ECET 305','Practicum 2 (300 hours)','Practicum 2 (300 hours)',3,2,1,6,3,7,'ECET 205',NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43'),(116,4,'ECET 306','Seminars, Issues and Trends in ECET','Seminars, Issues and Trends in ECET',3,2,0,6,2,6,NULL,NULL,'2025-11-12 07:46:43','2025-11-12 07:46:43');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'faculty',
  `course_id` bigint unsigned DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_team_id` bigint unsigned DEFAULT NULL,
  `profile_photo_path` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_course_id_foreign` (`course_id`),
  CONSTRAINT `users_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Jomar B. Ruiz','jbruiz@pup.edu.ph','faculty',NULL,NULL,'$2y$12$wIg2kQxNFXX1C7TrAia1nezXM1nm6zU4SYiITe1bN4F3WSumfzR0m',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-12 02:41:48','2025-11-12 04:47:10'),(2,'Admin User','admin@example.com','admin',NULL,NULL,'$2y$12$ErKOZqmutf/4lIm31/FeGeGJrnQ2PRtHo63jfTGVzXfMfpXCXhbdO',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-12 03:15:06','2025-11-12 03:15:06'),(3,'Chair DICT','chair.dict@example.com','chair',NULL,NULL,'$2y$12$oRwHig2QRlIBHxepoV.HSO2JByXnfE2FwFT2EIUaq2jE.kPgZY6.S',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-12 03:15:06','2025-11-12 03:15:06'),(4,'Faculty One','faculty1@example.com','faculty',NULL,NULL,'$2y$12$im8hsKd36tQ8KISwiOPL/Of60GiAbwpzkmXqk2GKVMy5Kyazg.afe',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-12 03:15:06','2025-11-12 03:15:06'),(5,'Auditor','auditor@example.com','auditor',NULL,NULL,'$2y$12$yjaWk2x2VSvJ8FVi7l8IvOHoTMmWlPMYIi.zV6fa0IOl2NZFqTfzW',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-12 03:15:06','2025-11-12 03:15:06'),(6,'Frescian C. Ruiz','chair.dcpet@pup.edu.ph','chair',9,NULL,'$2y$12$9T1I8e1bpyXCngPoOTJ.UudRUa/uNm8RM2Ckn9B3LOcdXRG5Yc5bW',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-12 04:35:26','2025-11-12 04:44:05'),(7,'Roel D. Cabrera','rdcabrera@pup.edu.ph','faculty',NULL,NULL,'$2y$12$cfzmZNHYME44YPuJR58xse8lDIZkQQ5On8KZbA4oeeLqre/T8TRc2',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-12 05:48:24','2025-11-12 05:48:24'),(8,'Joseph Lequigan','jblequigan@pup.edu.ph','faculty',NULL,NULL,'$2y$12$GbUYBt7DY9XmlLccjkuJ4OlF8N6G/kfb3trPMT/GukMEDB9Ma/bQ2',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-12 05:49:11','2025-11-12 05:49:11'),(9,'Jerome De Guzman','jtdeguzman@pup.edu.ph','faculty',NULL,NULL,'$2y$12$ZPdAyabaRYVK9WPt6/bMqu987wFsaOao8JgWvRumkFvI7NAUWQdcO',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-12 06:34:22','2025-11-12 06:34:22'),(10,'Jonathan C. Manarang','jcmanarang@pup.edu.ph','faculty',NULL,NULL,'$2y$12$eSQ0xBU4xHY/XFIZ/QpQA.wf4yyjJORwPJB5w1HkaUE4KW.0xlrpW',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-12 06:37:06','2025-11-12 06:37:06'),(11,'Isaiah Nikkolai M. Andaya','inmandaya@pup.edu.ph','faculty',NULL,NULL,'$2y$12$3NVGlO9E6cF89WVqlAHHKuYzeg5YGRQZ0X.JKaE2NT5pN5CvRIoS.',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-12 06:42:59','2025-11-12 06:42:59'),(12,'Jake M. Libed','jmlibed@pup.edu.ph','faculty',NULL,NULL,'$2y$12$.QqT6anBwlkRYxivFCuMLuWyGZ2rsq1tF2fLiBnwWh1CLXEZ9out6',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-12 06:43:44','2025-11-12 06:43:44'),(13,'Ronald D. Fernando','rdfernando@pup.edu.ph','faculty',NULL,NULL,'$2y$12$wpJ.QGh3cgTAk.Nk7SVXVeMg9HQ.kbtijMsbjfAX9NhygWuK7KMfS',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-12 06:44:20','2025-11-12 06:44:20'),(14,'John Michael V. Legaspi','jmvlegaspi@pup.edu.ph','faculty',NULL,NULL,'$2y$12$Emvlwgv0/LjmjrQR/W7ofe3iJ1S1sW9ndYRo1lI9pqfnlNUk0/JN.',NULL,NULL,NULL,'YZFl4MaSRNfaIL7VGXOyA0SeiXczusiMXtREqnhunkNVpckpNki4C3nT4pNv',NULL,NULL,'2025-11-12 06:46:03','2025-11-12 06:46:03'),(15,'Jose Marie B. Dipay','jmbdipay@pup.edu.ph','faculty',NULL,NULL,'$2y$12$MlDPrTF098boupAApQ8sze.QiUKYCXf3CU5fzhrO4C4vzT/zOzsrW',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-12 06:46:40','2025-11-12 06:46:40'),(16,'Ryan Evangelista','rsevangelista@pup.edu.ph','faculty',NULL,NULL,'$2y$12$pr6amqY9xjpVu.htwxHXsOyTyHg5USkvxWvQ6QhwrtQdh5wH09mOO',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-12 07:02:57','2025-11-12 07:02:57'),(17,'Kenneth Dazon','kpdazon@pup.edu.ph','faculty',NULL,NULL,'$2y$12$WRJ7dy5hdockUb.Z7VS2lu8LmkY7b8EYHalEagNtWyYKUsjBgofuC',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-12 07:19:12','2025-11-12 07:19:12'),(18,'Remegio Rios','rcrios@pup.edu.ph','faculty',NULL,NULL,'$2y$12$29DqGQKN6r0407c2indm8eAg9UbsYcZ8N.2aCZBa8KVAMxsw1d5le',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-13 01:25:49','2025-11-13 01:25:49');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-12 10:37:52
