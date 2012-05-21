-- MySQL dump 10.13  Distrib 5.1.62, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: coop
-- ------------------------------------------------------
-- Server version	5.1.62-0ubuntu0.10.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `coop_addresses`
--

DROP TABLE IF EXISTS `coop_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coop_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` text,
  `city` text,
  `state` text,
  `zipcode` char(5) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `date_mod` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  CONSTRAINT `coop_addresses_ibfk_1` FOREIGN KEY (`username`) REFERENCES `coop_users` (`username`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coop_addresses`
--

LOCK TABLES `coop_addresses` WRITE;
/*!40000 ALTER TABLE `coop_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `coop_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coop_assignments`
--

DROP TABLE IF EXISTS `coop_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coop_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assignment` varchar(100) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `online` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coop_assignments`
--

LOCK TABLES `coop_assignments` WRITE;
/*!40000 ALTER TABLE `coop_assignments` DISABLE KEYS */;
INSERT INTO `coop_assignments` VALUES (1,'Student Info Sheet','2012-04-11',1),(2,'assignment2','2012-06-09',0),(3,'assignment3','2012-05-19',0);
/*!40000 ALTER TABLE `coop_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coop_classes`
--

DROP TABLE IF EXISTS `coop_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coop_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coordinator` varchar(100) DEFAULT NULL COMMENT 'username of coordinator',
  `name` text,
  PRIMARY KEY (`id`),
  KEY `coordinator` (`coordinator`),
  CONSTRAINT `coop_classes_ibfk_1` FOREIGN KEY (`coordinator`) REFERENCES `coop_users` (`username`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coop_classes`
--

LOCK TABLES `coop_classes` WRITE;
/*!40000 ALTER TABLE `coop_classes` DISABLE KEYS */;
INSERT INTO `coop_classes` VALUES (1,NULL,'HUM 193V'),(2,NULL,'SSCI 193V'),(3,NULL,'CENT 293V'),(4,NULL,'AMT 93V');
/*!40000 ALTER TABLE `coop_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `coop_classinfo_view`
--

DROP TABLE IF EXISTS `coop_classinfo_view`;
/*!50001 DROP VIEW IF EXISTS `coop_classinfo_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `coop_classinfo_view` (
  `id` int(11),
  `coordinator` varchar(100),
  `name` text,
  `fname` text,
  `lname` text,
  `email` text
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `coop_coordinators`
--

DROP TABLE IF EXISTS `coop_coordinators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coop_coordinators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  CONSTRAINT `coop_coordinators_ibfk_1` FOREIGN KEY (`username`) REFERENCES `coop_users` (`username`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coop_coordinators`
--

LOCK TABLES `coop_coordinators` WRITE;
/*!40000 ALTER TABLE `coop_coordinators` DISABLE KEYS */;
INSERT INTO `coop_coordinators` VALUES (2,'coord1'),(1,'dcaulfie');
/*!40000 ALTER TABLE `coop_coordinators` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coop_disclaimers`
--

DROP TABLE IF EXISTS `coop_disclaimers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coop_disclaimers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `semesters_id` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `date_agreed` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `semesters_id` (`semesters_id`),
  KEY `username` (`username`),
  CONSTRAINT `coop_disclaimers_ibfk_1` FOREIGN KEY (`semesters_id`) REFERENCES `coop_semesters` (`id`) ON DELETE CASCADE,
  CONSTRAINT `coop_disclaimers_ibfk_2` FOREIGN KEY (`username`) REFERENCES `coop_users` (`username`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coop_disclaimers`
--

LOCK TABLES `coop_disclaimers` WRITE;
/*!40000 ALTER TABLE `coop_disclaimers` DISABLE KEYS */;
/*!40000 ALTER TABLE `coop_disclaimers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coop_employmentinfo`
--

DROP TABLE IF EXISTS `coop_employmentinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coop_employmentinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `classes_id` int(11) DEFAULT NULL,
  `semesters_id` int(11) DEFAULT NULL,
  `current_job` text,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `rate_of_pay` float DEFAULT NULL,
  `department` text,
  `job_address` text,
  `superv_name` text,
  `superv_title` text,
  `superv_phone` text,
  `superv_email` text,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `classes_id` (`classes_id`),
  KEY `semesters_id` (`semesters_id`),
  CONSTRAINT `coop_employmentinfo_ibfk_1` FOREIGN KEY (`username`) REFERENCES `coop_users` (`username`) ON DELETE CASCADE,
  CONSTRAINT `coop_employmentinfo_ibfk_2` FOREIGN KEY (`classes_id`) REFERENCES `coop_classes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `coop_employmentinfo_ibfk_3` FOREIGN KEY (`semesters_id`) REFERENCES `coop_semesters` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coop_employmentinfo`
--

LOCK TABLES `coop_employmentinfo` WRITE;
/*!40000 ALTER TABLE `coop_employmentinfo` DISABLE KEYS */;
/*!40000 ALTER TABLE `coop_employmentinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coop_majors`
--

DROP TABLE IF EXISTS `coop_majors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coop_majors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `major` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coop_majors`
--

LOCK TABLES `coop_majors` WRITE;
/*!40000 ALTER TABLE `coop_majors` DISABLE KEYS */;
INSERT INTO `coop_majors` VALUES (1,'HUM'),(2,'SSCI'),(3,'CENT'),(4,'AMT');
/*!40000 ALTER TABLE `coop_majors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coop_phonenumbers`
--

DROP TABLE IF EXISTS `coop_phonenumbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coop_phonenumbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phonenumber` char(8) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `phonetypes_id` int(11) DEFAULT NULL,
  `date_mod` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `phonetypes_id` (`phonetypes_id`),
  KEY `username` (`username`),
  CONSTRAINT `coop_phonenumbers_ibfk_1` FOREIGN KEY (`phonetypes_id`) REFERENCES `coop_phonetypes` (`id`),
  CONSTRAINT `coop_phonenumbers_ibfk_2` FOREIGN KEY (`username`) REFERENCES `coop_users` (`username`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coop_phonenumbers`
--

LOCK TABLES `coop_phonenumbers` WRITE;
/*!40000 ALTER TABLE `coop_phonenumbers` DISABLE KEYS */;
/*!40000 ALTER TABLE `coop_phonenumbers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coop_phonetypes`
--

DROP TABLE IF EXISTS `coop_phonetypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coop_phonetypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coop_phonetypes`
--

LOCK TABLES `coop_phonetypes` WRITE;
/*!40000 ALTER TABLE `coop_phonetypes` DISABLE KEYS */;
INSERT INTO `coop_phonetypes` VALUES (1,'home'),(2,'mobile');
/*!40000 ALTER TABLE `coop_phonetypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coop_roles`
--

DROP TABLE IF EXISTS `coop_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coop_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coop_roles`
--

LOCK TABLES `coop_roles` WRITE;
/*!40000 ALTER TABLE `coop_roles` DISABLE KEYS */;
INSERT INTO `coop_roles` VALUES (2,'admin'),(3,'coordinator'),(1,'supervisor'),(4,'user');
/*!40000 ALTER TABLE `coop_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coop_semesters`
--

DROP TABLE IF EXISTS `coop_semesters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coop_semesters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `semester` varchar(20) DEFAULT NULL,
  `current` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `semester` (`semester`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coop_semesters`
--

LOCK TABLES `coop_semesters` WRITE;
/*!40000 ALTER TABLE `coop_semesters` DISABLE KEYS */;
INSERT INTO `coop_semesters` VALUES (1,'Spring 2008',0),(2,'Fall 2008',0),(3,'Spring 2009',0),(4,'Fall 2009',0),(5,'Spring 2010',0),(6,'Fall 2010',0),(7,'Spring 2011',0),(8,'Fall 2011',0),(9,'Spring 2012',0),(10,'Fall 2012',0),(11,'Spring 2013',0),(12,'Fall 2013',0),(13,'Spring 2014',0),(14,'Fall 2014',0),(15,'Spring 2015',0),(16,'Fall 2015',0),(17,'Spring 2016',0),(18,'Fall 2016',0),(19,'Spring 2017',0),(20,'Fall 2017',0),(21,'Spring 2018',0),(22,'Fall 2018',0),(23,'Spring 2019',0),(24,'Fall 2019',0),(25,'Spring 2020',0),(26,'Fall 2020',0),(27,'Spring 2021',0),(28,'Fall 2021',0),(29,'Spring 2022',0),(30,'Fall 2022',0),(31,'Spring 2023',0),(32,'Fall 2023',0),(33,'Spring 2024',0),(34,'Fall 2024',0),(35,'Spring 2025',0),(36,'Fall 2025',0),(37,'Spring 2026',0),(38,'Fall 2026',0),(39,'Spring 2027',0),(40,'Fall 2027',0),(41,'Spring 2028',0),(42,'Fall 2028',0),(43,'Spring 2029',0),(44,'Fall 2029',0),(45,'Spring 2030',0),(46,'Fall 2030',0),(47,'Spring 2031',0),(48,'Fall 2031',0),(49,'Spring 2032',0),(50,'Fall 2032',0),(51,'Spring 2033',0),(52,'Fall 2033',0),(53,'Spring 2034',0),(54,'Fall 2034',0),(55,'Spring 2035',0),(56,'Fall 2035',0),(57,'Spring 2036',0),(58,'Fall 2036',0),(59,'Spring 2037',0),(60,'Fall 2037',0),(61,'Spring 2038',0),(62,'Fall 2038',0),(63,'Spring 2039',0),(64,'Fall 2039',0),(65,'Spring 2040',0),(66,'Fall 2040',0),(67,'Spring 2041',0),(68,'Fall 2041',0),(69,'Spring 2042',0),(70,'Fall 2042',0),(71,'Spring 2043',0),(72,'Fall 2043',0),(73,'Spring 2044',0),(74,'Fall 2044',0),(75,'Spring 2045',0),(76,'Fall 2045',0),(77,'Spring 2046',0),(78,'Fall 2046',0),(79,'Spring 2047',0),(80,'Fall 2047',0),(81,'Spring 2048',0),(82,'Fall 2048',0),(83,'Spring 2049',0),(84,'Fall 2049',0),(85,'Spring 2050',0),(86,'Fall 2050',0);
/*!40000 ALTER TABLE `coop_semesters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `coop_studentinfo_view`
--

DROP TABLE IF EXISTS `coop_studentinfo_view`;
/*!50001 DROP VIEW IF EXISTS `coop_studentinfo_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `coop_studentinfo_view` (
  `id` int(11),
  `fname` text,
  `lname` text,
  `uuid` char(8),
  `username` varchar(100),
  `password` varchar(20),
  `email` text,
  `roles_id` int(11),
  `active` tinyint(1),
  `role` varchar(20),
  `phonenumber` char(8),
  `phn_date_mod` datetime,
  `phonetype` text,
  `grad_date` date,
  `semester_in_major` int(11),
  `wanted_job` text,
  `address` text,
  `city` text,
  `state` text,
  `zipcode` char(5),
  `addr_date_mod` datetime,
  `current_job` text,
  `start_date` date,
  `end_date` date,
  `rate_of_pay` float,
  `job_address` text
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `coop_students`
--

DROP TABLE IF EXISTS `coop_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coop_students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `grad_date` date DEFAULT NULL,
  `majors_id` int(11) DEFAULT NULL,
  `semester_in_major` int(11) DEFAULT NULL,
  `coord_name` text,
  `coord_phone` text,
  `wanted_job` text,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `majors_id` (`majors_id`),
  CONSTRAINT `coop_students_ibfk_1` FOREIGN KEY (`username`) REFERENCES `coop_users` (`username`) ON DELETE CASCADE,
  CONSTRAINT `coop_students_ibfk_2` FOREIGN KEY (`majors_id`) REFERENCES `coop_majors` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coop_students`
--

LOCK TABLES `coop_students` WRITE;
/*!40000 ALTER TABLE `coop_students` DISABLE KEYS */;
INSERT INTO `coop_students` VALUES (1,'kuukekoa',NULL,NULL,NULL,NULL,NULL,NULL),(2,'janedoe',NULL,NULL,NULL,NULL,NULL,NULL),(3,'ousley',NULL,NULL,NULL,NULL,NULL,NULL),(4,'johndoe',NULL,NULL,NULL,NULL,NULL,NULL),(5,'barclay',NULL,NULL,NULL,NULL,NULL,NULL),(6,'oliva',NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `coop_students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coop_submittedassignments`
--

DROP TABLE IF EXISTS `coop_submittedassignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coop_submittedassignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assignments_id` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `semesters_id` int(11) DEFAULT NULL,
  `classes_id` int(11) DEFAULT NULL,
  `date_submitted` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assignments_id` (`assignments_id`),
  KEY `username` (`username`),
  KEY `semesters_id` (`semesters_id`),
  KEY `classes_id` (`classes_id`),
  CONSTRAINT `coop_submittedassignments_ibfk_1` FOREIGN KEY (`assignments_id`) REFERENCES `coop_assignments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `coop_submittedassignments_ibfk_2` FOREIGN KEY (`username`) REFERENCES `coop_users` (`username`) ON DELETE CASCADE,
  CONSTRAINT `coop_submittedassignments_ibfk_3` FOREIGN KEY (`semesters_id`) REFERENCES `coop_semesters` (`id`),
  CONSTRAINT `coop_submittedassignments_ibfk_4` FOREIGN KEY (`classes_id`) REFERENCES `coop_classes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coop_submittedassignments`
--

LOCK TABLES `coop_submittedassignments` WRITE;
/*!40000 ALTER TABLE `coop_submittedassignments` DISABLE KEYS */;
/*!40000 ALTER TABLE `coop_submittedassignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coop_supervisors`
--

DROP TABLE IF EXISTS `coop_supervisors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coop_supervisors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  CONSTRAINT `coop_supervisors_ibfk_1` FOREIGN KEY (`username`) REFERENCES `coop_users` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coop_supervisors`
--

LOCK TABLES `coop_supervisors` WRITE;
/*!40000 ALTER TABLE `coop_supervisors` DISABLE KEYS */;
INSERT INTO `coop_supervisors` VALUES (1,'superv1'),(2,'superv2'),(3,'superv3');
/*!40000 ALTER TABLE `coop_supervisors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coop_syllabuses`
--

DROP TABLE IF EXISTS `coop_syllabuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coop_syllabuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `classes_id` int(11) DEFAULT NULL,
  `syllabus` text,
  `final` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `classes_id` (`classes_id`),
  CONSTRAINT `coop_syllabuses_ibfk_1` FOREIGN KEY (`classes_id`) REFERENCES `coop_classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coop_syllabuses`
--

LOCK TABLES `coop_syllabuses` WRITE;
/*!40000 ALTER TABLE `coop_syllabuses` DISABLE KEYS */;
INSERT INTO `coop_syllabuses` VALUES (1,1,'Syllabus for HUM 193V',1),(2,2,'Syllabus for SSCI 193V',1),(3,3,'Syllabus for CENT 293V',1),(4,4,'Syllabus for AMT 93V',1);
/*!40000 ALTER TABLE `coop_syllabuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `coop_userrole_view`
--

DROP TABLE IF EXISTS `coop_userrole_view`;
/*!50001 DROP VIEW IF EXISTS `coop_userrole_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `coop_userrole_view` (
  `id` int(11),
  `fname` text,
  `lname` text,
  `uuid` char(8),
  `username` varchar(100),
  `password` varchar(20),
  `email` text,
  `roles_id` int(11),
  `active` tinyint(1),
  `role` varchar(20)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `coop_users`
--

DROP TABLE IF EXISTS `coop_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coop_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` text,
  `lname` text,
  `uuid` char(8) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(20) DEFAULT NULL,
  `email` text,
  `roles_id` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `user` (`username`),
  KEY `roles_id` (`roles_id`),
  CONSTRAINT `coop_users_ibfk_1` FOREIGN KEY (`roles_id`) REFERENCES `coop_roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coop_users`
--

LOCK TABLES `coop_users` WRITE;
/*!40000 ALTER TABLE `coop_users` DISABLE KEYS */;
INSERT INTO `coop_users` VALUES (1,'Joseph','Workman',NULL,'kuukekoa','pass',NULL,4,0),(2,'Jane','Doe',NULL,'janedoe','pass',NULL,4,0),(3,'Becky','Ousley',NULL,'ousley','pass',NULL,4,0),(4,'John','Doe',NULL,'johndoe','pass',NULL,4,0),(5,'Timothy','Barclay',NULL,'barclay','pass',NULL,4,0),(6,'Anne','Oliva',NULL,'oliva','pass',NULL,4,0),(7,'Diane','Caulfield',NULL,'dcaulfie','pass',NULL,3,0),(8,'coord1','coord1',NULL,'coord1','pass',NULL,3,0),(11,'Travis','Toka',NULL,'toka','pass',NULL,3,0),(12,'superv1','superv1',NULL,'superv1','pass',NULL,1,0),(13,'superv2','superv2',NULL,'superv2','pass',NULL,1,0),(14,'superv3','superv3',NULL,'superv3','pass',NULL,1,0),(17,'coord2','coord2',NULL,'coord2',NULL,NULL,3,0);
/*!40000 ALTER TABLE `coop_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coop_users_semesters`
--

DROP TABLE IF EXISTS `coop_users_semesters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coop_users_semesters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student` varchar(100) DEFAULT NULL,
  `semesters_id` int(11) DEFAULT NULL,
  `classes_id` int(11) DEFAULT NULL,
  `coordinator` varchar(100) DEFAULT NULL,
  `supervisor` varchar(100) DEFAULT NULL,
  `credits` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student` (`student`),
  KEY `semesters_id` (`semesters_id`),
  KEY `classes_id` (`classes_id`),
  KEY `coordinator` (`coordinator`),
  KEY `supervisor` (`supervisor`),
  CONSTRAINT `coop_users_semesters_ibfk_1` FOREIGN KEY (`student`) REFERENCES `coop_users` (`username`) ON DELETE CASCADE,
  CONSTRAINT `coop_users_semesters_ibfk_2` FOREIGN KEY (`semesters_id`) REFERENCES `coop_semesters` (`id`),
  CONSTRAINT `coop_users_semesters_ibfk_3` FOREIGN KEY (`classes_id`) REFERENCES `coop_classes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `coop_users_semesters_ibfk_4` FOREIGN KEY (`coordinator`) REFERENCES `coop_users` (`username`) ON DELETE SET NULL,
  CONSTRAINT `coop_users_semesters_ibfk_5` FOREIGN KEY (`supervisor`) REFERENCES `coop_users` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coop_users_semesters`
--

LOCK TABLES `coop_users_semesters` WRITE;
/*!40000 ALTER TABLE `coop_users_semesters` DISABLE KEYS */;
INSERT INTO `coop_users_semesters` VALUES (1,'kuukekoa',1,3,'dcaulfie',NULL,12),(2,'johndoe',3,1,NULL,NULL,12),(3,'johndoe',2,1,'coord1',NULL,12),(4,'johndoe',1,2,NULL,NULL,12),(5,'janedoe',2,2,NULL,NULL,11),(6,'ousley',1,3,NULL,NULL,10);
/*!40000 ALTER TABLE `coop_users_semesters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `coop_users_semesters_view`
--

DROP TABLE IF EXISTS `coop_users_semesters_view`;
/*!50001 DROP VIEW IF EXISTS `coop_users_semesters_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `coop_users_semesters_view` (
  `id` int(11),
  `fname` text,
  `lname` text,
  `uuid` char(8),
  `username` varchar(100),
  `password` varchar(20),
  `email` text,
  `roles_id` int(11),
  `active` tinyint(1),
  `semesters_id` int(11),
  `classes_id` int(11),
  `credits` int(11),
  `student` varchar(100),
  `coordinator` varchar(100),
  `semester` varchar(20),
  `current` tinyint(1),
  `class` text,
  `coordfname` text,
  `coordlname` text
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `coop_classinfo_view`
--

/*!50001 DROP TABLE IF EXISTS `coop_classinfo_view`*/;
/*!50001 DROP VIEW IF EXISTS `coop_classinfo_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `coop_classinfo_view` AS select `c`.`id` AS `id`,`c`.`coordinator` AS `coordinator`,`c`.`name` AS `name`,`u`.`fname` AS `fname`,`u`.`lname` AS `lname`,`u`.`email` AS `email` from (`coop_classes` `c` left join `coop_users` `u` on((`c`.`coordinator` = `u`.`username`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `coop_studentinfo_view`
--

/*!50001 DROP TABLE IF EXISTS `coop_studentinfo_view`*/;
/*!50001 DROP VIEW IF EXISTS `coop_studentinfo_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `coop_studentinfo_view` AS select `u`.`id` AS `id`,`u`.`fname` AS `fname`,`u`.`lname` AS `lname`,`u`.`uuid` AS `uuid`,`u`.`username` AS `username`,`u`.`password` AS `password`,`u`.`email` AS `email`,`u`.`roles_id` AS `roles_id`,`u`.`active` AS `active`,`r`.`role` AS `role`,`pn`.`phonenumber` AS `phonenumber`,`pn`.`date_mod` AS `phn_date_mod`,`pt`.`type` AS `phonetype`,`st`.`grad_date` AS `grad_date`,`st`.`semester_in_major` AS `semester_in_major`,`st`.`wanted_job` AS `wanted_job`,`ad`.`address` AS `address`,`ad`.`city` AS `city`,`ad`.`state` AS `state`,`ad`.`zipcode` AS `zipcode`,`ad`.`date_mod` AS `addr_date_mod`,`em`.`current_job` AS `current_job`,`em`.`start_date` AS `start_date`,`em`.`end_date` AS `end_date`,`em`.`rate_of_pay` AS `rate_of_pay`,`em`.`job_address` AS `job_address` from ((((((`coop_users` `u` left join `coop_addresses` `ad` on((`u`.`username` = `ad`.`username`))) left join `coop_phonenumbers` `pn` on((`u`.`username` = `pn`.`username`))) left join `coop_phonetypes` `pt` on((`pn`.`phonetypes_id` = `pt`.`id`))) left join `coop_students` `st` on((`u`.`username` = `st`.`username`))) left join `coop_employmentinfo` `em` on((`u`.`username` = `em`.`username`))) left join `coop_roles` `r` on((`u`.`roles_id` = `r`.`id`))) where (`r`.`role` = 'user') */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `coop_userrole_view`
--

/*!50001 DROP TABLE IF EXISTS `coop_userrole_view`*/;
/*!50001 DROP VIEW IF EXISTS `coop_userrole_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `coop_userrole_view` AS select `u`.`id` AS `id`,`u`.`fname` AS `fname`,`u`.`lname` AS `lname`,`u`.`uuid` AS `uuid`,`u`.`username` AS `username`,`u`.`password` AS `password`,`u`.`email` AS `email`,`u`.`roles_id` AS `roles_id`,`u`.`active` AS `active`,`r`.`role` AS `role` from (`coop_users` `u` left join `coop_roles` `r` on((`u`.`roles_id` = `r`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `coop_users_semesters_view`
--

/*!50001 DROP TABLE IF EXISTS `coop_users_semesters_view`*/;
/*!50001 DROP VIEW IF EXISTS `coop_users_semesters_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `coop_users_semesters_view` AS select `u`.`id` AS `id`,`u`.`fname` AS `fname`,`u`.`lname` AS `lname`,`u`.`uuid` AS `uuid`,`u`.`username` AS `username`,`u`.`password` AS `password`,`u`.`email` AS `email`,`u`.`roles_id` AS `roles_id`,`u`.`active` AS `active`,`us`.`semesters_id` AS `semesters_id`,`us`.`classes_id` AS `classes_id`,`us`.`credits` AS `credits`,`us`.`student` AS `student`,`us`.`coordinator` AS `coordinator`,`s`.`semester` AS `semester`,`s`.`current` AS `current`,`cl`.`name` AS `class`,`u2`.`fname` AS `coordfname`,`u2`.`lname` AS `coordlname` from ((((`coop_users` `u` join `coop_users_semesters` `us` on((`u`.`username` = `us`.`student`))) join `coop_semesters` `s` on((`us`.`semesters_id` = `s`.`id`))) left join `coop_classes` `cl` on((`us`.`classes_id` = `cl`.`id`))) left join `coop_users` `u2` on((`us`.`coordinator` = `u2`.`username`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-05-17 14:44:21
