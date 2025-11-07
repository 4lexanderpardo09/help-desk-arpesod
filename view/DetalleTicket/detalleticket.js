var selected_condicion_clave = null;
var selected_condicion_nombre = null;

function init() {

}

$(document).ready(function () {

    var rol_id = $("#rol_idx").val();

    var tick_id = getUrlParameter('ID');

    $('#usuario_seleccionado').on('change', updateEnviarButtonState);

    listarDetalle(tick_id);

    $('#tickd_descrip').summernote({
        height: 200,
        lang: "es-ES",
        callbacks: {
            onImageUpload: function (image) {
                myimagetreat(image[0]);
            },
            onPaste: function (e) {
            }
        }
    });

    $('#tickd_descripusu').summernote({
        height: 200,
        lang: 'es-ES'
    });

    $('#tickd_descripusu').summernote('disable');

    // Inicializar Summernote para la nota de cierre en el modal
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

    tabla = $('#documentos_data').dataTable({
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

    getRespuestasRapidas();

});

function updateEnviarButtonState() {
    // El botón solo está habilitado si el checkbox está realmente marcado  
    var panelVisible = $('#panel_seleccion_usuario').is(':visible');

    var enabledCheckbox = $('#checkbox_avanzar_flujo').is(':checked');
    var enabledSelectUserAssign =panelVisible &&  !!$('#usuario_seleccionado').val();
    var enabled = enabledCheckbox == true || enabledSelectUserAssign == true;
    $('#btnenviar').prop('disabled', !enabled);
}

// al terminar de inicializar (por ejemplo justo después de getRespuestasRapidas();)
$('#btnenviar').prop('disabled', true); // deshabilitado por defecto
updateEnviarButtonState();


function getRespuestasRapidas() {

    $.post("../../controller/respuestarapida.php?op=combo", function (data) {
        $('#fast_answer_id').html('<option value="">Seleccionar</option>' + data);

    });

}

function myimagetreat(image) {
    var data = new FormData();
    data.append("file", image);
    $.ajax({
        url: '../../controller/tmp_upload.php',
        cache: false,
        contentType: false,
        processData: false,
        data: data,
        type: "post",
        success: function(data) {
            var image = $('<img>').attr('src', data);
            $('#tickd_descrip').summernote("insertNode", image[0]);
        },
        error: function(data) {
            console.log(data);
        }
    });
}

var getUrlParameter = function getUrlParameter(sParam) {
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
}

// Helper para quitar HTML y caracteres invisibles y dejar solo texto legible
function stripHtml(html) {
    if (!html) return '';
    // quitar etiquetas html
    var tmp = html.replace(/<[^>]*>/g, '');
    // reemplazar &nbsp; y otros espacios invisibles
    tmp = tmp.replace(/\u00A0/g, ' ').replace(/\u200B/g, '').trim();
    // colapsar múltiples espacios y saltos
    tmp = tmp.replace(/\s+/g, ' ').trim();
    return tmp;
}

function htmlToPlainText(html) {
    // usa tu función stripHtml para normalizar
    return stripHtml(html || '');
}

function enviarDetalle() {
    if ($('#tickd_descrip').summernote('isEmpty')) {
        swal("Atención", "Debe ingresar una respuesta o comentario.", "warning");
        $('#btnenviar').data('processing', false);
        updateEnviarButtonState();
        return false;
    }

    // 2) Validar plantilla vs contenido real (usar summernote('code'))
    var templateHtml = $('#tickd_descrip').data('template') || '';
    var currentHtml = $('#tickd_descrip').summernote ? $('#tickd_descrip').summernote('code') : $('#tickd_descrip').val() || '';

    var cleanTemplate = htmlToPlainText(templateHtml);
    var cleanContent = htmlToPlainText(currentHtml);

    // Opcional: console.log para debug (quita en producción)
    console.log("cleanTemplate:", JSON.stringify(cleanTemplate));
    console.log("cleanContent:", JSON.stringify(cleanContent));

    if (!cleanContent || cleanContent.length === 0) {
        swal("Atención", "Debe ingresar una respuesta o comentario.", "warning");
        $('#btnenviar').data('processing', false).prop('disabled', false);
        updateEnviarButtonState();
        return false;
    }

    if (cleanContent === cleanTemplate) {
        swal("Atención", "Debe agregar información adicional a la plantilla de descripción.", "warning");
        $('#btnenviar').data('processing', false).prop('disabled', false);
        updateEnviarButtonState();
        return false;
    }

    var formData = new FormData($('#detalle_form')[0]);
    formData.append("tick_id", getUrlParameter('ID'));
    formData.append("usu_id", $('#user_idx').val());
    formData.append("tickd_descrip", $('#tickd_descrip').summernote('code'));

    if ($('#checkbox_avanzar_flujo').is(':checked')) {
        if (decisionSeleccionada) {
            formData.append("decision_nombre", decisionSeleccionada);
        } else {
            formData.append("avanzar_lineal", "true");
        }
    }

    if ($('#panel_seleccion_usuario').is(':visible')) {
        var usu_asig = $('#usuario_seleccionado').val();
        console.log(usu_asig);
        
        if (!usu_asig) {
            swal("Atención", "Por favor, selecciona un usuario para asignar antes de enviar.", "warning");
            // liberar botón para que el usuario intente de nuevo
            $('#btnenviar').data('processing', false).prop('disabled', false);
            updateEnviarButtonState();
            return false;
        }
        // Añadir la asignación al formData para que el backend la procese
        formData.append('usu_asig', usu_asig);
        formData.append('assign_on_send', 'true'); // flag opcional para proceso server-side
    }

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
            // Siempre liberar el bloqueo del botón cuando la petición termine
            $('#btnenviar').data('processing', false);
            updateEnviarButtonState();
        }
    });
}


