-- MySQL dump 10.13  Distrib 5.7.34, for Linux (x86_64)
--
-- Host: localhost    Database: saychas_z
-- ------------------------------------------------------
-- Server version	5.7.34-0ubuntu0.18.04.1

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
-- Table structure for table `user_data`
--

DROP TABLE IF EXISTS `user_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_data` (
  `user_id` int(11) DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `geodata` text COLLATE utf8_unicode_ci,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fias_id` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fias_level` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_data`
--

LOCK TABLES `user_data` WRITE;
/*!40000 ALTER TABLE `user_data` DISABLE KEYS */;
INSERT INTO `user_data` VALUES (159,'г Москва, ул Угрешская, д 2 стр 2, кв 2','{\"value\":\"г Москва, ул Угрешская, д 2 стр 2, кв 2\",\"unrestricted_value\":\"115088, г Москва, р-н Печатники, ул Угрешская, д 2 стр 2, кв 2\",\"data\":{\"postal_code\":\"115088\",\"country\":\"Россия\",\"country_iso_code\":\"RU\",\"federal_district\":\"Центральный\",\"region_fias_id\":\"0c5b2444-70a0-4932-980c-b4dc0d3f02b5\",\"region_kladr_id\":\"7700000000000\",\"region_iso_code\":\"RU-MOW\",\"region_with_type\":\"г Москва\",\"region_type\":\"г\",\"region_type_full\":\"город\",\"region\":\"Москва\",\"area_fias_id\":null,\"area_kladr_id\":null,\"area_with_type\":null,\"area_type\":null,\"area_type_full\":null,\"area\":null,\"city_fias_id\":\"0c5b2444-70a0-4932-980c-b4dc0d3f02b5\",\"city_kladr_id\":\"7700000000000\",\"city_with_type\":\"г Москва\",\"city_type\":\"г\",\"city_type_full\":\"город\",\"city\":\"Москва\",\"city_area\":\"Юго-восточный\",\"city_district_fias_id\":null,\"city_district_kladr_id\":null,\"city_district_with_type\":\"р-н Печатники\",\"city_district_type\":\"р-н\",\"city_district_type_full\":\"район\",\"city_district\":\"Печатники\",\"settlement_fias_id\":null,\"settlement_kladr_id\":null,\"settlement_with_type\":null,\"settlement_type\":null,\"settlement_type_full\":null,\"settlement\":null,\"street_fias_id\":\"966e6baf-a2da-4e70-8ac3-ba2c1a2a699a\",\"street_kladr_id\":\"77000000000022300\",\"street_with_type\":\"ул Угрешская\",\"street_type\":\"ул\",\"street_type_full\":\"улица\",\"street\":\"Угрешская\",\"house_fias_id\":\"02fd4c17-a8b7-4840-968b-9e417957f0d3\",\"house_kladr_id\":\"7700000000002230009\",\"house_cadnum\":null,\"house_type\":\"д\",\"house_type_full\":\"дом\",\"house\":\"2\",\"block_type\":\"стр\",\"block_type_full\":\"строение\",\"block\":\"2\",\"entrance\":null,\"floor\":null,\"flat_fias_id\":null,\"flat_cadnum\":null,\"flat_type\":\"кв\",\"flat_type_full\":\"квартира\",\"flat\":\"2\",\"flat_area\":null,\"square_meter_price\":null,\"flat_price\":null,\"postal_box\":null,\"fias_id\":\"02fd4c17-a8b7-4840-968b-9e417957f0d3\",\"fias_code\":\"77000000000000002230009\",\"fias_level\":\"8\",\"fias_actuality_state\":\"0\",\"kladr_id\":\"7700000000002230009\",\"geoname_id\":\"524901\",\"capital_marker\":\"0\",\"okato\":\"45290582000\",\"oktmo\":\"45393000\",\"tax_office\":\"7723\",\"tax_office_legal\":\"7723\",\"timezone\":null,\"geo_lat\":\"55.7110452\",\"geo_lon\":\"37.6840653\",\"beltway_hit\":null,\"beltway_distance\":null,\"metro\":null,\"qc_geo\":\"0\",\"qc_complete\":null,\"qc_house\":null,\"history_values\":null,\"unparsed_parts\":null,\"source\":null,\"qc\":null}}','2021-07-07 11:51:41','02fd4c17-a8b7-4840-968b-9e417957f0d3',8),(44,'г Москва, ул Угрешская, д 2 стр 2, кв 2','{\"value\":\"г Москва, ул Угрешская, д 2 стр 2, кв 2\",\"unrestricted_value\":\"115088, г Москва, р-н Печатники, ул Угрешская, д 2 стр 2, кв 2\",\"data\":{\"postal_code\":\"115088\",\"country\":\"Россия\",\"country_iso_code\":\"RU\",\"federal_district\":\"Центральный\",\"region_fias_id\":\"0c5b2444-70a0-4932-980c-b4dc0d3f02b5\",\"region_kladr_id\":\"7700000000000\",\"region_iso_code\":\"RU-MOW\",\"region_with_type\":\"г Москва\",\"region_type\":\"г\",\"region_type_full\":\"город\",\"region\":\"Москва\",\"area_fias_id\":null,\"area_kladr_id\":null,\"area_with_type\":null,\"area_type\":null,\"area_type_full\":null,\"area\":null,\"city_fias_id\":\"0c5b2444-70a0-4932-980c-b4dc0d3f02b5\",\"city_kladr_id\":\"7700000000000\",\"city_with_type\":\"г Москва\",\"city_type\":\"г\",\"city_type_full\":\"город\",\"city\":\"Москва\",\"city_area\":\"Юго-восточный\",\"city_district_fias_id\":null,\"city_district_kladr_id\":null,\"city_district_with_type\":\"р-н Печатники\",\"city_district_type\":\"р-н\",\"city_district_type_full\":\"район\",\"city_district\":\"Печатники\",\"settlement_fias_id\":null,\"settlement_kladr_id\":null,\"settlement_with_type\":null,\"settlement_type\":null,\"settlement_type_full\":null,\"settlement\":null,\"street_fias_id\":\"966e6baf-a2da-4e70-8ac3-ba2c1a2a699a\",\"street_kladr_id\":\"77000000000022300\",\"street_with_type\":\"ул Угрешская\",\"street_type\":\"ул\",\"street_type_full\":\"улица\",\"street\":\"Угрешская\",\"house_fias_id\":\"02fd4c17-a8b7-4840-968b-9e417957f0d3\",\"house_kladr_id\":\"7700000000002230009\",\"house_cadnum\":null,\"house_type\":\"д\",\"house_type_full\":\"дом\",\"house\":\"2\",\"block_type\":\"стр\",\"block_type_full\":\"строение\",\"block\":\"2\",\"entrance\":null,\"floor\":null,\"flat_fias_id\":null,\"flat_cadnum\":null,\"flat_type\":\"кв\",\"flat_type_full\":\"квартира\",\"flat\":\"2\",\"flat_area\":null,\"square_meter_price\":null,\"flat_price\":null,\"postal_box\":null,\"fias_id\":\"02fd4c17-a8b7-4840-968b-9e417957f0d3\",\"fias_code\":\"77000000000000002230009\",\"fias_level\":\"8\",\"fias_actuality_state\":\"0\",\"kladr_id\":\"7700000000002230009\",\"geoname_id\":\"524901\",\"capital_marker\":\"0\",\"okato\":\"45290582000\",\"oktmo\":\"45393000\",\"tax_office\":\"7723\",\"tax_office_legal\":\"7723\",\"timezone\":null,\"geo_lat\":\"55.7110452\",\"geo_lon\":\"37.6840653\",\"beltway_hit\":null,\"beltway_distance\":null,\"metro\":null,\"qc_geo\":\"0\",\"qc_complete\":null,\"qc_house\":null,\"history_values\":null,\"unparsed_parts\":null,\"source\":null,\"qc\":null}}','2021-07-07 13:06:24','02fd4c17-a8b7-4840-968b-9e417957f0d3',8),(44,'г Москва, ул Шаболовка, д 37 стр 2, кв 2','{\"value\":\"г Москва, ул Шаболовка, д 37 стр 2, кв 2\",\"unrestricted_value\":\"115162, г Москва, Донской р-н, ул Шаболовка, д 37 стр 2, кв 2\",\"data\":{\"postal_code\":\"115162\",\"country\":\"Россия\",\"country_iso_code\":\"RU\",\"federal_district\":\"Центральный\",\"region_fias_id\":\"0c5b2444-70a0-4932-980c-b4dc0d3f02b5\",\"region_kladr_id\":\"7700000000000\",\"region_iso_code\":\"RU-MOW\",\"region_with_type\":\"г Москва\",\"region_type\":\"г\",\"region_type_full\":\"город\",\"region\":\"Москва\",\"area_fias_id\":null,\"area_kladr_id\":null,\"area_with_type\":null,\"area_type\":null,\"area_type_full\":null,\"area\":null,\"city_fias_id\":\"0c5b2444-70a0-4932-980c-b4dc0d3f02b5\",\"city_kladr_id\":\"7700000000000\",\"city_with_type\":\"г Москва\",\"city_type\":\"г\",\"city_type_full\":\"город\",\"city\":\"Москва\",\"city_area\":\"Южный\",\"city_district_fias_id\":null,\"city_district_kladr_id\":null,\"city_district_with_type\":\"Донской р-н\",\"city_district_type\":\"р-н\",\"city_district_type_full\":\"район\",\"city_district\":\"Донской\",\"settlement_fias_id\":null,\"settlement_kladr_id\":null,\"settlement_with_type\":null,\"settlement_type\":null,\"settlement_type_full\":null,\"settlement\":null,\"street_fias_id\":\"d1febe9c-11b9-46db-891e-30f3083d5611\",\"street_kladr_id\":\"77000000000313300\",\"street_with_type\":\"ул Шаболовка\",\"street_type\":\"ул\",\"street_type_full\":\"улица\",\"street\":\"Шаболовка\",\"house_fias_id\":\"f9462eb5-216a-4897-934f-bafa832d9c88\",\"house_kladr_id\":\"7700000000031330036\",\"house_cadnum\":null,\"house_type\":\"д\",\"house_type_full\":\"дом\",\"house\":\"37\",\"block_type\":\"стр\",\"block_type_full\":\"строение\",\"block\":\"2\",\"entrance\":null,\"floor\":null,\"flat_fias_id\":null,\"flat_cadnum\":null,\"flat_type\":\"кв\",\"flat_type_full\":\"квартира\",\"flat\":\"2\",\"flat_area\":null,\"square_meter_price\":null,\"flat_price\":null,\"postal_box\":null,\"fias_id\":\"f9462eb5-216a-4897-934f-bafa832d9c88\",\"fias_code\":\"77000000000000031330036\",\"fias_level\":\"8\",\"fias_actuality_state\":\"0\",\"kladr_id\":\"7700000000031330036\",\"geoname_id\":\"524901\",\"capital_marker\":\"0\",\"okato\":\"45296561000\",\"oktmo\":\"45915000\",\"tax_office\":\"7725\",\"tax_office_legal\":\"7725\",\"timezone\":null,\"geo_lat\":\"55.7181023\",\"geo_lon\":\"37.6117459\",\"beltway_hit\":null,\"beltway_distance\":null,\"metro\":null,\"qc_geo\":\"0\",\"qc_complete\":null,\"qc_house\":null,\"history_values\":null,\"unparsed_parts\":null,\"source\":null,\"qc\":null}}','2021-07-08 06:13:10','f9462eb5-216a-4897-934f-bafa832d9c88',8);
/*!40000 ALTER TABLE `user_data` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-07-08  9:24:01
