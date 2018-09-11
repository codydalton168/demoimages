-- MySQL dump 10.16  Distrib 10.1.13-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: demoimg
-- ------------------------------------------------------
-- Server version	10.1.13-MariaDB

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
-- Table structure for table `adminuser`
--

DROP TABLE IF EXISTS `adminuser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adminuser` (
  `uid` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT '姓名',
  `gender` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '性别',
  `email` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT '電子郵件',
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '密碼',
  `onlineip` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT 'User IP',
  `ipfrom` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '來自哪裡',
  `regtime` int(10) NOT NULL COMMENT '加入時間',
  `logintime` int(10) NOT NULL COMMENT '最近登入時間',
  `situation` int(1) NOT NULL COMMENT '帳戶是否使用狀況',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adminuser`
--

LOCK TABLES `adminuser` WRITE;
/*!40000 ALTER TABLE `adminuser` DISABLE KEYS */;
INSERT INTO `adminuser` VALUES (1,'admin','male','admin@admin.com','21232f297a57a5a743894a0e4a801fc3','127.0.0.1','',1451982535,1462784079,1);
/*!40000 ALTER TABLE `adminuser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `fid` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `ordernum` bigint(10) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT '分類稱呼',
  `InputFile` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`fid`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,1,'RWD響應式','attachment/6565663532.png'),(2,3,'飯店旅宿','attachment/6263646636.png'),(3,4,'餐飲美食','attachment/6532633061.png'),(4,5,'公司企業','attachment/3737646531.png'),(5,6,'娛樂休閒','attachment/6438336162.png'),(6,7,'客製化系統','attachment/6364383462.png'),(7,8,'電子商務','attachment/3361303663.png'),(8,2,'機械業','attachment/3338323861.png'),(9,10,'其他演示','attachment/3863646265.png'),(10,9,'品牌專區','attachment/3532393935.png');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `demodata`
--

