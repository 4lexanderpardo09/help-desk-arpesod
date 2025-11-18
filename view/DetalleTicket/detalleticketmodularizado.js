/* =================================================================
 * MODULO DE UTILIDADES
 * (Funciones puras y genéricas)
 * ================================================================= */
var Utils = {
    getUrlParameter: function (sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    },

    stripHtml: function (html) {
        if (!html) return '';
        var tmp = html.replace(/<[^>]*>/g, '');
        tmp = tmp.replace(/\u00A0/g, ' ').replace(/\u200B/g, '').trim();
        tmp = tmp.replace(/\s+/g, ' ').trim();
        return tmp;
    },

    htmlToPlainText: function (html) {
        return Utils.stripHtml(html || '');
    }
};

/* =================================================================
 * MODULO DE ESTADO
 * (Maneja las variables que cambian durante el uso de la página)
 * ================================================================= */
var AppState = {
    tick_id: null,
    rol_id: null,
    user_id: null,
    selected_condicion_clave: null,
    selected_condicion_nombre: null,
    decisionSeleccionada: null,
    isProcessing: false // Para evitar dobles clicks
};

/* =================================================================
 * MODULO DE UI (Interfaz de Usuario)
 * (Inicializa y manipula componentes de la UI)
 * ================================================================= */
