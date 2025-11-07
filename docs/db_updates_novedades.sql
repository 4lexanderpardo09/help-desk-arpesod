
-- Agregar nuevo estado para el ticket
ALTER TABLE `tm_ticket` MODIFY `tick_estado` ENUM('Abierto','Cerrado','Pausado') DEFAULT 'Abierto';

-- Tabla para gestionar las novedades de los tickets
CREATE TABLE `th_ticket_novedad` (
  `novedad_id` int(11) NOT NULL AUTO_INCREMENT,
  `tick_id` int(11) NOT NULL,
  `paso_id_pausado` int(11) NOT NULL,
  `usu_asig_novedad` int(11) NOT NULL,
  `usu_crea_novedad` int(11) NOT NULL,
  `descripcion_novedad` text NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `estado_novedad` enum('Abierta','Resuelta') NOT NULL DEFAULT 'Abierta',
  PRIMARY KEY (`novedad_id`),
  KEY `tick_id` (`tick_id`),
  CONSTRAINT `th_ticket_novedad_ibfk_1` FOREIGN KEY (`tick_id`) REFERENCES `tm_ticket` (`tick_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