$(document).on('click', '#btnenviar', function () {
    var $btn = $(this);

    // Protección contra múltiples clicks: si ya está en procesamiento, salir
    if ($btn.data('processing')) return;

    // Si el checkbox está visible, exigir que esté marcado
    if ($('#checkbox_avanzar_flujo').is(':visible') && !$('#checkbox_avanzar_flujo').is(':checked')) {
        swal("Atención", "Debe marcar la opción para avanzar el flujo antes de enviar.", "warning");
        return false;
    }

    // Marcar en procesamiento y deshabilitar el botón
    $btn.data('processing', true).prop('disabled', true);

    selected_condicion_clave = null; // No hay clave de condición si se usa el botón de enviar normal
    enviarDetalle(); // enviarDetalle ahora liberará el botón en el complete del ajax
});

$(document).on('click', '#btn_registrar_evento', function () {
    var tick_id = getUrlParameter('ID');
    var answer_id = $('#fast_answer_id').val();
    var usu_id = $('#user_idx').val();
    var answer_text = $('#fast_answer_id option:selected').text();

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
                $.post("../../controller/ticket.php?op=registrar_error", { tick_id: tick_id, answer_id: answer_id, usu_id: usu_id, error_descrip: $('#error_descrip').val() })
                    .done(function() {
                        updateTicket(tick_id, usu_id);
                        listarDetalle(tick_id);
                        $('#fast_answer_id').val('');
                    })
                    .fail(function() {
                        swal("Error", "No se pudo registrar el evento.", "error");
                    });
            }
        });
    } else {
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
                $.post("../../controller/ticket.php?op=registrar_error", { tick_id: tick_id, answer_id: answer_id, usu_id: usu_id, error_descrip: $('#error_descrip').val() })
                    .done(function() {
                        swal("¡Registrado!", "El evento ha sido añadido al historial del ticket.", "success");
                        listarDetalle(tick_id);
                        $('#fast_answer_id').val('');
                    })
                    .fail(function() {
                        swal("Error", "No se pudo registrar el evento.", "error");
                    });
            }
        });
    }
});


$(document).on('click', '#btncerrarticket', function () {
    // Abre el modal para la nota de cierre
    $('#modal_nota_cierre').modal('show');
});

$(document).on('click', '#btn_confirmar_cierre', function() {
    var tick_id = getUrlParameter('ID');
    var usu_id = $('#user_idx').val();
    var nota_cierre = $('#nota_cierre_summernote').summernote('code');

    if ($('#nota_cierre_summernote').summernote('isEmpty')) {
        swal("Atención", "Debe ingresar una nota de cierre.", "warning");
        return;
    }

    var formData = new FormData();
    formData.append('tick_id', tick_id);
    formData.append('usu_id', usu_id);
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
            swal("¡Cerrado!", "El ticket ha sido cerrado correctamente.", "success");
            listarDetalle(tick_id);
        },
        error: function() {
            swal("Error", "No se pudo cerrar el ticket.", "error");
        }
    });
});

