CREATE TABLE `tm_cargo` (
  `car_id` int(11) NOT NULL AUTO_INCREMENT,
  `car_nom` varchar(150) NOT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`car_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tm_categoria` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_nom` varchar(150) NOT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categoria_empresa` (
  `cat_emp_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  PRIMARY KEY (`cat_emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categoria_departamento` (
  `cat_dep_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_id` int(11) NOT NULL,
  `dp_id` int(11) NOT NULL,
  PRIMARY KEY (`cat_dep_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tm_departamento` (
  `dp_id` int(11) NOT NULL AUTO_INCREMENT,
  `dp_nom` varchar(150) NOT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`dp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `td_documento` (
  `doc_id` int(11) NOT NULL AUTO_INCREMENT,
  `tick_id` int(11) NOT NULL,
  `doc_nom` varchar(400) NOT NULL,
  `fech_crea` datetime NOT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`doc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `td_documento_detalle` (
  `det_id` int(11) NOT NULL AUTO_INCREMENT,
  `tickd_id` int(11) NOT NULL,
  `det_nom` varchar(400) NOT NULL,
  `fech_crea` datetime NOT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`det_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `td_empresa` (
  `emp_id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_nom` varchar(150) NOT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `empresa_usuario` (
  `emp_usu_id` int(11) NOT NULL AUTO_INCREMENT,
  `usu_id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `fech_crea` datetime NOT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`emp_usu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tm_flujo` (
  `flujo_id` int(11) NOT NULL AUTO_INCREMENT,
  `flujo_nom` varchar(150) NOT NULL,
  `cats_id` int(11) NOT NULL,
  `requiere_aprobacion_jefe` tinyint(1) NOT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`flujo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tm_regla_mapeo` (
  `regla_id` int(11) NOT NULL AUTO_INCREMENT,
  `cats_id` int(11) NOT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`regla_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `regla_creadores` (
  `regla_cre_id` int(11) NOT NULL AUTO_INCREMENT,
  `regla_id` int(11) NOT NULL,
  `creador_car_id` int(11) NOT NULL,
  PRIMARY KEY (`regla_cre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `regla_asignados` (
  `regla_asi_id` int(11) NOT NULL AUTO_INCREMENT,
  `regla_id` int(11) NOT NULL,
  `asignado_car_id` int(11) NOT NULL,
  PRIMARY KEY (`regla_asi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tm_flujo_paso` (
  `paso_id` int(11) NOT NULL AUTO_INCREMENT,
  `flujo_id` int(11) NOT NULL,
  `paso_orden` int(11) NOT NULL,
  `paso_nombre` varchar(150) NOT NULL,
  `cargo_id_asignado` int(11) NOT NULL,
  `paso_tiempo_habil` int(11) NOT NULL,
  `paso_descripcion` text NOT NULL,
  `requiere_seleccion_manual` tinyint(1) NOT NULL,
  `es_tarea_nacional` tinyint(1) NOT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`paso_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tm_conversacion` (
  `conv_id` int(11) NOT NULL AUTO_INCREMENT,
  `de_usu_id` int(11) NOT NULL,
  `para_usu_id` int(11) NOT NULL,
  `fech_crea` datetime NOT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`conv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `td_mensaje` (
  `men_id` int(11) NOT NULL AUTO_INCREMENT,
  `conv_id` int(11) NOT NULL,
  `de_usu_id` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `fech_crea` datetime NOT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`men_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tm_notificacion` (
  `not_id` int(11) NOT NULL AUTO_INCREMENT,
  `usu_id` int(11) NOT NULL,
  `not_mensaje` varchar(400) NOT NULL,
  `tick_id` int(11) NOT NULL,
  `fech_not` datetime NOT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`not_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tm_organigrama` (
  `org_id` int(11) NOT NULL AUTO_INCREMENT,
  `car_id` int(11) NOT NULL,
  `jefe_car_id` int(11) NOT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`org_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `td_prioridad` (
  `pd_id` int(11) NOT NULL AUTO_INCREMENT,
  `pd_nom` varchar(150) NOT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`pd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tm_regional` (
  `reg_id` int(11) NOT NULL AUTO_INCREMENT,
  `reg_nom` varchar(150) NOT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`reg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tm_fast_answer` (
  `answer_id` int(11) NOT NULL AUTO_INCREMENT,
  `answer_nom` varchar(150) NOT NULL,
  `es_error_proceso` tinyint(1) NOT NULL,
  `fech_crea` datetime NOT NULL,
  `fech_modi` datetime DEFAULT NULL,
  `fech_elim` datetime DEFAULT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`answer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tm_subcategoria` (
  `cats_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_id` int(11) NOT NULL,
  `pd_id` int(11) NOT NULL,
  `cats_nom` varchar(150) NOT NULL,
  `cats_descrip` text NOT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`cats_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tm_ticket` (
  `tick_id` int(11) NOT NULL AUTO_INCREMENT,
  `usu_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `cats_id` int(11) NOT NULL,
  `pd_id` int(11) NOT NULL,
  `tick_titulo` varchar(250) NOT NULL,
  `tick_descrip` text NOT NULL,
  `tick_estado` varchar(15) NOT NULL,
  `error_proceso` int(11) DEFAULT NULL,
  `fech_crea` datetime NOT NULL,
  `usu_asig` int(11) DEFAULT NULL,
  `paso_actual_id` int(11) DEFAULT NULL,
  `how_asig` int(11) DEFAULT NULL,
  `est` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `dp_id` int(11) NOT NULL,
  `fech_cierre` datetime DEFAULT NULL,
  PRIMARY KEY (`tick_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `th_ticket_asignacion` (
  `th_id` int(11) NOT NULL AUTO_INCREMENT,
  `tick_id` int(11) NOT NULL,
  `usu_asig` int(11) NOT NULL,
  `how_asig` int(11) DEFAULT NULL,
  `paso_id` int(11) DEFAULT NULL,
  `fech_asig` datetime NOT NULL,
  `asig_comentario` text NOT NULL,
  `est` int(11) NOT NULL,
  `estado_tiempo_paso` varchar(15) DEFAULT NULL,
  `error_code_id` int(11) DEFAULT NULL,
  `error_descrip` text,
  PRIMARY KEY (`th_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `td_ticketdetalle` (
  `tickd_id` int(11) NOT NULL AUTO_INCREMENT,
  `tick_id` int(11) NOT NULL,
  `usu_id` int(11) NOT NULL,
  `tickd_descrip` text NOT NULL,
  `fech_crea` datetime NOT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`tickd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tm_usuario` (
  `usu_id` int(11) NOT NULL AUTO_INCREMENT,
  `usu_nom` varchar(150) NOT NULL,
  `usu_ape` varchar(150) NOT NULL,
  `usu_correo` varchar(150) NOT NULL,
  `usu_pass` varchar(150) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `reg_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `dp_id` int(11) DEFAULT NULL,
  `es_nacional` tinyint(1) NOT NULL,
  `fech_crea` datetime NOT NULL,
  `fech_modi` datetime DEFAULT NULL,
  `fech_elim` datetime DEFAULT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`usu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tm_regla_aprobacion` (
  `regla_id` int(11) NOT NULL AUTO_INCREMENT,
  `creador_car_id` int(11) NOT NULL,
  `aprobador_usu_id` int(11) NOT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`regla_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tm_destinatario` (
  `dest_id` int(11) NOT NULL AUTO_INCREMENT,
  `answer_id` int(11) NOT NULL,
  `usu_id` int(11) NOT NULL,
  `dp_id` int(11) NOT NULL,
  `cats_id` int(11) NOT NULL,
  `fech_crea` datetime NOT NULL,
  `fech_modi` datetime DEFAULT NULL,
  `fech_elim` datetime DEFAULT NULL,
  `est` int(11) NOT NULL,
  PRIMARY KEY (`dest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