DROP TABLE IF EXISTS `demodata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `demodata` (
  `fid` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `images` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `httpurl` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `postdate` int(10) NOT NULL,
  PRIMARY KEY (`fid`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `demodata`
--

LOCK TABLES `demodata` WRITE;
/*!40000 ALTER TABLE `demodata` DISABLE KEYS */;
INSERT INTO `demodata` VALUES (1,',8,1,','威亞精密機械','wiviamachinery','attachment/201604/1461822931_967.jpg','http://www.wiviamachinery.com.tw/',1461822930),(2,'','TEAM MACHINERY CO., LTD.','','attachment/201604/1461822933_920.jpg','http://www.mori-power.com/',1461822930),(3,'','大享科技','','attachment/201604/1461822935_71.jpg','http://www.bigshottec.com/',1461822930),(4,'','Eyan Machine Tools Co., Ltd','','attachment/201604/1461822937_879.jpg','http://sdemo.precise-marketing.com/eyan/index.html',1461822930),(5,'','永安綜合醫療','','attachment/201604/1461822939_178.jpg','http://www.hongtun.com.tw/',1461822930),(6,'','真好貸','真好貸行銷信用貸款專辦銀行拒絕案件和急需資金者,信用貸款想要繳得輕鬆，交給真好貸,擔心貸款辦不過','attachment/201604/1461822941_461.jpg','http://www.yes1688.com.tw/',1461822930),(7,'','益彰機械','','attachment/201604/1461822943_394.jpg','http://sdemo.precise-marketing.com/demo4/index.html',1461822930),(8,'','COODTEK','','attachment/201604/1461822946_162.jpg','http://goodtek.precise-marketing.com/index.html',1461822930),(9,'','LICO','','attachment/201604/1461822947_793.jpg','http://lico.precise-marketing.com/',1461822930),(10,'','好事達','','attachment/201604/1461822949_71.jpg','http://sdemo.precise-marketing.com/hsd/index.html',1461822930),(11,'','大族激光鈑金裝備事業部','宇特國際有限公司,臺灣總代理','attachment/201604/1461822950_168.jpg','http://sdemo.precise-marketing.com/u-tanklaser/index.html',1461822930),(12,'','JING DUANN Machinery Industrial Co., Ltd.','','attachment/201604/1461822951_622.jpg','http://sdemo.precise-marketing.com/jdmcl/index.html',1461822930),(13,'','威得隆自動化股份有限公司','','attachment/201604/1461822953_781.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d28/index.html',1461822930),(14,'','馬可瀚','','attachment/201604/1461822954_343.jpg','http://sdemo.precise-marketing.com/makohan/index.html',1461822930),(15,'','優利貸','','attachment/201604/1461822955_113.jpg','http://sdemo.precise-marketing.com/unisis/index.html',1461822930),(16,'','金銲機電廠股份有限公司','','attachment/201604/1461822957_604.jpg','http://sdemo.precise-marketing.com/gold/index.html',1461822930),(17,'','東急不動產','','attachment/201604/1461822958_611.jpg','http://sdemo.precise-marketing.com/livable/index.html',1461822930),(18,'','Midas','','attachment/201604/1461822960_371.jpg','http://midas.precise-marketing.com/index.html',1461822930),(19,'','拓岳精機','','attachment/201604/1461822961_445.jpg','http://techaf.precise-marketing.com/index.html',1461822930),(20,'','星亮點','','attachment/201604/1461822962_951.jpg','http://sdemo.precise-marketing.com/bright/index.html',1461822930),(21,'','SHIELD - Free Bootstrap 3 Theme','','attachment/201604/1461822964_252.jpg','http://sdemo.precise-marketing.com/hotspring/index.html',1461822930),(22,'','喬光科技股份有限公司','','attachment/201604/1461822965_748.jpg','http://sdemo.precise-marketing.com/joylight/index.html',1461822930),(23,'','溫泉','','attachment/201604/1461822967_886.jpg','http://sdemo.precise-marketing.com/hotspring2/index.html',1461822930),(24,'','好山好水','','attachment/201604/1461822969_7.jpg','http://sdemo.precise-marketing.com/demo_H/index.html',1461822930),(25,'','宏鼎美食','','attachment/201604/1461822970_172.jpg','http://sdemo.precise-marketing.com/roseglory2/index.html',1461822930),(26,'','Trundean Machinery Industrial Co., Ltd. was ','','attachment/201604/1461822972_569.jpg','http://sdemo.precise-marketing.com/trundean/index.html',1461822930),(27,'','三彩玉','','attachment/201604/1461822974_652.jpg','http://sdemo.precise-marketing.com/hong-teng/index.html',1461822930),(28,'','PACKWAY INC.','','attachment/201604/1461822976_616.jpg','http://sdemo.precise-marketing.com/packway/index.html',1461822930),(29,'','享樂主義男女時尚會館','','attachment/201604/1461822978_339.jpg','http://sdemo.precise-marketing.com/happy-boygirl/index.html',1461822930),(30,'','啓鑫科技','','attachment/201604/1461822979_426.jpg','http://sdemo.precise-marketing.com/ppc/index.html',1461822930),(31,'','Amango Mobile Inc. ','','attachment/201604/1461822981_464.jpg','http://sdemo.precise-marketing.com/olink/index.html',1461822930),(32,'','真心娛樂酒店經紀','','attachment/201604/1461822983_121.jpg','http://www.dream168club.com/index.html',1461822930),(33,'','愛娛樂酒店經紀','','attachment/201604/1461822984_224.jpg','http://www.justlove66.com.tw/index.html',1461822930),(34,'','大台北好鄰居人力派遣','','attachment/201604/1461822987_415.jpg','http://sdemo.precise-marketing.com/neighbor/index.html',1461822930),(35,'','豐瑋機械有限公司','','attachment/201604/1461822989_539.jpg','http://sdemo.precise-marketing.com/forngwey/index.html',1461822930),(36,'','追夢女孩','','attachment/201604/1461822991_367.jpg','http://sdemo.precise-marketing.com/justdream/index.html',1461822930),(37,'','YONGTEK CO., LTD.','','attachment/201604/1461822993_794.jpg','http://sdemo.precise-marketing.com/yongtek/index.html',1461822930),(38,'','Union Machinery Co','','attachment/201604/1461822995_128.jpg','http://sdemo.precise-marketing.com/kimunion-2/index.html',1461822930),(39,'','友欣數位學苑','','attachment/201604/1461822996_596.jpg','http://sdemo.precise-marketing.com/easy100-2/index.html',1461822930),(40,'','CHI SHUN MACHINERY PLANT CO., Ltd.','','attachment/201604/1461822998_696.jpg','http://sdemo.precise-marketing.com/chishun/index.html',1461822930),(41,',9,','演示網站','','attachment/201604/1461822999_519.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d01/index.html',1461822930),(42,',9,','演示網站','','attachment/201604/1461823002_660.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d02/index.html',1461822930),(43,',9,','演示網站','','attachment/201604/1461823005_855.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d03/index.html',1461822930),(44,',9,','演示網站','','attachment/201604/1461823006_953.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d04/index.html',1461822930),(45,',9,','演示網站','','attachment/201604/1461823008_366.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d05/index.html',1461822930),(46,',9,','演示網站','','attachment/201604/1461823010_604.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d06/index.html',1461822930),(47,',9,','演示網站','','attachment/201604/1461823012_33.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d07/index.html',1461822930),(48,',9,','演示網站','','attachment/201604/1461823014_482.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d08/index.html',1461822930),(49,',9,','演示網站','','attachment/201604/1461823016_806.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d09/index.html',1461822930),(50,',9,','演示網站','','attachment/201604/1461823018_53.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d10/index.html',1461822930),(51,',9,','演示網站','','attachment/201604/1461823020_351.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d11/index.html',1461822930),(52,',9,','演示網站','','attachment/201604/1461823022_126.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d12/index.html',1461822930),(53,',9,','演示網站','','attachment/201604/1461823024_805.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d13/index.html',1461822930),(54,',9,','演示網站','','attachment/201604/1461823026_602.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d14/index.html',1461822930),(55,',9,','演示網站','','attachment/201604/1461823028_525.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d15/index.html',1461822930),(56,',9,','演示網站','','attachment/201604/1461823030_229.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d16/index.html',1461822930),(57,',9,','演示網站','','attachment/201604/1461823032_835.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d17/index.html',1461822930),(58,',9,','演示網站','','attachment/201604/1461823034_345.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d18/index.html',1461822930),(59,',9,','演示網站','','attachment/201604/1461823037_776.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d19/index.html',1461822930),(60,',9,','演示網站','','attachment/201604/1461823039_635.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d20/index.html',1461822930),(61,',9,','演示網站','','attachment/201604/1461823041_862.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d21/index.html',1461822930),(62,',9,','演示網站','','attachment/201604/1461823043_875.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d22/index.html',1461822930),(63,',9,','演示網站','','attachment/201604/1461823045_822.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d23/index.html',1461822930),(64,',9,','演示網站','','attachment/201604/1461823047_687.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d24/index.html',1461822930),(65,',9,','演示網站','','attachment/201604/1461823049_356.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d25/index.html',1461822930),(66,',9,','演示網站','','attachment/201604/1461823051_285.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d26/index.html',1461822930),(67,',9,','演示網站','','attachment/201604/1461823053_881.jpg','http://sdemo.precise-marketing.com/nico_demo/sample/d27/index.html',1461822930);
/*!40000 ALTER TABLE `demodata` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-05-11 16:36:17