var UI = {
    initSummernote: function () {
        $('#tickd_descrip').summernote({
            height: 200,
            lang: "es-ES",
            callbacks: {
                onImageUpload: function (image) {
                    UI.myimagetreat(image[0]);
                },
                onPaste: function (e) {
                }
            }
        });
        // ...el resto de tus inits de summernote...
        $('#tickd_descripusu').summernote({ height: 200, lang: 'es-ES' }).summernote('disable');
        
        $('#nota_cierre_summernote').summernote({
             height: 150,
             lang: "es-ES",
            toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
        ]
        });
    },

    initDataTables: function (tick_id) {
        $('#documentos_data').dataTable({
            "aProcessing": true,
            "aServerSide": true,
            "searching": false,
            "info": false,
            lengthChange: false,
            colReorder: true,
            "buttons": [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5',
            ],
            "ajax": {
                url: '../../controller/documento.php?op=listar',
                type: 'post',
                data: { tick_id: tick_id },
                dataType: 'json',
                error: function (e) {
                    console.log(e.responseText);
                }
            },
            "bDestroy": true,
            "responsive": true,
            "bInfo": true,
            "iDisplayLength": 3,
            "autoWidth": false,
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            }
        }).DataTable();
    },

    myimagetreat: function (image) {
        var data = new FormData();
        data.append("file", image);
        $.ajax({
            url: '../../controller/tmp_upload.php',
            // ...config de ajax...
            success: function(data) {
                var image = $('<img>').attr('src', data);
                $('#tickd_descrip').summernote("insertNode", image[0]);
            },
            error: function(data) {
                console.log(data);
            }
        });
    },

    updateEnviarButtonState: function () {
        var panelVisible = $('#panel_seleccion_usuario').is(':visible');
        var enabledCheckbox = $('#checkbox_avanzar_flujo').is(':checked');
        var enabledSelectUserAssign = panelVisible && !!$('#usuario_seleccionado').val();
        var enabled = enabledCheckbox || enabledSelectUserAssign;
        $('#btnenviar').prop('disabled', !enabled);
    },

    // Esta función recibe los datos de listarDetalle y actualiza el DOM
    renderTicketDetails: function(ticketData) {
        console.log(ticketData);

        $('#lbltickestado').html(ticketData.tick_estado);
        $('#lblprioridad').html(ticketData.pd_nom);
        $('#lblnomusuario').html(ticketData.usu_nom + ' ' + ticketData.usu_ape);
        $('#lblestado_tiempo').html(ticketData.estado_tiempo);
        $('#lblfechacrea').html(ticketData.fech_crea);
        $('#lblticketid').html("Detalle del ticket #" + ticketData.tick_id + " - " + ticketData.cats_nom);
        $('#cat_id').val(ticketData.cat_nom);
        $('#cats_id').val(ticketData.cats_nom);
        $('#emp_id').val(ticketData.emp_nom);
        $('#dp_id').val(ticketData.dp_nom);
        $('#tick_titulo').val(ticketData.tick_titulo);
        $('#tickd_descripusu').summernote('code', ticketData.tick_descrip);
        
        // Limpiar paneles de acciones antes de re-evaluar
        $('#panel_seleccion_usuario').hide();
        $('#usuario_seleccionado').html('');
        $('#panel_checkbox_flujo').hide().data('acciones', null);
        $('#checkbox_avanzar_flujo').prop('checked', false).prop('disabled', true).hide();
        $('#panel_aprobacion').hide();
        $('#panel_guia_paso').hide();
        $('#panel_guia_paso .attachment-section').remove(); // Limpiar adjuntos previos
        $('#btnenviar').show();
        $('#btncrearnovedad').show();
        $('#btncerrarticket').show();
        $('#panel_respuestas_rapidas').show();
        $('#btnresolvernovedad').hide();

        // --- Lógica de visualización de paneles ---
        if (ticketData.siguientes_pasos_lineales && ticketData.siguientes_pasos_lineales.length > 0 && ticketData.usuarios_seleccionables && ticketData.usuarios_seleccionables.length > 0) {
            console.log('Modo: Seleccionar Siguiente Usuario');
            
            $('#panel_seleccion_usuario').show();
            var options = '<option value="">Seleccionar Usuario</option>';
            ticketData.usuarios_seleccionables.forEach(function(usuario) {
                options += '<option value="' + usuario.usu_id + '">' + usuario.usu_nom + ' ' + usuario.usu_ape + ' (' + usuario.usu_correo + ')</option>';
            });
            $('#usuario_seleccionado').html(options);
            $('#usuario_seleccionado').select2();
            
            // Ocultar otras acciones de flujo
            $('#panel_checkbox_flujo').hide().data('acciones', null);
            $('#checkbox_avanzar_flujo').prop('checked', false).prop('disabled', true).hide();
        }

        // --- Reinserción del bloque de paso que tenías antes ---
        var pasoInfo = ticketData.paso_actual_info || {};

        if (pasoInfo && Object.keys(pasoInfo).length > 0) {
            if (pasoInfo.es_aprobacion == 1) {
                console.log("El paso actual es de aprobación");
                $('#panel_respuestas_rapidas').hide();
                $('#btnenviar').prop('disabled', false) // Habilitar 'Enviar' si es aprobación
                $('#panel_aprobacion').show();
                // Ocultar otros controles de avance
                $('#panel_checkbox_flujo').hide();
                $('#panel_seleccion_usuario').hide();
            } else {
                $('#panel_guia_paso').show();
                $('#guia_paso_nombre').text('Paso Actual: ' + (pasoInfo.paso_nombre || ''));
                $('#guia_paso_tiempo').text(pasoInfo.paso_tiempo_habil || '');
                
                if (pasoInfo.paso_descripcion) {
                    var pasoTemplate = pasoInfo.paso_descripcion;
                    // Setear el contenido en el editor y almacenar la plantilla
                    $('#tickd_descrip').summernote('code', pasoTemplate);
                    $('#tickd_descrip').data('template', pasoTemplate);
                } else {
                    $('#tickd_descrip').data('template', '');
                }

                if (pasoInfo.paso_nom_adjunto) {
                    var attachmentHtml = '<div class="attachment-section">' +
                                             '<p><strong>Adjunto del Paso:</strong></p>' +
                                             '<a href="../../public/document/paso/' + pasoInfo.paso_nom_adjunto + '" target="_blank">' + pasoInfo.paso_nom_adjunto + '</a>' +
                                           '</div>';
                    $('#panel_guia_paso .card-body').append(attachmentHtml);
                }
            }
        } else {
            // Si no hay info del paso, ocultamos la guía por seguridad
            $('#panel_guia_paso').hide();
        }

        // Ocultar paneles por defecto y resetear
        $('#panel_checkbox_flujo').hide().data('acciones', null);
        $('#btncerrarticket').hide().prop('disabled', true);
        $('#checkbox_avanzar_flujo').prop('checked', false).prop('disabled', true);

        // Evaluar permisos y estado del flujo
        var user_id = $('#user_idx').val();
        var isAssigned = String(ticketData.usu_asig) === String(user_id);
        console.log("Is Assigned: " + isAssigned);
        
        var hasDecisions = ticketData.decisiones_disponibles && ticketData.decisiones_disponibles.length > 0;
        var hasNextLinear = ticketData.siguientes_pasos_lineales && ticketData.siguientes_pasos_lineales.length > 0;
        var isApprovalStep = (pasoInfo.es_aprobacion == 1);
        var isSelectionStep = $('#panel_seleccion_usuario').is(':visible');
        
        // El último paso se redefine: no hay decisiones, no hay sig. paso lineal, Y no es un paso de selección de usuario
        var isLastStep = !hasDecisions && !hasNextLinear && !isSelectionStep;

        // Guardar flags en el contenedor para consultas posteriores (event handlers)
        $('#boxdetalleticket').data('isAssigned', isAssigned);
        $('#boxdetalleticket').data('isLastStep', isLastStep);

        // Si ya está cerrado, ocultar todo y salir
        if (ticketData.tick_estado_texto === 'Cerrado') {
            $('#boxdetalleticket').hide();
            return;
        }

        // Solo el usuario asignado puede ver/usar controles de avance o cierre
        if (isAssigned) {
            
            // Si es un paso de aprobación, ya mostramos el panel. Ocultar el resto.
            if (isApprovalStep) {
                $('#panel_checkbox_flujo').hide();
                $('#panel_seleccion_usuario').hide();
                $('#btncerrarticket').hide();
            }
            // Si es un paso de selección de usuario, ya lo mostramos. Ocultar el resto.
            else if (isSelectionStep) {
                 $('#panel_checkbox_flujo').hide();
                 $('#btncerrarticket').hide();
            }
            // Si es el último paso: mostrar botón cerrar, ocultar avanzar
            else if (isLastStep) {
                $('#btncerrarticket').show().prop('disabled', false);
                $('#panel_checkbox_flujo').hide();
                $('#panel_seleccion_usuario').hide();
            } 
            // Si NO es el último paso (y no es aprobación/selección): mostrar opción de avanzar
            else {
                $('#btncerrarticket').hide(); // Ocultar cierre si no es el último paso

                if (hasDecisions || hasNextLinear) {
                    var acciones = {
                        decisiones: ticketData.decisiones_disponibles || [],
                        siguiente_paso: hasNextLinear
                    };
                    $('#panel_checkbox_flujo').data('acciones', acciones).show();
                    $('#checkbox_avanzar_flujo').prop('disabled', false).show();
                }
            }
        } else {
            // No es usuario asignado: ocultar/desactivar todos los controles de acción
            $('#boxdetalleticket').hide(); // Ocultar toda la caja de respuesta
            $('#panel_checkbox_flujo').hide();
            $('#btncerrarticket').hide();
            $('#panel_aprobacion').hide();
            $('#panel_seleccion_usuario').hide();
        }

        
        // === Manejo seguro y robusto de mermaid ===
        if (ticketData.timeline_graph && ticketData.timeline_graph.length > 0) {
            $('#panel_linea_tiempo').show();

            const mermaidContainer = document.querySelector("#panel_linea_tiempo .mermaid");
            mermaidContainer.innerHTML = '';
            let graph = ticketData.timeline_graph.trim();
            graph = graph.replace(/graph\s+(TD|TB)/i, 'graph LR');
            mermaidContainer.textContent = graph;

            setTimeout(function () {
                if (window.mermaid) {
                    try {
                        mermaid.initialize({ startOnLoad: false });
                    } catch (e) {}

                    try {
                        const renderResult = mermaid.render ? mermaid.render('mermaid_graph_' + Date.now(), graph) : null;

                        if (renderResult && typeof renderResult.then === 'function') {
                            renderResult.then(res => {
                                mermaidContainer.innerHTML = (res.svg || res);
                            }).catch(err => {
                                console.error("mermaid.render (promise) falló:", err);
                                try { mermaid.init(undefined, mermaidContainer); } catch(e){ console.error(e); }
                            });
                        } else if (renderResult) {
                            mermaidContainer.innerHTML = (renderResult.svg || renderResult);
                        } else {
                            try {
                                mermaid.init(undefined, mermaidContainer);
                            } catch (err) {
                                console.error("mermaid.init falló:", err);
                            }
                        }
                    } catch (err) {
                        console.error("Error al renderizar con mermaid.render:", err);
                        try { mermaid.init(undefined, mermaidContainer); } catch(e){ console.error(e); }
                    }
                } else {
                    console.error("Mermaid no está cargado en window cuando intentamos renderizar.");
                }
            }, 50); 
        } else {
            $('#panel_linea_tiempo').hide();
        }

        // Si está Pausado (Novedad)
        if (ticketData.tick_estado_texto === 'Pausado') {
            $('#btnenviar').hide();
            $('#btncrearnovedad').hide();
            $('#btncerrarticket').hide();
            $('#panel_checkbox_flujo').hide();
            $('#panel_respuestas_rapidas').hide();
            $('#panel_aprobacion').hide();
            $('#panel_seleccion_usuario').hide();
            $('#boxdetalleticket').show(); // Asegurarse que el panel de respuesta esté oculto

            // Lógica para mostrar el botón de resolver novedad
            $.post("../../controller/ticket.php?op=get_novedad_abierta", { tick_id: tick_id }, function (novedadData) {
                var novedad = JSON.parse(novedadData);
                // Solo el usuario asignado A LA NOVEDAD puede resolverla
                if (novedad && String(novedad.usu_asig_novedad) === String(user_id)) {
                    $('#btnresolvernovedad').show();
                }
            });
            return; // Salir de la función aquí
        }
        
        // Al final, actualizar estado del botón
        UI.updateEnviarButtonState();
    }
};