$(document).on("click", "#btn_aprobar_paso", function () {
    var tick_id = getUrlParameter('ID');
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
            $.post("../../controller/ticket.php?op=aprobar_paso", { tick_id: tick_id }, function (data) {
                swal("¡Aprobado!", "El ticket ha sido aprobado y continuará su flujo.", "success");
                listarDetalle(tick_id);
            }).fail(function (jqXHR) {
                swal("Error", "No se pudo completar la aprobación. Detalle: " + jqXHR.responseText, "error");
            });
        }
    });
});

$(document).on("click", "#btn_rechazar_paso", function () {
    var tick_id = getUrlParameter('ID');
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
            $.post("../../controller/ticket.php?op=rechazar_paso", { tick_id: tick_id }, function (data) {
                swal("¡Rechazado!", "El ticket ha sido rechazado y devuelto al paso anterior.", "success");
                listarDetalle(tick_id);
            }).fail(function (jqXHR) {
                swal("Error", "No se pudo completar el rechazo. Detalle: " + jqXHR.responseText, "error");
            });
        }
    });
});


function updateTicket(tick_id, usu_id) {
    $.post("../../controller/ticket.php?op=update", { tick_id: tick_id, usu_id: usu_id }, function (data) {
        swal({
            title: "Cerrado!",
            text: "El ticket ha sido cerrado correctamente.",
            type: "success",
            confirmButtonClass: "btn-success"
        }, function () {
            listarDetalle(tick_id);
        }
        );
    });

}

function listarDetalle(tick_id) {
    // Carga el historial del ticket
    $.post("../../controller/ticket.php?op=listardetalle", { tick_id: tick_id }, function (data) {
        $('#lbldetalle').html(data);
    });

    // Carga los datos principales del ticket y define las acciones
    $.post("../../controller/ticket.php?op=mostrar", { tick_id: tick_id }, function (data) {
        var ticketData = JSON.parse(data);
        console.log(ticketData);
        

        // --- Asignación de datos a la vista (tu código original) ---
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
        
        if (ticketData.siguientes_pasos_lineales[0].requiere_seleccion_manual) {
            console.log('entre');
            
            $('#panel_seleccion_usuario').show();
            var options = '<option value="">Seleccionar Usuario</option>';
            ticketData.usuarios_seleccionables.forEach(function(usuario) {
                options += '<option value="' + usuario.usu_id + '">' + usuario.usu_nom + ' ' + usuario.usu_ape + ' (' + usuario.usu_correo + ')</option>';
            });
            $('#usuario_seleccionado').html(options);
            $('#usuario_seleccionado').select2();
            $('#panel_checkbox_flujo').hide().data('acciones', null);
            $('#checkbox_avanzar_flujo').prop('checked', false).prop('disabled', true).hide();
        }

        // --- Reinserción del bloque de paso que tenías antes ---
        var pasoInfo = ticketData.paso_actual_info || {};

        if (pasoInfo && Object.keys(pasoInfo).length > 0) {
            if (pasoInfo.es_aprobacion == 1) {
                console.log("El paso actual es de aprobación");
                $('#panel_respuestas_rapidas').hide();
                $('#btnenviar').prop('disabled', false)
                $('#panel_aprobacion').show();
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
            // no tocar panel_aprobacion aquí (queda según la lógica principal)
        }


        // Ocultar paneles por defecto y resetear
        $('#panel_checkbox_flujo').hide().data('acciones', null);
        $('#btncerrarticket').hide().prop('disabled', true);
        $('#checkbox_avanzar_flujo').prop('checked', false).prop('disabled', true);

        // Evaluar permisos y estado del flujo
        var user_id = $('#user_idx').val();
        var isAssigned = String(ticketData.usu_asig) === String(user_id); // comparar como strings por seguridad
        console.log(isAssigned);
        
        var hasDecisions = ticketData.decisiones_disponibles && ticketData.decisiones_disponibles.length > 0;
        var hasNextLinear = ticketData.siguientes_pasos_lineales && ticketData.siguientes_pasos_lineales.length > 0;
        var aprobacion = ticketData.paso_actual_info ? ticketData.paso_actual_info.es_aprobacion : 0;
        var isLastStep = !hasDecisions && !hasNextLinear;

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
            // Si es el último paso: mostrar botón cerrar activado, ocultar / desactivar avanzar
            if (isLastStep) {
                $('#btncerrarticket').show().prop('disabled', false);
                $('#panel_checkbox_flujo').hide().data('acciones', null);
                $('#checkbox_avanzar_flujo').prop('disabled', true);
            } 
            // Si NO es el último paso: mostrar opción de avanzar (si aplica) y ocultar botón cerrar
            else {
                $('#btncerrarticket').hide().prop('disabled', true);

                if (hasDecisions || hasNextLinear) {
                    if (!hasNextLinear) {
                        $('#btncerrarticket').show().prop('disabled', false);
                    }
                    // Guardar acciones exactas para que el modal/checkbox las use
                    if(aprobacion == 1) {
                        console.log('entre');
                        $('#checkbox_avanzar_flujo').prop('disabled', true);
                        return;
                    }
                    // Si el panel de selección de usuario está visible, NO mostrar el checkbox de avance
                    if ($('#panel_seleccion_usuario').is(':visible')) {
                        // ocultar y deshabilitar checkbox de avance (la asignación se hará con ENVIAR)
                        $('#panel_checkbox_flujo').hide().data('acciones', null);
                        $('#checkbox_avanzar_flujo').prop('checked', false).prop('disabled', true).hide();
                    } else {
                        var acciones = {
                            decisiones: ticketData.decisiones_disponibles || [],
                            siguiente_paso: hasNextLinear
                        };
                        $('#panel_checkbox_flujo').data('acciones', acciones).show();
                        $('#checkbox_avanzar_flujo').prop('disabled', false).show();
                    }
                } else {
                    $('#panel_checkbox_flujo').hide().data('acciones', null);
                    $('#checkbox_avanzar_flujo').prop('disabled', true);
                }
            }
        } else {
            // No es usuario asignado: ocultar/desactivar todos los controles relacionados
            $('#panel_checkbox_flujo').hide().data('acciones', null);
            $('#btncerrarticket').hide().prop('disabled', true);
            $('#checkbox_avanzar_flujo').prop('disabled', true);
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
        
        if (ticketData.tick_estado_texto === 'Cerrado') {
            $('#boxdetalleticket').hide();
            return; // No procesar más si el ticket está cerrado
        }

    });
}

$(document).on('click', '#btn_asignar_usuario', function() {
    var tick_id = getUrlParameter('ID');
    var usu_asig = $('#usuario_seleccionado').val();
    var usu_id = $('#user_idx').val();

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
            $.post("../../controller/ticket.php?op=updateasignacion", { tick_id: tick_id, usu_asig: usu_asig, how_asig: usu_id })
                .done(function() {
                    swal("¡Asignado!", "El ticket ha sido asignado correctamente.", "success");
                    listarDetalle(tick_id);
                })
                .fail(function() {
                    swal("Error", "No se pudo asignar el ticket.", "error");
                });
        }
    });
});

