-- MySQL dump 10.13  Distrib 8.4.11, for Linux (x86_64)
--
-- Host: localhost    Database: expense_manager
-- ------------------------------------------------------
-- Server version	8.4.11-0ubuntu0.26.04.1

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
-- Table structure for table `budgets`
--

DROP TABLE IF EXISTS `budgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `budgets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_budget_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `budgets`
--

LOCK TABLES `budgets` WRITE;
/*!40000 ALTER TABLE `budgets` DISABLE KEYS */;
INSERT INTO `budgets` VALUES (1,1,21825852.00,'2026-10-11 20:21:14','2026-10-11 20:59:43',NULL),(9,11,0.00,'2026-10-11 21:00:45','2026-10-11 21:00:45',NULL);
/*!40000 ALTER TABLE `budgets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('income','expense') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_category_user` (`user_id`),
  CONSTRAINT `fk_category_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (4,1,'test','expense','2026-10-11 20:47:50','2026-10-11 20:59:36',NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `version` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (7);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `remember_tokens`
--

DROP TABLE IF EXISTS `remember_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `remember_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `selector` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `selector` (`selector`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `remember_tokens`
--

LOCK TABLES `remember_tokens` WRITE;
/*!40000 ALTER TABLE `remember_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `remember_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `category_id` int unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `type` enum('income','expense') COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_transaction_user` (`user_id`),
  KEY `fk_transaction_category` (`category_id`),
  CONSTRAINT `fk_transaction_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_transaction_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES (1,1,NULL,'Expense transaction',3115733.00,'expense','2026-09-27','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(2,1,NULL,'Income transaction',3906013.00,'income','2026-09-28','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(3,1,NULL,'Expense transaction',551995.00,'expense','2026-10-01','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(4,1,NULL,'Expense transaction',3530112.00,'expense','2026-10-06','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(5,1,NULL,'Income transaction',4724402.00,'income','2026-09-18','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(6,1,NULL,'Income transaction',4744719.00,'income','2026-10-04','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(7,1,NULL,'Expense transaction',2963478.00,'expense','2026-10-01','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(8,1,NULL,'Expense transaction',1466524.00,'expense','2026-09-23','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(9,1,NULL,'Expense transaction',3691030.00,'expense','2026-09-30','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(10,1,NULL,'Expense transaction',2917855.00,'expense','2026-10-01','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(11,1,NULL,'Income transaction',4612118.00,'income','2026-09-30','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(12,1,NULL,'Income transaction',663844.00,'income','2026-09-14','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(13,1,NULL,'Income transaction',3607382.00,'income','2026-09-15','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(14,1,NULL,'Income transaction',171936.00,'income','2026-09-21','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(15,1,NULL,'Expense transaction',2575101.00,'expense','2026-09-21','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(16,1,NULL,'Expense transaction',4618344.00,'expense','2026-09-14','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(17,1,NULL,'Income transaction',4492258.00,'income','2026-10-09','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(18,1,NULL,'Income transaction',295697.00,'income','2026-10-04','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(19,1,NULL,'Income transaction',4719609.00,'income','2026-09-27','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(20,1,NULL,'Expense transaction',919147.00,'expense','2026-09-25','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(21,1,NULL,'Expense transaction',1314378.00,'expense','2026-09-16','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(22,1,NULL,'Expense transaction',2141307.00,'expense','2026-10-03','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(23,1,NULL,'Income transaction',1908145.00,'income','2026-10-11','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(24,1,NULL,'Income transaction',102736.00,'income','2026-09-25','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(25,1,NULL,'Expense transaction',1913149.00,'expense','2026-09-13','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(26,1,NULL,'Income transaction',2091682.00,'income','2026-09-19','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(27,1,NULL,'Income transaction',1606255.00,'income','2026-09-30','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(28,1,NULL,'Expense transaction',1459041.00,'expense','2026-10-05','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(29,1,NULL,'Expense transaction',2593317.00,'expense','2026-09-22','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(30,1,NULL,'Income transaction',2448894.00,'income','2026-09-16','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(31,1,NULL,'Expense transaction',4081843.00,'expense','2026-09-18','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(32,1,NULL,'Income transaction',3121876.00,'income','2026-10-02','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(33,1,NULL,'Expense transaction',2140966.00,'expense','2026-10-03','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(34,1,NULL,'Income transaction',1165363.00,'income','2026-09-30','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(35,1,NULL,'Income transaction',4993621.00,'income','2026-09-30','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(36,1,NULL,'Expense transaction',120162.00,'expense','2026-09-25','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(37,1,NULL,'Expense transaction',1155000.00,'expense','2026-10-01','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(38,1,NULL,'Income transaction',3891813.00,'income','2026-10-02','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(39,1,NULL,'Income transaction',699611.00,'income','2026-09-16','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(40,1,NULL,'Expense transaction',1911979.00,'expense','2026-09-23','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(41,1,NULL,'Expense transaction',3068358.00,'expense','2026-09-24','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(42,1,NULL,'Expense transaction',4938044.00,'expense','2026-10-08','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(43,1,NULL,'Expense transaction',779775.00,'expense','2026-09-18','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(44,1,NULL,'Income transaction',3639965.00,'income','2026-10-03','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(45,1,NULL,'Expense transaction',4660972.00,'expense','2026-10-07','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(46,1,NULL,'Expense transaction',4840078.00,'expense','2026-10-11','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(47,1,NULL,'Income transaction',2100309.00,'income','2026-10-02','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(48,1,NULL,'Expense transaction',1943841.00,'expense','2026-10-10','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(49,1,NULL,'Income transaction',4165045.00,'income','2026-10-06','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(50,1,NULL,'Income transaction',1364088.00,'income','2026-09-28','Random generated transaction','2026-10-11 16:51:26','2026-10-11 16:51:26',NULL),(51,1,NULL,'salary',25000000.00,'income','2026-09-13','','2026-10-11 20:21:54','2026-10-11 20:21:54',NULL),(58,1,4,'test',2000000.00,'expense','2026-10-11','','2026-10-11 20:48:12','2026-10-11 20:59:43',NULL),(59,1,4,'test',1000000.00,'expense','2026-10-11','','2026-10-11 20:59:12','2026-10-11 20:59:42',NULL);
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `full_name_normalized` varchar(100) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS (trim(`full_name`)) STORED,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `full_name_normalized` (`full_name_normalized`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `avatar`, `created_at`, `updated_at`, `deleted_at`) VALUES (1,'Amirreza Eft','ammir.riza@gmail.com','$2y$12$ikCti.ZiWOIGjrsNGiczGOY8Le3WRUjGs67ShiExi8YU4TErVpX72',NULL,'2026-10-11 16:51:14','2026-10-11 17:33:54',NULL),(11,'AmirrezaEft','ammir.rizaa@gmail.com','$2y$12$7CkZLzdPKTYgVUJB5CUt4ODixbHAuPhIB5HEX9gT1ReKm1KEOjz86',NULL,'2026-10-11 17:30:45','2026-10-11 21:00:45',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'expense_manager'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-11 21:05:57