/* =================================================================
 * MODULO DE SERVICIO DE DATOS
 * (Todas las llamadas AJAX para Cargar datos)
 * ================================================================= */
var DataService = {
    listarDetalle: function (tick_id) {
        // Carga el historial
        $.post("../../controller/ticket.php?op=listardetalle", { tick_id: tick_id }, function (data) {
            $('#lbldetalle').html(data);
        });

        // Carga los datos principales
        $.post("../../controller/ticket.php?op=mostrar", { tick_id: tick_id }, function (data) {
            var ticketData = JSON.parse(data);
            // En lugar de procesar aquí, pasamos los datos al módulo UI
            UI.renderTicketDetails(ticketData);
        });
    },

    getRespuestasRapidas: function () {
        $.post("../../controller/respuestarapida.php?op=combo", function (data) {
            $('#fast_answer_id').html('<option value="">Seleccionar</option>' + data);
        });
    },
    
    getUsuariosNovedad: function() {
        $.post("../../controller/usuario.php?op=combo", function (data) {
            $('#usu_asig_novedad').html(data);
        });
    },
    
    getNovedadAbierta: function(tick_id) {
        $.post("../../controller/ticket.php?op=get_novedad_abierta", { tick_id: tick_id }, function (novedadData) {
            var novedad = JSON.parse(novedadData);
            if (novedad && String(novedad.usu_asig_novedad) === String(AppState.user_id)) {
                $('#btnresolvernovedad').show();
            }
        });
    }
};

