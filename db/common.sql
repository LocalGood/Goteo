SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `image`
--

CREATE TABLE `image` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `size` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `image`
--

INSERT INTO `image` VALUES(1, 'avatar.png', 'image/png', 1469);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(60) NOT NULL,
  `about` text,
  `keywords` tinytext,
  `active` tinyint(1) NOT NULL,
  `avatar` int(11) DEFAULT NULL,
  `contribution` text,
  `twitter` tinytext,
  `facebook` tinytext,
  `google` tinytext,
  `identica` tinytext,
  `linkedin` tinytext,
  `worth` int(7) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `token` tinytext NOT NULL,
  `hide` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'No se ve publicamente',
  `confirmed` int(1) NOT NULL DEFAULT '0',
  `lang` varchar(2) DEFAULT NULL,
  `node` varchar(50) DEFAULT NULL,
  `home` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `user`
--

INSERT INTO `user` VALUES('root', 'Sysadmin', '', '', '', '', '', 1, 91, '', '', '', '', '', '', 0, '', '', '', 1, 1, 'es', 'goteo');
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_image`
--

CREATE TABLE `user_image` (
  `user` varchar(50) NOT NULL,
  `image` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user`,`image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_interest`
--

CREATE TABLE `user_interest` (
  `user` varchar(50) NOT NULL,
  `interest` int(12) NOT NULL,
  UNIQUE KEY `user_interest` (`user`,`interest`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Intereses de los usuarios';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_lang`
--

CREATE TABLE `user_lang` (
  `id` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `about` text,
  `keywords` tinytext,
  `contribution` text,
  UNIQUE KEY `id_lang` (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_login`
--

CREATE TABLE `user_login` (
  `user` varchar(50) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `oauth_token` text NOT NULL,
  `oauth_token_secret` text NOT NULL,
  `datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user`,`oauth_token`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_login_log`
--

CREATE TABLE `user_login_log` (
  `user` varchar(50) NOT NULL,
  `node` varchar(50) NOT NULL,
  `datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user`,`node`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_personal`
--

CREATE TABLE `user_personal` (
  `user` varchar(50) NOT NULL,
  `contract_name` varchar(255) DEFAULT NULL,
  `contract_surname` varchar(255) DEFAULT NULL,
  `contract_nif` varchar(15) DEFAULT NULL COMMENT 'Guardar sin espacios ni puntos ni guiones',
  `contract_email` varchar(256) DEFAULT NULL,
  `phone` varchar(9) DEFAULT NULL COMMENT 'guardar sin espacios ni puntos',
  `address` tinytext,
  `zipcode` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Datos personales de usuario';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_prefer`
--

CREATE TABLE `user_prefer` (
  `user` varchar(50) NOT NULL,
  `updates` int(1) NOT NULL DEFAULT '0',
  `threads` int(1) NOT NULL DEFAULT '0',
  `rounds` int(1) NOT NULL DEFAULT '0',
  `mailing` int(1) NOT NULL DEFAULT '0',
  `email` int(1) NOT NULL DEFAULT '0',
  `tips` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Preferencias de notificacion de usuario';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_review`
--

CREATE TABLE `user_review` (
  `user` varchar(50) NOT NULL,
  `review` bigint(20) unsigned NOT NULL,
  `ready` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Ha terminado con la revision',
  PRIMARY KEY (`user`,`review`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Asignacion de revision a usuario';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_role`
--

CREATE TABLE `user_role` (
  `user_id` varchar(50) NOT NULL,
  `role_id` varchar(50) NOT NULL,
  `node_id` varchar(50) NOT NULL,
  `datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `user_FK` (`user_id`),
  KEY `role_FK` (`role_id`),
  KEY `node_FK` (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
INSERT INTO `user_role` VALUES('root', 'root', '*', NULL);
INSERT INTO `user_role` VALUES('root', 'superadmin', '*', NULL);
INSERT INTO `user_role` VALUES('root', 'localadmin', '*', NULL);
INSERT INTO `user_role` VALUES('root', 'user', '*', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_skill`
--

CREATE TABLE `user_skill` (
  `user` varchar(50) NOT NULL,
  `skill` int(12) NOT NULL,
  PRIMARY KEY (`user`,`skill`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Asignacion de revision a usuario';

--
-- Estructura de tabla para la tabla `user_translang`
--

CREATE TABLE `user_translang` (
  `user` varchar(50) NOT NULL,
  `lang` varchar(2) NOT NULL,
  PRIMARY KEY (`user`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Idiomas de traductores';

-- --------------------------------------------------------

INSERT INTO `user_translang` (`user`, `lang`) VALUES
('root', 'ca'),
('root', 'de'),
('root', 'el'),
('root', 'en'),
('root', 'es'),
('root', 'eu'),
('root', 'fr'),
('root', 'gl'),
('root', 'it'),
('root', 'nl'),
('root', 'pt');

--
-- Estructura de tabla para la tabla `user_translate`
--

CREATE TABLE `user_translate` (
  `user` varchar(50) NOT NULL,
  `type` varchar(10) NOT NULL COMMENT 'Tipo de contenido',
  `item` varchar(50) NOT NULL COMMENT 'id del contenido',
  `ready` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Ha terminado con la traduccion',
  PRIMARY KEY (`user`,`type`,`item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Asignacion de traduccion a usuario';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_web`
--

CREATE TABLE `user_web` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `url` tinytext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Webs de los usuarios';
