<?php
if ($_SESSION['rol_id'] == 1 && $_SESSION['rol_id_real'] != 3) {
?>
    <nav class="side-menu">
        <ul class="side-menu-list">
            <li class="blue-dirty">
                <a href="..\Home\">
                    <i class="font-icon font-icon-dashboard"></i>
                    <span class="lbl">Inicio</span>
                </a>
            </li>
            <li class="blue-dirty">
                <a href="..\NuevoTicket\">
                    <i class="tag-color grey-blue"></i>
                    <span class="lbl">Nuevo Ticket</span>
                </a>
            </li>
            <li class="blue-dirty">
                <a href="..\ConsultarTicket\">
                    <i class="tag-color grey-blue"></i>
                    <span class="lbl">Consultar Ticket</span>
                </a>
            </li>
        </ul>
    </nav>
<?php
} else if ($_SESSION['rol_id'] == 2 && $_SESSION['rol_id_real'] != 3) {
?>
    <nav class="side-menu">
        <ul class="side-menu-list">
            <li class="blue-dirty">
                <a href="..\Home\">
                    <i class="font-icon font-icon-dashboard"></i>
                    <span class="lbl">Inicio</span>
                </a>
            </li>
            <li class="blue-dirty">
                <a href="..\NuevoTicket\">
                    <i class="tag-color grey-blue"></i>
                    <span class="lbl">Nuevo Ticket</span>
                </a>
            </li>
            <li class="blue-dirty">
                <a href="..\ConsultarTicket\">
                    <i class="tag-color grey-blue"></i>
                    <span class="lbl">Consultar Ticket Asignados</span>
                </a>
            </li>
            <li class="blue-dirty">
                <a href="..\ConsultarTicketAgentes\">
                    <i class="tag-color grey-blue"></i>
                    <span class="lbl">Consultar Ticket Creados</span>
                </a>
            </li>
            <li class="blue-dirty">
                <a href="..\ConsultarHistorialTicket\">
                    <i class="tag-color grey-blue"></i>
                    <span class="lbl">Consultar Ticket Historial</span>
                </a>
            </li>
            <li class="menu-footer" style="padding: 20px 0; text-align: center; color: #a3a3a3; font-size: 12px; border-top: 1px solid #e0e0e0;">
                <div>Desarrollado por departamento de Sistema</div>
                <div>&copy; <?php echo date("Y"); ?> Arpesod SAS</div>
            </li>
        </ul>
    </nav>
<?php
}else if ($_SESSION['rol_id'] == 2 || $_SESSION['rol_id'] == 1 && $_SESSION['rol_id_real'] == 3) {
?>
    <nav class="side-menu">
        <ul class="side-menu-list">
            <li class="blue-dirty">
                <a href="..\Home\">
                    <i class="font-icon font-icon-dashboard"></i>
                    <span class="lbl">Inicio</span>
                </a>
            </li>
            <li class="blue-dirty">
                <a href="..\ConsultarTicket\">
                    <i class="tag-color grey-blue"></i>
                    <span class="lbl">Consultar Ticket</span>
                </a>
            </li>
            <li class="blue-dirty">
                <a href="..\ConsultarHistorialTicket\">
                    <i class="tag-color grey-blue"></i>
                    <span class="lbl">Consultar Ticket Historial</span>
                </a>
            </li>
            <li class="grey with-sub">
	            <span>
	                <i class="font-icon font-icon-dashboard"></i>
	                <span class="lbl">Gestion</span>
	            </span>
	            <ul>
                    <li class="blue-dirty">
                        <a href="..\GestionUsuario\">
                            <span class="lbl">Gestion usuarios</span>
                        </a>
                    </li>
                    <li class="blue-dirty">
                        <a href="..\GestionPrioridad\">
                            <span class="lbl">Gestion prioridad</span>
                        </a>
                    </li>
                    <li class="blue-dirty">
                        <a href="..\GestionCategoria\">
                            <span class="lbl">Gestion categoria</span>
                        </a>
                    </li>
                    <li class="blue-dirty">
                        <a href="..\GestionSubcategoria\">
                            <span class="lbl">Gestion subcategoria</span>
                        </a>
                    </li>
                    <li class="blue-dirty">
                        <a href="..\GestionDepartamento\">
                            <span class="lbl">Gestion departamento</span>
                        </a>
                    </li>
                    <li class="blue-dirty">
                        <a href="..\GestionEmpresa\">
                            <span class="lbl">Gestion empresa</span>
                        </a>
                    </li>
                    <li class="blue-dirty">
                        <a href="..\GestionRespuesta\">
                            <span class="lbl">Gestion respuesta rapida</span>
                        </a>
                    </li>
                    <li class="blue-dirty">
                        <a href="..\GestionDestinatario\">
                            <span class="lbl">Gestion destinatario ticket</span>
                        </a>
                    </li>
                    <li class="grey with-sub">
                        <span>
                            <span class="lbl">Gestion flujos</span>
                        </span>
                        <ul>
                            <li class="blue-dirty">
                                <a href="..\GestionFlujo\">
                                    <span class="lbl">Flujos</span>
                                </a>
                            </li>
                            <li class="blue-dirty">
                                <a href="..\GestionMapeoFlujo\">
                                    <span class="lbl">Mapeo de flujos</span>
                                </a>
                            </li>
                            <li class="blue-dirty">
                                <a href="..\GestionReglaAprobacion\">
                                    <span class="lbl">Reglas de aprobacion</span>
                                </a>
                            </li>
                        </ul>
                    </li>    
	            </ul>
	        </li>
        </ul>
    </nav>
<?php
}
?>