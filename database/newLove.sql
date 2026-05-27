-- MariaDB dump 10.19-11.1.2-MariaDB, for osx10.18 (arm64)
--
-- Host: 127.0.0.1    Database: newLove
-- ------------------------------------------------------
-- Server version	11.1.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `newLove`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `newLove` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `newLove`;

--
-- Table structure for table `collections`
--

DROP TABLE IF EXISTS `collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `collections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collections`
--

LOCK TABLES `collections` WRITE;
/*!40000 ALTER TABLE `collections` DISABLE KEYS */;
INSERT INTO `collections` VALUES
(5,'Gold Collection'),
(6,'Pearl Collection'),
(7,'Summer Collection');
/*!40000 ALTER TABLE `collections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `colours`
--

DROP TABLE IF EXISTS `colours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `colours` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colours`
--

LOCK TABLES `colours` WRITE;
/*!40000 ALTER TABLE `colours` DISABLE KEYS */;
INSERT INTO `colours` VALUES
(1,'Gold'),
(2,'Blue'),
(4,'Green'),
(5,'Silver'),
(6,'Yellow'),
(7,'Black'),
(8,'Bronze');
/*!40000 ALTER TABLE `colours` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materials_products`
--

DROP TABLE IF EXISTS `materials_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `materials_products` (
  `quantity` int(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rawmaterial_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`,`rawmaterial_id`),
  KEY `FK_materials_products_materials` (`rawmaterial_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materials_products`
--

LOCK TABLES `materials_products` WRITE;
/*!40000 ALTER TABLE `materials_products` DISABLE KEYS */;
INSERT INTO `materials_products` VALUES
(3,8,1),
(1,8,2),
(1,16,8),
(2,16,11),
(12,25,8);
/*!40000 ALTER TABLE `materials_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phinxlog`
--

DROP TABLE IF EXISTS `phinxlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phinxlog` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phinxlog`
--

LOCK TABLES `phinxlog` WRITE;
/*!40000 ALTER TABLE `phinxlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `phinxlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_inventories`
--

DROP TABLE IF EXISTS `product_inventories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_inventories` (
  `product_id` int(11) NOT NULL,
  `quantity` int(255) NOT NULL,
  PRIMARY KEY (`product_id`),
  CONSTRAINT `FK_Inventory_Product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_inventories`
--

LOCK TABLES `product_inventories` WRITE;
/*!40000 ALTER TABLE `product_inventories` DISABLE KEYS */;
INSERT INTO `product_inventories` VALUES
(16,5),
(17,4),
(18,7),
(19,12),
(20,8),
(21,3),
(23,1),
(24,1);
/*!40000 ALTER TABLE `product_inventories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `photo` varchar(200) DEFAULT NULL,
  `collection_id` int(11) DEFAULT NULL,
  `colour_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_Product_Collection` (`collection_id`),
  KEY `FK_Product_Colour` (`colour_id`),
  CONSTRAINT `FK_Product_Collection` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_Product_Colour` FOREIGN KEY (`colour_id`) REFERENCES `colours` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES
(16,'Two Circle Earring','The Two Circle Earring belongs to our Gold Collection and brings together a fantastic vibrance to the wearer','27497E2A-A9B8-46B4-9D7A-173F178F5767.PNG',5,1),
(17,'Gold and Silver Pearl Earring','\"Add a touch of elegance to your jewelry collection with our exquisite Gold and Silver Pearl Earrings. These stunning earrings feature lustrous pearls delicately set in a harmonious blend of gold and silver, creating a timeless and versatile accessory tha','IMG_3799.PNG',6,5),
(18,'Silver Pearl Earring','\"Elevate your style with our timeless Pearl Earring. Crafted with meticulous attention to detail, these earrings showcase the natural beauty of pearls. Each pearl is hand-selected for its luster and elegance, making every pair unique. Whether you\'re dress','IMG_3800.PNG',6,5),
(19,'Gold Ball Earring','\"Enhance your everyday style with our chic Gold Ball Earring. These earrings feature a classic design with a contemporary twist, showcasing a gleaming gold ball that adds a touch of modern elegance to any outfit. With their versatile and minimalist appeal','IMG_3801.PNG',5,1),
(20,'Three Stone Necklace','\"Elevate your neckline with our exquisite Three Stone Necklace. This stunning piece of jewelry features a delicate chain adorned with three sparkling gemstones, each carefully chosen for its brilliance and beauty. The three stones symbolize the past, pres','IMG_5049.PNG',7,1),
(21,'Blue Ball Earring','\"Make a bold and stylish statement with our Blue Ball Earring. These eye-catching earrings feature vibrant blue spheres that instantly catch the eye and add a pop of color to your look. The spherical design adds a touch of modern flair to your ensemble, m','IMG_6888.PNG',7,2),
(22,'Bronze Stone Earring','\"Elevate your style with our stunning Bronze Stone Earrings. These elegant earrings feature exquisite bronze-hued stones, carefully selected for their rich, earthy tones and unique character. The warm and timeless design adds a touch of sophistication to ','PNG image-912E6B02CF67-1.PNG',7,8),
(23,'Gold Stone Earring','\"Radiate timeless elegance with our Gold Stone Earrings. These exquisite earrings feature dazzling golden-hued stones that capture the light and add a touch of luxury to your look. The classic design and shimmering stones make them a versatile accessory s','PNG image-C296B86DAF6F-1.jpg',5,1),
(24,'Three Gold Piece Earring','\"Elevate your elegance with our Three Gold Piece Earring. This exquisite earring set features a trio of finely crafted gold pieces, each designed with a unique charm and style. These versatile earrings can be mixed and matched to create a personalized loo','PNG image-FDF05AEC4FFE-1.PNG',5,1),
(40,'item1','sdafsd','1.png',5,2);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rawmaterial_inventories`
--

DROP TABLE IF EXISTS `rawmaterial_inventories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rawmaterial_inventories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rawmaterial_id` int(11) NOT NULL,
  `quantity` int(255) NOT NULL,
  `lowStockLimit` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_Rawmaterial_Inventory_Rawmaterial` (`rawmaterial_id`),
  CONSTRAINT `FK_Rawmaterial_Inventory_Rawmaterial` FOREIGN KEY (`rawmaterial_id`) REFERENCES `rawmaterials` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rawmaterial_inventories`
--

LOCK TABLES `rawmaterial_inventories` WRITE;
/*!40000 ALTER TABLE `rawmaterial_inventories` DISABLE KEYS */;
INSERT INTO `rawmaterial_inventories` VALUES
(43,7,110,5),
(44,8,30,40),
(45,9,63,30),
(46,10,201,60),
(47,12,86,100);
/*!40000 ALTER TABLE `rawmaterial_inventories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rawmaterials`
--

DROP TABLE IF EXISTS `rawmaterials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rawmaterials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `delivery_time` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `cost_price` decimal(6,2) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `photo` varchar(200) DEFAULT NULL,
  `colour_id` int(11) DEFAULT NULL,
  `lowStockLimit` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_Rawmaterial_Colour` (`colour_id`),
  KEY `FK_Rawmaterial_Supplier` (`supplier_id`),
  CONSTRAINT `FK_Rawmaterial_Colour` FOREIGN KEY (`colour_id`) REFERENCES `colours` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_Rawmaterial_Supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rawmaterials`
--

LOCK TABLES `rawmaterials` WRITE;
/*!40000 ALTER TABLE `rawmaterials` DISABLE KEYS */;
INSERT INTO `rawmaterials` VALUES
(7,'Cernit Bezel Triple Bowl Earring Case','2 weeks','\"Introducing our Cernit Bezel Triple Bowl Earring Component – the perfect foundation for your jewelry creations. Crafted with precision and elegance, these bezel components feature three gracefully curved bowls, designed to securely hold and showcase your',2.50,6,'Cernit-Bezels-Triple-Bowl-Earrings-27x11x3mm.jpg',5,0),
(8,'Cernit Bezel Long Oval Pendant Case','1 week','\"Elevate your jewelry designs with our Cernit Bezel Long Oval Earring Component. Expertly crafted, this component features a gracefully elongated oval bezel that\'s perfect for showcasing your favorite gemstones, resin creations, or other precious elements',1.50,7,'Cernit-Bezel-Long-Oval-Pendant-39x10x5mm.jpg',8,0),
(9,'Cernit Bezel Triangle Pendant Case','1 week','\"Unlock your creativity with our Cernit Bezel Triangle Pendant. This beautifully crafted pendant component boasts a sleek triangular design, ideal for showcasing your artistic creations, gemstones, or other cherished embellishments. Its unique shape adds ',1.00,8,'Cernit-Bezel-Triangle-Pendant-26x17x19x4mm.jpg',5,0),
(10,'Butterfly Ear Nuts','1 week','\"Secure your earrings with style and ease using our Butterfly Ear Nut. These delicately designed ear nuts not only provide a secure grip for your earrings but also feature a charming butterfly shape, adding a touch of whimsy and elegance to your jewelry. ',0.50,9,'Butterfly-Ear-Nuts-BRIGHT-ROSE-GOLD_Stainless-Steel-50-pk.jpg',8,0),
(11,'Black Butterfly Ear Nuts','1 week','\"Enhance your earring designs with our Black Butterfly Ear Nut. These unique ear nuts combine functionality with a touch of sophistication, featuring a sleek black finish and an elegant butterfly shape. Not only do they provide a secure and comfortable fi',0.50,9,'Butterfly-Ear-Nuts-BLACK-plated_Stainless-Steel-50-pk.jpg',7,0),
(12,'Gold Hollow Tube','1 week','\"Discover the timeless elegance of our Gold Hollow Tube. Crafted with precision and sophistication, this exquisite component showcases a hollow tube design in radiant gold, ideal for adding a touch of refinement to your jewelry creations. Whether used as ',0.75,10,'Hollow-Curved-Tube-80mmx6mm-Gold-Colour.jpg',1,0),
(23,'item1','2 days','fewrfr',34.00,7,'1.png',2,NULL);
/*!40000 ALTER TABLE `rawmaterials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `name` varchar(56) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `phone_no` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES
('Arnav Jewllery',6,'arnav@email.com','+61456342897','www.arnavjewels.com','Melbourne, Victoria, Australia'),
('Lloyd Wholesale',7,'lloyd@email.com','+61433567890','www.lloydwholesale.com','Sydney, New South Wales, Australia'),
('Liam Jewellery Hardware',8,'LJH@email.com','+61345890757','www.LJH.com','Adelaide, South Australia, Australia'),
('Tianning Jewellery ',9,'tianningJ@email.com','+34596809727','www.tianningjewellery.com','Geelong, Victoria, Australia'),
('Jason Silver Wholesale',10,'jasonsilver@email.com','+34567765765','www.jasonsilverwholsale.com','Ballarat, Victoria, Australia');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(64) NOT NULL,
  `last_name` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `nonce` char(128) DEFAULT NULL,
  `nonce_expiry` timestamp NULL DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(15,'Arnav','Bhalla','arnie.bhalla@gmail.com','$2y$10$2t9/V6G8lxupHaEAWGjNS.CfW4F7VVrPCamqKlBKolFfC0AwZch0C','7f3d0e1687ab2f40a6e6242a4364698434df6920c92f0084f23a271b1816d3a6327ae3fad09aadf4e9311c07ae1319bfd69efe94f13f76cb06e057910c800356','2023-10-18 03:40:29','2023-09-04 05:48:26','2023-10-11 03:40:29'),
(16,'admin','admin','admin@email.com','$2y$10$KXl3bQpbloNq649c.sdv7.EC85OUhiBnEy2VTc1Dm6TLpFT.EDNci',NULL,NULL,'2023-09-12 09:51:37','2023-09-12 09:51:37'),
(17,'Arnav','Bhalla','abha0028@student.monash.edu','$2y$10$1gOxbXqOAY8BfeqSClXzhOtv2zV/5ZxAHW6HDueo5lUbOIIXCygIK','bb28c7133f596040c01c2ef33980d48623848b083e3836ff66371c6de4c8a28da181463d41a1d55965c8e1f2b182fbdbf72341eb18081280986c6291324779fb','2023-10-18 03:40:39','2023-10-02 02:22:53','2023-10-11 03:40:39'),
(18,'tianning','yang','tyan0026@student.monash.edu','$2y$10$Kb9ciOE7o85g4b8LEgv8geX1i0dtm9n.T4fwYkUvFH.3wkzfoR9m2',NULL,NULL,'2023-10-11 09:37:07','2023-10-11 09:37:07');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'newLove'
--

--
-- Dumping routines for database 'newLove'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-27 23:50:36