/* =================================================================
 * MODULO DE ACCIONES DE TICKET
 * (Lógica para *modificar* el estado del ticket)
 * ================================================================= */
var TicketActions = {

    isProcessing: false, // Propio estado de procesamiento para acciones

    /**
     * Lógica para enviar la respuesta principal, avanzar flujo o reasignar.
     * Reemplaza la función global enviarDetalle().
     */
    enviarDetalle: function () {
        if (TicketActions.isProcessing) return; // Evitar doble envío

        // 1) Validar que el summernote no esté vacío
        if ($('#tickd_descrip').summernote('isEmpty')) {
            swal("Atención", "Debe ingresar una respuesta o comentario.", "warning");
            return false; // Salir de la función
        }

        // 2) Validar plantilla vs contenido real
        var templateHtml = $('#tickd_descrip').data('template') || '';
        var currentHtml = $('#tickd_descrip').summernote('code') || '';
        var cleanTemplate = Utils.htmlToPlainText(templateHtml);
        var cleanContent = Utils.htmlToPlainText(currentHtml);

        if (!cleanContent || cleanContent.length === 0) {
            swal("Atención", "Debe ingresar una respuesta o comentario.", "warning");
            return false;
        }

        if (cleanContent === cleanTemplate) {
            swal("Atención", "Debe agregar información adicional a la plantilla de descripción.", "warning");
            return false;
        }
        
        // Marcar como procesando
        TicketActions.isProcessing = true;
        $('#btnenviar').prop('disabled', true);

        // 3) Construir FormData
        var formData = new FormData($('#detalle_form')[0]);
        formData.append("tick_id", AppState.tick_id);
        formData.append("usu_id", AppState.user_id);
        formData.append("tickd_descrip", $('#tickd_descrip').summernote('code'));

        // 4) Añadir datos de flujo/decisión
        if ($('#checkbox_avanzar_flujo').is(':checked')) {
            if (AppState.decisionSeleccionada) {
                formData.append("decision_nombre", AppState.decisionSeleccionada);
            } else {
                formData.append("avanzar_lineal", "true");
            }
        }

        // 5) Añadir datos de reasignación (si el panel está visible)
        if ($('#panel_seleccion_usuario').is(':visible')) {
            var usu_asig = $('#usuario_seleccionado').val();
            if (!usu_asig) {
                swal("Atención", "Por favor, selecciona un usuario para asignar antes de enviar.", "warning");
                TicketActions.isProcessing = false; // Liberar
                UI.updateEnviarButtonState(); // Reactivar botón según estado
                return false;
            }
            formData.append('usu_asig', usu_asig);
            formData.append('assign_on_send', 'true');
        }

        // 6) Enviar AJAX
        $.ajax({
            url: "../../controller/ticket.php?op=insertdetalle",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                var data;
                try { data = JSON.parse(response); } catch (e) {
                    console.error("Respuesta no válida del servidor:", response);
                    swal("Error", "El servidor devolvió una respuesta inesperada.", "error");
                    return;
                }

                if (data.status === 'success') {
                    swal({
                        title: "¡Éxito!",
                        text: "La acción se ha completado correctamente.",
                        type: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // Redirigir si fue reasignado, sino recargar
                    if (data.reassigned) {
                        setTimeout(function() { window.location.href = "../../view/ConsultarTicket/"; }, 1600);
                    } else {
                        setTimeout(function() { location.reload(); }, 1600);
                    }
                } else {
                    swal("Error", data.message || "No se pudo procesar la acción.", "error");
                }
            },
            error: function (jqXHR) {
                swal("Error Fatal", "Ocurrió un error con el servidor. Revisa la consola.", "error");
                console.error(jqXHR.responseText);
            },
            complete: function() {
                // Siempre liberar el bloqueo
                TicketActions.isProcessing = false;
                UI.updateEnviarButtonState();
            }
        });
    },

    /**
     * Lógica para crear una novedad.
     * @param {Element} form - El elemento <form> que se está enviando.
     */
    crearNovedad: function(form) {
        var formData = new FormData(form);
        formData.append('tick_id', AppState.tick_id);
        formData.append('usu_id', AppState.user_id);

        $.ajax({
            url: '../../controller/ticket.php?op=crear_novedad',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                var data = JSON.parse(response);
                if (data.status === 'success') {
                    $('#modal_crear_novedad').modal('hide');
                    swal({
                        title: "¡Éxito!",
                        text: "Novedad creada y ticket pausado. Redirigiendo a la lista...",
                        type: "success",
                        timer: 1600,
                        showConfirmButton: false
                    });
                    setTimeout(function() { window.location.href = "../../view/ConsultarTicket/"; }, 1700);
                } else {
                    swal("Error", data.message, "error");
                }
            },
            error: function () {
                swal("Error", "No se pudo crear la novedad.", "error");
            }
        });
    },

    /**
     * Lógica para resolver una novedad.
     */
    resolverNovedad: function() {
        swal({
            title: "¿Estás seguro?",
            text: "Se resolverá la novedad y el ticket volverá a su estado anterior.",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-success",
            confirmButtonText: "Sí, resolver",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false
        }, function (isConfirm) {
            if (isConfirm) {
                $.post("../../controller/ticket.php?op=resolver_novedad", { tick_id: AppState.tick_id, usu_id: AppState.user_id })
                    .done(function (response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            swal({
                                title: "¡Éxito!",
                                text: data.message + " Redirigiendo a la lista...",
                                type: "success",
                                timer: 1600,
                                showConfirmButton: false
                            });
                            setTimeout(function() { window.location.href = "../../view/ConsultarTicket/"; }, 1700);
                        } else {
                            swal("Error", data.message, "error");
                        }
                    })
                    .fail(function () {
                        swal("Error", "No se pudo resolver la novedad.", "error");
                    });
            }
        });
    },

    /**
     * Lógica para registrar un evento (Respuesta Rápida).
     */
    registrarEvento: function() {
        var answer_id = $('#fast_answer_id').val();
        var answer_text = $('#fast_answer_id option:selected').text();
        var error_descrip = $('#error_descrip').val();

        if (!answer_id) {
            swal("Atención", "Por favor, seleccione una respuesta rápida para registrar.", "warning");
            return;
        }

        if (answer_text.toLowerCase().trim() === "cierre forzoso") {
            swal({
                title: "¿Estás seguro?",
                text: "Se registrará este evento y se cerrará el ticket.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Sí, cerrar ticket",
                cancelButtonText: "Cancelar",
                closeOnConfirm: false
            }, function(isConfirm) {
                if (isConfirm) {
                    $.post("../../controller/ticket.php?op=registrar_error", { tick_id: AppState.tick_id, answer_id: answer_id, usu_id: AppState.user_id, error_descrip: error_descrip })
                        .done(function() {
                            TicketActions.cierreForzoso(); // Llamar a la acción de cierre forzoso
                            $('#fast_answer_id').val('');
                        })
                        .fail(function() {
                            swal("Error", "No se pudo registrar el evento.", "error");
                        });
                }
            });
        } else {
            // Lógica para otros eventos que no son cierre forzoso
            swal({
                title: "¿Estás seguro?",
                text: "Se registrará este evento en el historial del ticket y se marcará el ticket con este estado.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Sí, registrar ahora",
                cancelButtonText: "Cancelar",
                closeOnConfirm: false
            }, function(isConfirm) {
                if (isConfirm) {
                    $.post("../../controller/ticket.php?op=registrar_error", { tick_id: AppState.tick_id, answer_id: answer_id, usu_id: AppState.user_id, error_descrip: error_descrip })
                        .done(function(response) {
                            var data;
                            try { data = JSON.parse(response); } catch (e) { data = {}; }

                            if (data.reassigned) {
                                swal({
                                    title: "¡Registrado!",
                                    text: "El evento se registró y el ticket fue reasignado. Redirigiendo a la lista...",
                                    type: "success",
                                    timer: 1600,
                                    showConfirmButton: false
                                });
                                setTimeout(function() { window.location.href = "../../view/ConsultarTicket/"; }, 1700);
                            } else {
                                swal("¡Registrado!", "El evento ha sido añadido al historial del ticket.", "success");
                                DataService.listarDetalle(AppState.tick_id); // Recargar
                            }
                            $('#fast_answer_id').val('');
                        })
                        .fail(function() {
                            swal("Error", "No se pudo registrar el evento.", "error");
                        });
                }
            });
        }
    },

    /**
     * Lógica para el cierre forzoso (reemplaza a updateTicket).
     */
    cierreForzoso: function() {
        $.post("../../controller/ticket.php?op=update", { tick_id: AppState.tick_id, usu_id: AppState.user_id }, function (data) {
            swal({
                title: "¡Cerrado!",
                text: "El ticket ha sido cerrado correctamente. Redirigiendo a la lista...",
                type: "success",
                timer: 1600,
                showConfirmButton: false
            });
            setTimeout(function() { window.location.href = "../../view/ConsultarTicket/"; }, 1700);
        });
    },

    /**
     * Lógica para confirmar el cierre desde el modal.
     */
    confirmarCierre: function() {
        var nota_cierre = $('#nota_cierre_summernote').summernote('code');

        if ($('#nota_cierre_summernote').summernote('isEmpty')) {
            swal("Atención", "Debe ingresar una nota de cierre.", "warning");
            return;
        }

        var formData = new FormData();
        formData.append('tick_id', AppState.tick_id);
        formData.append('usu_id', AppState.user_id);
        formData.append('nota_cierre', nota_cierre);

        var files = $('#cierre_files')[0].files;
        for (var i = 0; i < files.length; i++) {
            formData.append('cierre_files[]', files[i]);
        }

        $.ajax({
            url: '../../controller/ticket.php?op=cerrar_con_nota',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#modal_nota_cierre').modal('hide');
                $('#nota_cierre_summernote').summernote('reset');
                $('#cierre_files').val('');
                
                swal({
                    title: "¡Cerrado!",
                    text: "El ticket ha sido cerrado correctamente. Redirigiendo a la lista...",
                    type: "success",
                    timer: 1600,
                    showConfirmButton: false
                });
                setTimeout(function() { window.location.href = "../../view/ConsultarTicket/"; }, 1700);
            },
            error: function() {
                swal("Error", "No se pudo cerrar el ticket.", "error");
            }
        });
    },

    /**
     * Lógica para aprobar un paso de flujo.
     */
    aprobarPaso: function () {
        swal({
            title: "¿Estás seguro de aprobar este paso?",
            text: "Una vez aprobado, el ticket avanzará al siguiente paso del flujo.",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-success",
            confirmButtonText: "Sí, ¡Aprobar!",
            cancelButtonText: "No, cancelar",
            closeOnConfirm: false
        },
        function (isConfirm) {
            if (isConfirm) {
                $.post("../../controller/ticket.php?op=aprobar_paso", { tick_id: AppState.tick_id }, function (data) {
                    swal({
                        title: "¡Aprobado!",
                        text: "El ticket ha sido aprobado. Redirigiendo a la lista...",
                        type: "success",
                        timer: 1600,
                        showConfirmButton: false
                    });
                    setTimeout(function() { window.location.href = "../../view/ConsultarTicket/"; }, 1700);
                }).fail(function (jqXHR) {
                    swal("Error", "No se pudo completar la aprobación. Detalle: " + jqXHR.responseText, "error");
                });
            }
        });
    },

    /**
     * Lógica para rechazar un paso de flujo.
     */
    rechazarPaso: function () {
        swal({
            title: "¿Estás seguro de rechazar este paso?",
            text: "Una vez rechazado, el ticket será devuelto al paso anterior.",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Sí, ¡Rechazar!",
            cancelButtonText: "No, cancelar",
            closeOnConfirm: false
        },
        function (isConfirm) {
            if (isConfirm) {
                $.post("../../controller/ticket.php?op=rechazar_paso", { tick_id: AppState.tick_id }, function (data) {
                    swal({
                        title: "¡Rechazado!",
                        text: "El ticket ha sido devuelto. Redirigiendo a la lista...",
                        type: "success",
                        timer: 1600,
                        showConfirmButton: false
                    });
                    setTimeout(function() { window.location.href = "../../view/ConsultarTicket/"; }, 1700);
                }).fail(function (jqXHR) {
                    swal("Error", "No se pudo completar el rechazo. Detalle: " + jqXHR.responseText, "error");
                });
            }
        });
    },
    
    /**
     * Lógica para el botón "Asignar Usuario" (no el del panel de envío).
     */
    asignarUsuario: function() {
        var usu_asig = $('#usuario_seleccionado').val();

        if (!usu_asig) {
            swal("Atención", "Por favor, seleccione un usuario para asignar el ticket.", "warning");
            return;
        }

        swal({
            title: "¿Estás seguro?",
            text: "Se asignará este ticket al usuario seleccionado.",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-primary",
            confirmButtonText: "Sí, asignar ahora",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false
        }, function(isConfirm) {
            if (isConfirm) {
                $.post("../../controller/ticket.php?op=updateasignacion", { tick_id: AppState.tick_id, usu_asig: usu_asig, how_asig: AppState.user_id })
                    .done(function() {
                        swal({
                            title: "¡Asignado!",
                            text: "El ticket ha sido asignado. Redirigiendo a la lista...",
                            type: "success",
                            timer: 1600,
                            showConfirmButton: false
                        });
                        setTimeout(function() { window.location.href = "../../view/ConsultarTicket/"; }, 1700);
                    })
                    .fail(function() {
                        swal("Error", "No se pudo asignar el ticket.", "error");
                    });
            }
        });
    }
};

