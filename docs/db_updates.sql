CREATE TABLE td_documento_cierre (
  doc_cierre_id INT(11) NOT NULL AUTO_INCREMENT,
  tick_id INT(11) NOT NULL,
  doc_nom VARCHAR(255) NOT NULL,
  fech_crea DATETIME NOT NULL,
  est INT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (doc_cierre_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;