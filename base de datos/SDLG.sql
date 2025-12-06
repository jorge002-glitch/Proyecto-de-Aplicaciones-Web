-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.32-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.12.0.7122
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para sdlg
CREATE DATABASE IF NOT EXISTS `sdlg` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci */;
USE `sdlg`;

-- Volcando estructura para tabla sdlg.conductores
CREATE TABLE IF NOT EXISTS `conductores` (
  `NOMBRE` varchar(50) DEFAULT NULL,
  `APELLIDO_PATERNO` varchar(50) DEFAULT NULL,
  `APELLIDO_MATERNO` varchar(50) DEFAULT NULL,
  `LICENCIA` varchar(25) DEFAULT NULL,
  `TELEFONO` varchar(15) DEFAULT NULL,
  `DIRECCION` varchar(100) DEFAULT NULL,
  `CVE_CONDUCTOR` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`CVE_CONDUCTOR`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Volcando datos para la tabla sdlg.conductores: ~6 rows (aproximadamente)
INSERT INTO `conductores` (`NOMBRE`, `APELLIDO_PATERNO`, `APELLIDO_MATERNO`, `LICENCIA`, `TELEFONO`, `DIRECCION`, `CVE_CONDUCTOR`) VALUES
	('jorge alberto', 'alamilla', 'gonzalez', 'LIC12250', '9933781010', 'calle 5, villahermosa', 1),
	('Juan Ernesto', 'Magaña', 'Leon', 'LIC13450', '9933853764', 'Calle 1, Villahermosa', 2),
	('Pablo', 'Lopez', 'Chable', 'LIC53621', '9931934501', 'Calle 2, Villahermosa', 3),
	('Jose', 'Trujillo', 'Lopez', 'LIC73827', '9934261748', 'Calle 3, Villahermosa', 4),
	('Armando', 'Beltran', 'Madero', 'LIC47282', '9932467496', 'Calle 4, Villahermosa', 5),
	('Kelly Michelle', 'Perera', 'Alvarez', 'LIC26188', '9932563492', 'Calle 5, Villahermosa', 6);

-- Volcando estructura para tabla sdlg.documentos_conductores
CREATE TABLE IF NOT EXISTS `documentos_conductores` (
  `CVE_CONDUCTOR` int(11) DEFAULT NULL,
  `TIPO` varchar(50) DEFAULT NULL,
  `FECHA_VENCIMIENTO` datetime DEFAULT NULL,
  `ACHIVO` text DEFAULT NULL,
  `CVE_DOCUMENTO` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`CVE_DOCUMENTO`),
  KEY `FK_REFERENCE_8` (`CVE_CONDUCTOR`),
  CONSTRAINT `FK_REFERENCE_8` FOREIGN KEY (`CVE_CONDUCTOR`) REFERENCES `conductores` (`CVE_CONDUCTOR`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Volcando datos para la tabla sdlg.documentos_conductores: ~2 rows (aproximadamente)
INSERT INTO `documentos_conductores` (`CVE_CONDUCTOR`, `TIPO`, `FECHA_VENCIMIENTO`, `ACHIVO`, `CVE_DOCUMENTO`) VALUES
	(1, 'Licencia', '2027-02-15 00:00:00', 'licencia_juan.pdf', 1),
	(2, 'INE', '2030-03-03 00:00:00', 'ine_pablo.pdf', 2);

-- Volcando estructura para tabla sdlg.documentos_taxi
CREATE TABLE IF NOT EXISTS `documentos_taxi` (
  `CVE_TAXI` int(11) DEFAULT NULL,
  `TIPO` varchar(50) DEFAULT NULL,
  `FECHA_VENCIMIENTO` datetime DEFAULT NULL,
  `ARCHIVO` text DEFAULT NULL,
  `CVE_DOCUMENTO` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`CVE_DOCUMENTO`),
  KEY `FK_REFERENCE_7` (`CVE_TAXI`),
  CONSTRAINT `FK_REFERENCE_7` FOREIGN KEY (`CVE_TAXI`) REFERENCES `taxis` (`CVE_TAXI`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Volcando datos para la tabla sdlg.documentos_taxi: ~2 rows (aproximadamente)
INSERT INTO `documentos_taxi` (`CVE_TAXI`, `TIPO`, `FECHA_VENCIMIENTO`, `ARCHIVO`, `CVE_DOCUMENTO`) VALUES
	(10, 'Seguro', '2025-08-01 00:00:00', 'seguro_dgt1234.pdf', 1),
	(11, 'Tarjeta Circulación', '2026-01-01 00:00:00', 'tarjeta_dgt8273.pdf', 2);

-- Volcando estructura para tabla sdlg.estados_cuota
CREATE TABLE IF NOT EXISTS `estados_cuota` (
  `DESCRIPCION` varchar(50) DEFAULT NULL,
  `CVE_ESTADO_CUOTA` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`CVE_ESTADO_CUOTA`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Volcando datos para la tabla sdlg.estados_cuota: ~3 rows (aproximadamente)
INSERT INTO `estados_cuota` (`DESCRIPCION`, `CVE_ESTADO_CUOTA`) VALUES
	('Pagado', 1),
	('Pendiente', 2),
	('Retrasado', 3);

-- Volcando estructura para tabla sdlg.mantenimiento
CREATE TABLE IF NOT EXISTS `mantenimiento` (
  `CVE_MANTENIMIENTO` int(11) NOT NULL,
  `CVE_TAXI` int(11) DEFAULT NULL,
  `CVE_PROVEEDORES` int(11) DEFAULT NULL,
  `FECHA` datetime DEFAULT NULL,
  `TIPO` varchar(50) DEFAULT NULL,
  `DESCRIPCION` text DEFAULT NULL,
  `COSTO` decimal(10,0) DEFAULT NULL,
  `KILOMETRAJE` int(11) DEFAULT NULL,
  `CVE_PIEZA` int(11) DEFAULT NULL,
  PRIMARY KEY (`CVE_MANTENIMIENTO`),
  KEY `FK_REFERENCE_2` (`CVE_TAXI`),
  KEY `FK_REFERENCE_3` (`CVE_PROVEEDORES`),
  KEY `FK_MANT_PIEZA` (`CVE_PIEZA`),
  CONSTRAINT `FK_MANT_PIEZA` FOREIGN KEY (`CVE_PIEZA`) REFERENCES `piezas` (`CVE_PIEZA`),
  CONSTRAINT `FK_REFERENCE_2` FOREIGN KEY (`CVE_TAXI`) REFERENCES `taxis` (`CVE_TAXI`),
  CONSTRAINT `FK_REFERENCE_3` FOREIGN KEY (`CVE_PROVEEDORES`) REFERENCES `proveedores` (`CVE_PROVEEDORES`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Volcando datos para la tabla sdlg.mantenimiento: ~2 rows (aproximadamente)
INSERT INTO `mantenimiento` (`CVE_MANTENIMIENTO`, `CVE_TAXI`, `CVE_PROVEEDORES`, `FECHA`, `TIPO`, `DESCRIPCION`, `COSTO`, `KILOMETRAJE`, `CVE_PIEZA`) VALUES
	(1001, 10, 1, '2025-03-10 10:30:00', 'Afinación', 'Cambio de bujías', 850, 154000, NULL),
	(1002, 5, 2, '2025-03-12 11:15:00', 'Motor', 'A', 2000, 90000, 6);

-- Volcando estructura para tabla sdlg.piezas
CREATE TABLE IF NOT EXISTS `piezas` (
  `CVE_PIEZA` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(80) DEFAULT NULL,
  `DESCRIPCION` text DEFAULT NULL,
  `PRECIO` decimal(10,2) DEFAULT NULL,
  `CANTIDAD` int(11) DEFAULT NULL,
  `CVE_PROVEEDORES` int(11) DEFAULT NULL,
  PRIMARY KEY (`CVE_PIEZA`),
  KEY `CVE_PROVEEDORES` (`CVE_PROVEEDORES`),
  CONSTRAINT `piezas_ibfk_1` FOREIGN KEY (`CVE_PROVEEDORES`) REFERENCES `proveedores` (`CVE_PROVEEDORES`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Volcando datos para la tabla sdlg.piezas: ~6 rows (aproximadamente)
INSERT INTO `piezas` (`CVE_PIEZA`, `NOMBRE`, `DESCRIPCION`, `PRECIO`, `CANTIDAD`, `CVE_PROVEEDORES`) VALUES
	(1, 'Filtro de aceite', 'Filtro para motor Nissan', 250.00, 10, 1),
	(2, 'Bujía NGK', 'Bujía estándar para taxi', 75.50, 40, 2),
	(3, 'Pastillas de freno', 'Juego de pastillas delanteras', 320.00, 15, 1),
	(4, 'Aceite sintético 5W30', 'Lubricante para motor', 450.00, 25, 3),
	(5, 'Correa de distribución', 'Correa para motor Toyota', 680.00, 8, 2),
	(6, 'Amortiguador delantero', 'Amortiguador compatible con Nissan Tsuru', 890.00, 12, 1),
	(7, 'filtro', 'filtro del agua del motor', 2000.00, 1, 4);

-- Volcando estructura para tabla sdlg.proveedores
CREATE TABLE IF NOT EXISTS `proveedores` (
  `NOMBRE` varchar(50) DEFAULT NULL,
  `APELLIDO_PATERNO` varchar(50) DEFAULT NULL,
  `APELLIDO_MATERNO` varchar(50) DEFAULT NULL,
  `TELEFONO` varchar(15) DEFAULT NULL,
  `DIRECCION` varchar(100) DEFAULT NULL,
  `CVE_PROVEEDORES` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`CVE_PROVEEDORES`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Volcando datos para la tabla sdlg.proveedores: ~4 rows (aproximadamente)
INSERT INTO `proveedores` (`NOMBRE`, `APELLIDO_PATERNO`, `APELLIDO_MATERNO`, `TELEFONO`, `DIRECCION`, `CVE_PROVEEDORES`) VALUES
	('Volksheri', 'Refaccionaria', '', '9931978571', 'Gaviotas Nte. Sector Explanada', 1),
	('Mayel', 'Lavadora', '', '9931643827', 'Av. Luis Donaldo Colosio', 2),
	('Villahermosa', 'Auto Refaccionaria', '', '9931601190', 'Aquiles Calderón Marchena', 3),
	('Auto Express', 'Taller Mecanico', '', '9933590780', 'Agrónomos, Gaviotas Sur', 4);

-- Volcando estructura para tabla sdlg.sindicatos
CREATE TABLE IF NOT EXISTS `sindicatos` (
  `CVE_SINDICATOS` int(11) NOT NULL,
  `CVE_TAXI` int(11) DEFAULT NULL,
  `NOMBRE` varchar(50) DEFAULT NULL,
  `FECHA_AFILIACION` datetime DEFAULT NULL,
  `OBSERVACIONES` text DEFAULT NULL,
  PRIMARY KEY (`CVE_SINDICATOS`),
  KEY `FK_REFERENCE_9` (`CVE_TAXI`),
  CONSTRAINT `FK_REFERENCE_9` FOREIGN KEY (`CVE_TAXI`) REFERENCES `taxis` (`CVE_TAXI`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Volcando datos para la tabla sdlg.sindicatos: ~2 rows (aproximadamente)
INSERT INTO `sindicatos` (`CVE_SINDICATOS`, `CVE_TAXI`, `NOMBRE`, `FECHA_AFILIACION`, `OBSERVACIONES`) VALUES
	(1, 10, 'SITTETAB', '2023-05-01 00:00:00', 'Activo'),
	(2, 11, 'UDTAB', '2015-07-01 00:00:00', 'Activo');

-- Volcando estructura para tabla sdlg.tarifas
CREATE TABLE IF NOT EXISTS `tarifas` (
  `CVE_CONDUCTOR` int(11) DEFAULT NULL,
  `CVE_TAXI` int(11) DEFAULT NULL,
  `CVE_ESTADO_CUOTA` int(11) DEFAULT NULL,
  `FECHA` datetime DEFAULT NULL,
  `HORA_ENTRADA` time DEFAULT NULL,
  `HORA_SALIDA` time DEFAULT NULL,
  `TARIFA_TOTAL` decimal(10,0) DEFAULT NULL,
  `CUOTA` decimal(10,0) DEFAULT NULL,
  `CVE_TARIFAS` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`CVE_TARIFAS`),
  KEY `FK_REFERENCE_4` (`CVE_CONDUCTOR`),
  KEY `FK_REFERENCE_5` (`CVE_TAXI`),
  KEY `FK_REFERENCE_6` (`CVE_ESTADO_CUOTA`),
  CONSTRAINT `FK_REFERENCE_4` FOREIGN KEY (`CVE_CONDUCTOR`) REFERENCES `conductores` (`CVE_CONDUCTOR`),
  CONSTRAINT `FK_REFERENCE_5` FOREIGN KEY (`CVE_TAXI`) REFERENCES `taxis` (`CVE_TAXI`),
  CONSTRAINT `FK_REFERENCE_6` FOREIGN KEY (`CVE_ESTADO_CUOTA`) REFERENCES `estados_cuota` (`CVE_ESTADO_CUOTA`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Volcando datos para la tabla sdlg.tarifas: ~2 rows (aproximadamente)
INSERT INTO `tarifas` (`CVE_CONDUCTOR`, `CVE_TAXI`, `CVE_ESTADO_CUOTA`, `FECHA`, `HORA_ENTRADA`, `HORA_SALIDA`, `TARIFA_TOTAL`, `CUOTA`, `CVE_TARIFAS`) VALUES
	(1, 10, 1, '2025-03-15 00:00:00', '06:00:00', '14:00:00', 500, 150, 1),
	(2, 11, 2, '2025-03-15 00:00:00', '14:00:00', '22:00:00', 470, 150, 2);

-- Volcando estructura para tabla sdlg.taxis
CREATE TABLE IF NOT EXISTS `taxis` (
  `CVE_CONDUCTOR` int(11) DEFAULT NULL,
  `PLACA` varchar(12) DEFAULT NULL,
  `MARCA` varchar(20) DEFAULT NULL,
  `MODELO` varchar(20) DEFAULT NULL,
  `ANO` int(11) DEFAULT NULL,
  `CVE_TAXI` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`CVE_TAXI`),
  KEY `FK_REFERENCE_1` (`CVE_CONDUCTOR`),
  CONSTRAINT `FK_REFERENCE_1` FOREIGN KEY (`CVE_CONDUCTOR`) REFERENCES `conductores` (`CVE_CONDUCTOR`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Volcando datos para la tabla sdlg.taxis: ~6 rows (aproximadamente)
INSERT INTO `taxis` (`CVE_CONDUCTOR`, `PLACA`, `MARCA`, `MODELO`, `ANO`, `CVE_TAXI`) VALUES
	(0, 'DGT2345', 'Chevrolet', 'Aveo', 2020, 1),
	(1, 'DGT1234', 'Nissan', 'Versa', 2018, 2),
	(2, 'DGT8273', 'Toyota', 'Avanza', 2020, 3),
	(3, 'DGT9122', 'Kia', 'Rio', 2017, 4),
	(1, 'DGT5566', 'Chevrolet', 'Spark', 2016, 5),
	(2, 'DGT9988', 'Hyundai', 'Accent', 2019, 6);

-- Volcando estructura para tabla sdlg.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `CVE_USUARIO` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(80) NOT NULL,
  `EMAIL` varchar(120) NOT NULL,
  `PASSWORD_HASH` varchar(255) NOT NULL,
  `CREADO_EN` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`CVE_USUARIO`),
  UNIQUE KEY `EMAIL` (`EMAIL`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Volcando datos para la tabla sdlg.usuarios: ~3 rows (aproximadamente)
INSERT INTO `usuarios` (`CVE_USUARIO`, `NOMBRE`, `EMAIL`, `PASSWORD_HASH`, `CREADO_EN`) VALUES
	(1, 'Jorge Alberto', 'jorge022@gmail.com', '$2y$10$Yguw0zMz1.tkZSb1vVzeb.UnphaCgu3.ZDOFJMHf6Y1i0WpkmAPLe', '2025-11-09 18:52:18'),
	(2, 'Jorge Alberto', 'jogealamilla030@gmail.com', '$2y$10$dfB6arXYdeS91Evfl1FtSuEF3t1vyQqzJRAE5lVXcMH9B8TyZvAve', '2025-11-28 08:07:49'),
	(3, 'Jorge Alberto', 'jorgealamilla030@gmail.com', '$2y$10$HKM8OORj98A34i/DrNxzVO.vbRPbiVK6Cq.cMsw1GOC4vvgR1Lh3O', '2025-11-28 08:12:49'),
	(4, 'fabrizio', 'fabcolla06@gmail.com', '$2y$10$Jlw5vovylZUNRG7C/.6RPuntqEU3soN1ELtQNT3NTDZnvJy5Izx7m', '2025-11-28 08:15:55');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
