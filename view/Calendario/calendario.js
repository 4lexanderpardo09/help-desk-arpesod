function init(){

}

$(document).ready(function() {

    var usu_id = $('#user_idx').val();
    var rol_id = $('#rol_idx').val();

    var calendarConfig = {
        lang: 'es',
        header:{
            left: 'prev,today,next',
            center: 'title',
            right: 'month,basicWeek,basicDay'
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            basicWeek: 'Semana',
            basicDay: 'Dia'
        },
        timeFormat: 'H:mm',
        events: {
            url: '',
            method: 'POST',
            data: {}
        },
        eventRender: function(event, element) {
            
            // 1. Función para obtener la clase del badge según el estado
            function getEstadoBadge(estado) {
                switch (estado) {
                    case 'Abierto':
                        return 'badge-estado-abierto';
                    case 'Cerrado':
                        return 'badge-estado-cerrado';
                    default:
                        return 'badge-estado-default';
                }
            }

            function getPrioridadBadge(prioridad) {
                switch (prioridad) {
                    case 'Baja':
                        return 'badge-prioridad-baja';
                    case 'Media':
                        return 'badge-prioridad-media';
                    default:
                        return 'badge-prioridad-alta';
                }
            }
            
            // 2. Construimos el contenido HTML con la nueva estructura y clases
            var popoverContent;
            
            // Contenido común para ambos roles
            let estadoHtml = `
                <div class="popover-detail-row">
                    <i class="fas fa-flag popover-icon"></i>
                    <div><strong>Estado:</strong> <span class="badge-estado ${getEstadoBadge(event.estado)}">${event.estado}</span></div>
                </div>
            `;

            let prioridadHtml = `
                <div class="popover-detail-row">
                    <i class="font-icon font-icon-chart-2 popover-icon"></i>
                    <div><strong>Prioridad:</strong> <span class="badge-prioridad ${getPrioridadBadge(event.prioridad)}">${event.prioridad}</span></div>
                </div>
            `;
            
            let descripcionHtml = `
                <div class="popover-detail-row">
                    <i class="fas fa-align-left popover-icon"></i>
                    <div><strong>Descripción:</strong><br>${event.descripcion || 'Sin descripción'}</div>
                </div>
            `;

            if (rol_id == 1) { // Rol Usuario

                if(event.usuasignado != null){

                let asignadoValHtml = `
                    <div class="popover-detail-row">
                        <i class="fas fa-user-tag popover-icon"></i>
                        <div><strong>Agente asignado:</strong> ${event.usuasignado || 'N/A'}</div>
                    </div>
                `;
                popoverContent = asignadoValHtml + estadoHtml + prioridadHtml + descripcionHtml;

                }else{

                let asignadoValHtml = `
                    <div class="popover-detail-row">
                        <i class="fas fa-user-tag popover-icon"></i>
                        <div><strong>Agente asignado:</strong> Sin agente</div>
                    </div>
                `;
                popoverContent = asignadoValHtml + estadoHtml + prioridadHtml + descripcionHtml;
                }

            } else { // Rol Soporte
                let usuarioHtml = `
                    <div class="popover-detail-row">
                        <i class="fas fa-user popover-icon"></i>
                        <div><strong>Ticket de:</strong> ${event.nombre || 'N/A'}</div>
                    </div>
                `;
                popoverContent = usuarioHtml + estadoHtml + prioridadHtml + descripcionHtml;
            }

            // 3. Inicializamos el Popover con la clase personalizada
            element.popover({
                title: `<i class="fas fa-ticket-alt"></i> ${event.title}`, // Título con ícono
                content: popoverContent,
                html: true,
                trigger: 'hover',
                placement: 'top',
                container: 'body',
                // ¡Importante! Aplicamos la clase CSS principal que definimos
                customClass: '<div class="popover calendar-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>'
            });
        },


        eventClick: function(event) {
            window.location.href = '../../view/DetalleTicket/?ID=' + event.id;
        }
    };

    if (rol_id == 1) {
        calendarConfig.events.url = '../../controller/ticket.php?op=calendario_x_usu';
        calendarConfig.events.data = { usu_id: usu_id };
    } else {
        calendarConfig.events.url = '../../controller/ticket.php?op=calendario_x_usu_asig';
        calendarConfig.events.data = { usu_asig: usu_id };
    }
    
    $("#idcalendar").fullCalendar(calendarConfig);
    

});


init();