$(document).on('change', '#checkbox_avanzar_flujo', function() {
    // Limpiar selección previa al cambiar el checkbox
    decisionSeleccionada = null;
    $(this).prop('checked', false); // Forzamos a que se desmarque para que el usuario confirme su acción

    var acciones = $('#panel_checkbox_flujo').data('acciones');

    // CASO 1: Hay decisiones (rutas) disponibles -> ABRIR MODAL
    if (acciones && acciones.decisiones.length > 0) {
        var options_html = '<option value="" selected disabled>-- Seleccione una opción --</option>';
        acciones.decisiones.forEach(function(d) {
            options_html += `<option value="${d.condicion_nombre}">${d.condicion_nombre}</option>`;
        });
        $('#select_siguiente_paso').html(options_html);
        $('#modal_seleccionar_paso_label').text('Seleccionar Decisión de Avance');
        $('#modal_seleccionar_paso .modal-body p').text('Este paso tiene múltiples caminos. Por favor, selecciona la decisión que deseas tomar:');
        $('#modal_seleccionar_paso').modal('show');
    } 
    // CASO 2: Hay avance lineal -> MARCAR CHECKBOX
    else if (acciones && acciones.siguiente_paso) {
        $(this).prop('checked', true); // Marcar el checkbox directamente
        swal("Avance Lineal", "Al enviar su respuesta, el ticket avanzará al siguiente paso.", "info");
    }
    updateEnviarButtonState();
});


$(document).on('click', '#btn_confirmar_paso_seleccionado', function() {
    var seleccion = $('#select_siguiente_paso').val();
    if (seleccion && seleccion !== '') {
        decisionSeleccionada = seleccion; // Guardamos la decisión
        $('#checkbox_avanzar_flujo').prop('checked', true); // Marcamos el checkbox
        $('#modal_seleccionar_paso').modal('hide');
        swal("Decisión registrada", "Tu elección \"" + decisionSeleccionada + "\" se aplicará al enviar la respuesta.", "info");
        updateEnviarButtonState();
    } else {
        swal("Atención", "Por favor, selecciona una opción para continuar.", "warning");
    }
});



init();