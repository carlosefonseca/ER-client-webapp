# Sequel Pro dump
# Version 2492
# http://code.google.com/p/sequel-pro
#
# Host: eu-r.com (MySQL 5.0.91-community)
# Database: eurcom_engirega
# Generation Time: 2011-01-20 02:18:08 +0000
# ************************************************************

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table clientes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `clientes`;

CREATE TABLE `clientes` (
  `id` varchar(20) NOT NULL,
  `name` text,
  `center_lat` varchar(15) default NULL,
  `center_lng` varchar(15) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` (`id`,`name`,`center_lat`,`center_lng`)
VALUES
	('oeiras','C?mara Municipal de Oeiras','0.9999999999','-9.27538');

/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table jardins
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jardins`;

CREATE TABLE `jardins` (
  `client` varchar(50) character set latin1 NOT NULL default '',
  `id` int(3) NOT NULL auto_increment,
  `acronym` varchar(5) collate utf8_bin NOT NULL default '',
  `name` varchar(255) character set latin1 default NULL,
  `lat` varchar(20) character set latin1 NOT NULL default '0',
  `lng` varchar(20) character set latin1 default '0',
  `data` text character set latin1,
  `status` varchar(15) character set latin1 default 'ok',
  `contact` varchar(14) collate utf8_bin default NULL,
  `ligado` int(11) default NULL,
  PRIMARY KEY  (`client`,`acronym`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

LOCK TABLES `jardins` WRITE;
/*!40000 ALTER TABLE `jardins` DISABLE KEYS */;
INSERT INTO `jardins` (`client`,`id`,`acronym`,`name`,`lat`,`lng`,`data`,`status`,`contact`,`ligado`)
VALUES
	('oeiras',3,'MEDR0','Medrosa','38.684111','-9.320505','Temperatura: 22ºC\nHumidade: 20 mm\nCaudal Total: 100m<sup>3</sup>\nCaudal Médio: 20m<sup>3</sup>','ok',NULL,NULL),
	('oeiras',4,'PMPO_','P. Marques de Pombal','38.692787','-9.315596','Temperatura: 21ºC\nHumidade: 25 mm\nCaudal Total: 200m<sup>3</sup>\nCaudal Médio: 30m<sup>3</sup>','ok',NULL,NULL),
	('oeiras',5,'BACAS','Bairro Augusto Castro','38.696505','-9.309927','Temperatura: 19ºC\nHumidade: 20 mm\nCaudal Total: 500m<sup>3</sup>\nCaudal Médio: 100m<sup>3</sup>','ok',NULL,NULL),
	('oeiras',6,'SLFIG','S. Luis Figuerinha','38.69612','-9.305272','Temperatura: 22ºC\nHumidade: 20 mm\nCaudal Total: 100m<sup>3</sup>\nCaudal Médio: 25m<sup>3</sup>','ok',NULL,NULL),
	('lisboa',7,'t1','Lisboa','38.74779118432729','-9.123973846435547',NULL,'ok',NULL,NULL),
	('lisboa',8,'t2','Lisboa2','38.74350688982668','-9.171180725097656',NULL,'ko',NULL,NULL),
	('oeiras',9,'PFFIG','Puxa Feixe Figueirinha','38.70070841856798','-9.298993349075317','Temperatura: 20º C\nHumidade: 20 mm\nCaudal Total: 150m<sup>3</sup>\nCaudal Médio: 70m<sup>3</sup>','ok',NULL,NULL),
	('cascais',10,'','Jardim 1','0','0',NULL,'ok',NULL,NULL),
	('moita',11,'','Jardim 1','0','0',NULL,'ok',NULL,NULL),
	('gulbenkian',12,'','Jardim 1','0','0',NULL,'ok',NULL,NULL),
	('acmilitar',13,'','Jardim 1','0','0',NULL,'ok',NULL,NULL),
	('oeiras',14,'QTORN','Quinta do Torneiro','38.709426','-9.28945',NULL,'ok',NULL,NULL),
	('oeiras',15,'PCIDA','Parque das Cidades','38.70924','-9.295903',NULL,'ok',NULL,NULL),
	('oeiras',16,'PARCO','Palácio dos Arcos','38.696484','-9.288919',NULL,'ok',NULL,NULL),
	('oeiras',17,'VFRIA','Vila Fria','38.71812637940384','-9.294755458831787',NULL,'ok',NULL,NULL),
	('oeiras',18,'JMURG','Jardim do Murganhal','38.7124589963465','-9.270272254943848',NULL,'ok',NULL,NULL),
	('oeiras',19,'POUTO','Piscinas Outurela','38.70200622596984','-9.302759170532227',NULL,'ok',NULL,NULL),
	('oeiras',20,'CCCAR','C. Cívico Carnaxide','38.72609929999977','-9.241905212402344',NULL,'ok',NULL,NULL),
	('oeiras',21,'PUMIR','Parque Urbano Miraflores','38.71294','-9.224471',NULL,'ok',NULL,NULL),
	('oeiras',22,'JQSAN','Jardim Quinta Sto António','38.7091981639085','-9.229041337966919',NULL,'ok',NULL,NULL),
	('oeiras',23,'JACIP','Jardim Aciprestes','38.711827','-9.242592',NULL,'ok',NULL,NULL),
	('oeiras',24,'PANJO','Palácio Anjos','38.699854','-9.23039',NULL,'ok',NULL,NULL),
	('oeiras',25,'AQUEI','Alameda de Queijas','38.723178','-9.261974',NULL,'ok',NULL,NULL),
	('oeiras',26,'Q7CAS','Quinta 7 Castelos','38.68845763104359','-9.269027709960938',NULL,'ok',NULL,NULL),
	('oeiras',27,'MEDRO','Medrosa','38.702592324818326','-9.302276372909546',NULL,'ok','00000000000NET',NULL),
	('oeiras',28,'PMPOM','Parque Marques de Pombal','38.68650635500108','-9.305065870285034',NULL,'ok','00351934377767',NULL),
	('oeiras',29,'OOOOO','LIVRE','0','0',NULL,'ok','00000000000000',NULL),
	('algarve',30,'MEDRO','Medrosa','0','0',NULL,'ok','00000000000NET',NULL),
	('algarve',31,'PMPOM','Parque Marques de Pombal','0','0',NULL,'ok','00351934377767',NULL),
	('algarve',32,'BACAS','Bairro Augusto Castro','0','0',NULL,'ok','00351934377767',NULL),
	('algarve',33,'SLFIG','SÃ£o Luis Figueirinha','0','0',NULL,'ok','00351934377767',NULL),
	('algarve',34,'PFFIG','Puxa Feixe Figueirinha','0','0',NULL,'ok','00351934377767',NULL),
	('algarve',35,'PCIDA','Parque das Cidades','0','0',NULL,'ok','00351934377767',NULL),
	('algarve',36,'QTORN','Quinta do Torneiro','0','0',NULL,'ok','00000000000NET',NULL),
	('algarve',37,'PARCO','Palacio dos Arcos','0','0',NULL,'ok','00351934377767',NULL),
	('algarve',38,'VFRIA','Vila Fria','0','0',NULL,'ok','00351934377767',NULL),
	('algarve',39,'JMURG','Jardim Murganhal','0','0',NULL,'ok','00351934377767',NULL),
	('algarve',40,'PANJO','Palacio Anjo','0','0',NULL,'ok','00351934377767',NULL),
	('algarve',41,'JACIP','Jardim Aciprestes','0','0',NULL,'ok','00351934377767',NULL),
	('algarve',42,'JQSAN','Jardim Quinta de Santo Antonio','0','0',NULL,'ok','00351934377767',NULL),
	('algarve',43,'PUMIR','Parque Urbano de Miraflores','0','0',NULL,'ok','00351934377767',NULL),
	('algarve',44,'CCCAR','Centro civico de Carnaxide','0','0',NULL,'ok','00351934377767',NULL),
	('algarve',45,'POUTO','Piscinas Outorela','0','0',NULL,'ok','00351934377767',NULL),
	('algarve',46,'AQUEI','Alameda de Queijas','0','0',NULL,'ok','00351934377767',NULL),
	('algarve',47,'Q7CAS','Quinta 7 Castelos','0','0',NULL,'ok','00351934377767',NULL),
	('algarve',48,'OOOOO','LIVRE','0','0',NULL,'ok','00000000000000',NULL);

/*!40000 ALTER TABLE `jardins` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table jardins_dados
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jardins_dados`;

CREATE TABLE `jardins_dados` (
  `id` int(11) NOT NULL auto_increment,
  `client` varchar(30) NOT NULL,
  `jardim` varchar(10) NOT NULL,
  `variavel` varchar(30) default NULL,
  `valor` float default NULL,
  `data` date default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

LOCK TABLES `jardins_dados` WRITE;
/*!40000 ALTER TABLE `jardins_dados` DISABLE KEYS */;
INSERT INTO `jardins_dados` (`id`,`client`,`jardim`,`variavel`,`valor`,`data`)
VALUES
	(1,'oeiras','1','caudal',78,'2009-04-05');

/*!40000 ALTER TABLE `jardins_dados` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user` varchar(255) NOT NULL default '',
  `pass` varchar(255) NOT NULL,
  `email` varchar(255) default NULL,
  `phone` varchar(9) default NULL,
  `permissions` text,
  `client` text,
  PRIMARY KEY  (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`user`,`pass`,`email`,`phone`,`permissions`,`client`)
VALUES
	('admin','21232f297a57a5a743894a0e4a801fc3','carlosefonseca@gmail.com','963507744','edit_program,edit_markers,j*,admin','*'),
	('teste','21232f297a57a5a743894a0e4a801fc3','teste',NULL,'j*','oeiras'),
	('tiago','b59c67bf196a4758191e42f76670ceba','qwertyu@iuytr.com',NULL,'edit_program,j*','oeiras'),
	('fran','b59c67bf196a4758191e42f76670ceba','a@a.com',NULL,'edit_program,j*','oeiras lisboa cascais acmilitar gulbenkian moita'),
	('E1','b59c67bf196a4758191e42f76670ceba','b@b.com',NULL,'edit_program,j1,j2,j3,j4','oeiras'),
	('E2','b59c67bf196a4758191e42f76670ceba','a@a.com',NULL,'edit_program,j10,j11,j12,j13','oeiras'),
	('E3','b59c67bf196a4758191e42f76670ceba','a@a.com',NULL,'edit_program,j5,j6,j7,j8','oeiras'),
	('E4','b59c67bf196a4758191e42f76670ceba','a@a.com',NULL,'','oeiras');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table variaveis
# ------------------------------------------------------------

DROP TABLE IF EXISTS `variaveis`;

CREATE TABLE `variaveis` (
  `variavel` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;






/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