/* =================================================================
 * MODULO DE MANEJADORES DE MODALES
 * (Lógica para abrir y confirmar modales)
 * ================================================================= */
var ModalHandlers = {
    
    abrirModalCierre: function() {
        $('#modal_nota_cierre').modal('show');
    },

    abrirModalDecision: function() {
        var acciones = $('#panel_checkbox_flujo').data('acciones') || { decisiones: [], siguiente_paso: false };

        if (acciones.decisiones && acciones.decisiones.length > 0) {
            var options_html = '<option value="" selected disabled>-- Seleccione una opción --</option>';
            acciones.decisiones.forEach(function(d) {
                options_html += `<option value="${d.condicion_nombre}">${d.condicion_nombre}</option>`;
            });
            $('#select_siguiente_paso').html(options_html);
            $('#modal_seleccionar_paso_label').text('Seleccionar Decisión de Avance');
            $('#modal_seleccionar_paso .modal-body p').text('Este paso tiene múltiples caminos. Por favor, selecciona la decisión que deseas tomar:');
            $('#modal_seleccionar_paso').modal('show');
        } else if (acciones.siguiente_paso) {
            // Avance lineal -> no hacemos más (el checkbox ya quedó marcado por el usuario)
            swal("Avance Lineal", "Al enviar su respuesta, el ticket avanzará al siguiente paso.", "info");
        } else {
            swal("Atención", "No hay acciones de avance disponibles.", "warning");
        }
    },

    
    confirmarPasoSeleccionado: function() {
        var seleccion = $('#select_siguiente_paso').val();
        if (seleccion && seleccion !== '') {
            AppState.decisionSeleccionada = seleccion; // Guardamos la decisión
            $('#checkbox_avanzar_flujo').prop('checked', true);
            $('#modal_seleccionar_paso').modal('hide');
            swal("Decisión registrada", "Tu elección \"" + AppState.decisionSeleccionada + "\" se aplicará al enviar la respuesta.", "info");
        } else {
            swal("Atención", "Por favor, selecciona una opción para continuar.", "warning");
        }
        UI.updateEnviarButtonState();
    },

    onModalDecisionClose: function() {
        if (!AppState.decisionSeleccionada) {
            $('#checkbox_avanzar_flujo').prop('checked', false);
            UI.updateEnviarButtonState();
        }
    }
};


