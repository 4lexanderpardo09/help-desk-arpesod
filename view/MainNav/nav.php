<?php
if ($_SESSION['rol_id'] == 1) {
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
} else if ($_SESSION['rol_id'] == 2) {
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
	            </ul>
	        </li>
        </ul>
    </nav>
<?php
}
?>