/* =================================================================
 * MODULO ENLAZADOR DE EVENTOS
 * (El "pegamento". Asigna listeners a los elementos del DOM)
 * ================================================================= */
var EventBinder = {
    bindEvents: function () {
        
        // --- Eventos de UI ---
        $('#usuario_seleccionado').on('change', UI.updateEnviarButtonState);

        // --- Novedades ---
        $(document).on('click', '#btncrearnovedad', function() {
             $('#modal_crear_novedad').modal('show');
        });
        $('#novedad_form').on('submit', function (e) {
            e.preventDefault();
            TicketActions.crearNovedad(this); // 'this' es el formulario
        });
        $(document).on('click', '#btnresolvernovedad', TicketActions.resolverNovedad);

        // --- Envío principal ---
        $(document).on('click', '#btnenviar', function () {
            // La validación de "processing" está ahora DENTRO de enviarDetalle
            // La validación de paneles también está DENTRO
            TicketActions.enviarDetalle();
        });

        // --- Registrar Evento (Respuesta Rápida) ---
        $(document).on('click', '#btn_registrar_evento', TicketActions.registrarEvento);

        // --- Cierre de Ticket ---
        $(document).on('click', '#btncerrarticket', function() {
             $('#modal_nota_cierre').modal('show');
        });
        $(document).on('click', '#btn_confirmar_cierre', TicketActions.confirmarCierre);

        // --- Aprobación / Rechazo ---
        $(document).on('click', "#btn_aprobar_paso", TicketActions.aprobarPaso);
        $(document).on('click', "#btn_rechazar_paso", TicketActions.rechazarPaso);
        
        // --- Asignación (Botón separado) ---
        $(document).on('click', '#btn_asignar_usuario', TicketActions.asignarUsuario);

        // Reemplaza el handler antiguo por este
        $(document).on('change', '#checkbox_avanzar_flujo', function() {
            // Resetar decisión previa
            AppState.decisionSeleccionada = null;

            var $chk = $(this);
            var isChecked = $chk.is(':checked'); // estado después del click
            var acciones = $('#panel_checkbox_flujo').data('acciones') || { decisiones: [], siguiente_paso: false };

            if (isChecked) {
                // Usuario intentó marcar el checkbox
                if (acciones.decisiones && acciones.decisiones.length > 0) {
                    // Hay decisiones -> abrir modal y NO dejar el checkbox marcado hasta confirmar
                    $chk.prop('checked', false);
                    ModalHandlers.abrirModalDecision(); // se encargará de rellenar el select y mostrar modal
                } else if (acciones.siguiente_paso) {
                    // Avance lineal -> permitir marcarlo directamente
                    // (queda marcado porque el usuario lo hizo)
                    swal("Avance Lineal", "Al enviar su respuesta, el ticket avanzará al siguiente paso.", "info");
                } else {
                    // No hay acciones -> revertir y avisar
                    $chk.prop('checked', false);
                    swal("Atención", "No hay acciones de avance disponibles.", "warning");
                }
            } else {
                // Usuario desmarcó -> no hacemos nada especial (si tenías decisión previa, ya la reseteaste arriba)
            }
            UI.updateEnviarButtonState();
        });

        
        $(document).on('click', '#btn_confirmar_paso_seleccionado', ModalHandlers.confirmarPasoSeleccionado);
        
        $('#modal_seleccionar_paso').on('hidden.bs.modal', ModalHandlers.onModalDecisionClose);
    }
};

/* =================================================================
 * INICIALIZACIÓN PRINCIPAL
 * (El antiguo $(document).ready)
 * ================================================================= */
function init() {
    // 1. Configurar estado inicial
    AppState.rol_id = $("#rol_idx").val();
    AppState.user_id = $("#user_idx").val(); // Importante para TicketActions
    AppState.tick_id = Utils.getUrlParameter('ID'); // Importante para TicketActions

    // 2. Inicializar UI
    UI.initSummernote();
    UI.initDataTables(AppState.tick_id);
    $('#btnenviar').prop('disabled', true); // deshabilitado por defecto

    // 3. Cargar datos
    DataService.getRespuestasRapidas();
    DataService.getUsuariosNovedad();
    DataService.listarDetalle(AppState.tick_id); // Esta es la llamada principal que renderiza todo

    // 4. Enlazar todos los eventos
    EventBinder.bindEvents();
    
    // 5. Estado inicial del botón
    UI.updateEnviarButtonState();
}

// Iniciar la aplicación
$(document).ready(init);