function init() {

}
boxdetalleticket
$(document).ready(function () {

    var rol_id = $("#rol_idx").val();

    var tick_id = getUrlParameter('ID');

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
        },
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
        ]
    });

    $('#tickd_descripusu').summernote({
        height: 200,
        lang: 'es-ES',
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
        ]
    });

    $('#tickd_descripusu').summernote('disable');

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

function getRespuestasRapidas() {

    $.post("../../controller/respuestarapida.php?op=combo", function (data) {
        $('#fast_answer_id').html('<option value="">Seleccionar</option>' + data);

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

$(document).on('click', '#btnenviar', function () {
    if ($('#tickd_descrip').summernote('isEmpty')) {
        swal("Atención", "Debe ingresar una respuesta", "warning");
        return false;
    }

    var tick_id = getUrlParameter('ID');
    var usu_id = $('#user_idx').val();
    var tickd_descrip = $('#tickd_descrip').val();

    var formData = new FormData($('#detalle_form')[0])

    formData.append("tick_id", tick_id);
    formData.append("usu_id", usu_id);
    formData.append("tickd_descrip", tickd_descrip);

     // --- MODIFICADO: Lógica para enviar los datos del flujo ---
    if ($('#checkbox_avanzar_flujo').is(':checked')) {
        var selected_siguiente_paso_id = $('#selected_siguiente_paso_id').val();
        if (!selected_siguiente_paso_id) {
            swal("Atención", "Debe seleccionar un paso siguiente para avanzar el flujo.", "warning");
            return false;
        }
        formData.append("siguiente_paso_id", selected_siguiente_paso_id);

        // Obtenemos la información del paso seleccionado para verificar si requiere selección manual
        var siguientes_pasos = $('#panel_checkbox_flujo').data('siguientes-pasos');
        var selected_paso_info = siguientes_pasos.find(paso => paso.paso_id == selected_siguiente_paso_id);

        if (selected_paso_info && selected_paso_info.requiere_seleccion_manual == 1) {
            var nuevo_asignado_id = $('#nuevo_asignado_id').val();
            if (!nuevo_asignado_id) {
                swal("Atención", "Debe seleccionar un agente para el siguiente paso.", "warning");
                return false;
            }
            // Si la selección es manual, enviamos el ID del usuario seleccionado
            formData.append("nuevo_asignado_id", nuevo_asignado_id);
        }
        // Si no es manual, no se envía 'nuevo_asignado_id' y el backend lo asignará automáticamente.
    }

    var totalFile = $('#fileElem').val().length;
    for (var i = 0; i < totalFile; i++) {
        formData.append('files[]', $('#fileElem')[0].files[i]);
    }

    $.ajax({
        url: "../../controller/ticket.php?op=insertdetalle",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            var resultado = JSON.parse(data);
            // Verificamos la nueva bandera que envía el controlador
            if (resultado.reassigned) {
                // Si se reasignó, mostramos un mensaje de éxito y redirigimos
                swal({
                    title: "¡Correcto!",
                    text: "El ticket ha sido avanzado y reasignado.",
                    type: "success",
                    timer: 1500, // La alerta se cierra sola después de 1.5 segundos
                    showConfirmButton: false
                });

                // Después de 1.6 segundos, redirigimos al listado principal
                setTimeout(function() {
                    window.location.href = "../../view/ConsultarTicket/";
                }, 1600);

            } else {
                // Si no se reasignó (solo fue un comentario), recargamos los detalles
                $('#tickd_descrip').summernote('reset');
                $('#fileElem').val('');
                $('#checkbox_avanzar_flujo').prop('checked', false); // Desmarcamos el checkbox
                $('#panel_siguiente_asignado').hide(); // Ocultamos el combo si estaba visible
                listarDetalle(tick_id);
                swal("Correcto", "Respuesta enviada correctamente", "success");
            }
        }
    })




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
    swal({
        title: "¿Estas seguro que quieres cerrar el ticket?",
        text: "Una vez cerrado no podrás volver a abrirlo",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Si, cerrar ticket!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: false
    },
        function (isConfirm) {
            if (isConfirm) {
                var tick_id = getUrlParameter('ID');
                var usu_id = $('#user_idx').val();
                updateTicket(tick_id, usu_id);

                setTimeout(function () {
                    $.post("../../controller/email.php?op=ticket_cerrado", { tick_id: tick_id }, function (resp) {
                    }).fail(function (err) {
                        console.error("Error al enviar el correo:", err.responseText);
                    });
                }, 0);

            } else {
                swal({
                    title: "Cancelado",
                    text: "El ticket sigue abierto.",
                    type: "error",
                    confirmButtonClass: "btn-danger"

                });
            }
        });

});


$(document).on("click", "#btn_aprobar_flujo", function () {
    swal({
        title: "¿Estás seguro de aprobar este ticket?",
        text: "Una vez aprobado, el ticket avanzará al siguiente paso del flujo y será reasignado automáticamente.",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        confirmButtonText: "Sí, ¡Aprobar ahora!",
        cancelButtonText: "No, cancelar",
        closeOnConfirm: false
    },
        function (isConfirm) {
            if (isConfirm) {
                var tick_id = getUrlParameter('ID');
                // Se envía la petición al controlador de tickets
                $.post("../../controller/ticket.php?op=aprobar_ticket_jefe", { tick_id: tick_id }, function (data) {
                    swal({
                        title: "¡Aprobado!",
                        text: "El ticket ha sido reasignado y el flujo continúa. Serás redirigido a la lista de tickets.",
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });

                    setTimeout(function () {
                        window.location.href = "../../view/ConsultarTicket/";
                    }, 2100);

                }).fail(function (jqXHR) {
                    // Si el backend devuelve un error, lo mostramos
                    swal("Error", "No se pudo completar la aprobación. Detalle: " + jqXHR.responseText, "error");
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

    $.post("../../controller/ticket.php?op=listardetalle", { tick_id: tick_id }, function (data) {
        $('#lbldetalle').html(data);

    });

    $.post("../../controller/ticket.php?op=mostrar", { tick_id: tick_id }, function (data) {
        data = JSON.parse(data);

        $('#lbltickestado').html(data.tick_estado);
        $('#lblprioridad').html(data.pd_nom);
        $('#lblnomusuario').html(data.usu_nom + ' ' + data.usu_ape);
        $('#lblestado_tiempo').html(data.estado_tiempo);
        $('#lblfechacrea').html(data.fech_crea);
        $('#lblticketid').html("Detalle del tikect #" + data.tick_id);
        $('#cat_id').val(data.cat_nom);
        $('#cats_id').val(data.cats_nom);
        $('#emp_id').val(data.emp_nom);
        $('#dp_id').val(data.dp_nom);
        $('#tick_titulo').val(data.tick_titulo);
        $('#tickd_descripusu').summernote('code', data.tick_descrip);

        var usu_id = $('#user_idx').val();
        if (usu_id != data.usu_asig) {
            $("#btncerrarticket").addClass('hidden');
            $("#panel_respuestas_rapidas").addClass('hidden');
        };

        $('#panel_aprobacion_jefe').hide();

        if ((data.paso_actual_id === null || data.paso_actual_id == 0) && data.tick_estado_texto === 'Abierto' && data.usu_asig == usu_id) {
            // Si todas las condiciones se cumplen, muestra el panel
            $('#panel_aprobacion_jefe').show();
            // $('#boxdetalleticket').hide(); 
        } else {
            $('#boxdetalleticket').show();
            if (data.tick_estado_texto == 'Cerrado') {
                $('#boxdetalleticket').hide();
            };
        }


    });
}

    function listarDetalle(tick_id) {

    $.post("../../controller/ticket.php?op=listardetalle", { tick_id: tick_id }, function (data) {
        $('#lbldetalle').html(data);

    });

    $.post("../../controller/ticket.php?op=mostrar", { tick_id: tick_id }, function (data) {
        data = JSON.parse(data);

        $('#lbltickestado').html(data.tick_estado);
        $('#lblprioridad').html(data.pd_nom);
        $('#lblnomusuario').html(data.usu_nom + ' ' + data.usu_ape);
        $('#lblestado_tiempo').html(data.estado_tiempo);
        $('#lblfechacrea').html(data.fech_crea);
        $('#lblticketid').html("Detalle del tikect #" + data.tick_id);
        $('#cat_id').val(data.cat_nom);
        $('#cats_id').val(data.cats_nom);
        $('#emp_id').val(data.emp_nom);
        $('#dp_id').val(data.dp_nom);
        $('#tick_titulo').val(data.tick_titulo);
        $('#tickd_descripusu').summernote('code', data.tick_descrip);

        var usu_id = $('#user_idx').val();
        if (usu_id != data.usu_asig) {
            $("#btncerrarticket").addClass('hidden');
            $("#panel_respuestas_rapidas").addClass('hidden');
        };

        $('#panel_aprobacion_jefe').hide();

        if ((data.paso_actual_id === null || data.paso_actual_id == 0) && data.tick_estado_texto === 'Abierto' && data.usu_asig == usu_id) {
            // Si todas las condiciones se cumplen, muestra el panel
            $('#panel_aprobacion_jefe').show();
            // $('#boxdetalleticket').hide(); 
        } else {
            $('#boxdetalleticket').show();
            if (data.tick_estado_texto == 'Cerrado') {
                $('#boxdetalleticket').hide();
            };
        }


    });

    $.post("../../controller/ticket.php?op=mostrar", { tick_id: tick_id }, function (data) {
        data = JSON.parse(data);
        var usu_asigx = $('#user_idx').val();

        // Lógica para construir la línea de tiempo
        if (data.timeline_steps && data.timeline_steps.length > 0) {
            $('#panel_linea_tiempo').show();
            var timeline_html = '';

            data.timeline_steps.forEach(function (paso) {
                var status_class = '';
                if (paso.estado === 'Completado') {
                    status_class = 'timeline-step-completed';
                } else if (paso.estado === 'Actual') {
                    status_class = 'timeline-step-active';
                } else { // Pendiente
                    status_class = 'timeline-step-pending';
                }

                timeline_html += '<li class="' + status_class + '">';
                timeline_html += '  <div class="step-name">' + paso.paso_nombre + '</div>';
                timeline_html += '</li>';
            });

            $('#timeline_flujo').html(timeline_html);
        } else {
            $('#panel_linea_tiempo').hide();
        }

        $('#panel_checkbox_flujo').hide(); // Ocultamos por defecto

        if (data.siguientes_pasos && data.siguientes_pasos.length > 0) {
            // Si el servidor nos dice que hay uno o más siguientes pasos, mostramos el checkbox
            $('#panel_checkbox_flujo').show();
            // Y guardamos el array completo de siguientes pasos para usarlo después
            $('#panel_checkbox_flujo').data('siguientes-pasos', data.siguientes_pasos);
        }

        if ((data.siguientes_pasos && data.siguientes_pasos.length > 0) || data.paso_actual_info == null) {
            // SI hay un siguiente paso, el flujo NO ha terminado.
            // Deshabilitamos el botón de cerrar.
            $('#btncerrarticket').prop('disabled', true);
        } else {
            // SI NO hay un siguiente paso (o el ticket no está en un flujo),
            // el botón debe estar habilitado.
            $('#btncerrarticket').prop('disabled', false);
        }
        // Ocultamos el panel por defecto en cada recarga
        $('#panel_guia_paso').hide();
    
        // Verificamos si el ticket está en un paso activo de un flujo
        if (data.paso_actual_info) {
            var pasoInfo = data.paso_actual_info;
    
            // Mostramos y llenamos el panel de guía
            $('#panel_guia_paso').show();
            $('#guia_paso_nombre').text('Paso Actual: ' + pasoInfo.paso_nombre);
            $('#guia_paso_tiempo').text(pasoInfo.paso_tiempo_habil);
    
            // Llenamos el editor Summernote con la descripción del paso
            // Si la descripción está vacía, no ponemos nada.
            if (pasoInfo.paso_descripcion) {
                
                $('#tickd_descrip').summernote('code', pasoInfo.paso_descripcion);
            }
        }
    });
}

// Función para manejar la lógica después de seleccionar un paso siguiente (ya sea automáticamente o por el usuario)
function handleSelectedNextStep(selectedPasoId) {
    // Obtenemos el array completo de siguientes pasos
    var siguientes_pasos = $('#panel_checkbox_flujo').data('siguientes-pasos');
    // Encontramos el paso seleccionado dentro del array
    var selected_paso_info = siguientes_pasos.find(paso => paso.paso_id == selectedPasoId);

    if (selected_paso_info) {
        // Almacenamos el ID del paso seleccionado en el campo oculto
        $('#selected_siguiente_paso_id').val(selectedPasoId);

        // Si el paso seleccionado requiere selección manual, mostramos el combo de agentes
        if (selected_paso_info.requiere_seleccion_manual == 1) {
            $.post("../../controller/ticket.php?op=get_usuarios_por_paso", { paso_id: selected_paso_info.paso_id }, function(data) {
                $('#nuevo_asignado_id').html(data).trigger('change');
                $('#panel_siguiente_asignado').show();
            });
        } else {
            // Si no requiere selección manual, ocultamos el combo de agentes
            $('#panel_siguiente_asignado').hide();
        }
    } else {
        console.error("Error: No se encontró información para el paso seleccionado: " + selectedPasoId);
    }
}

// --- Evento que se activa al marcar/desmarcar el checkbox de avanzar flujo ---
$(document).on('change', '#checkbox_avanzar_flujo', function() {
    console.clear(); // Limpiamos la consola para ver mejor

    // Obtenemos el array completo de siguientes pasos que guardamos en el panel
    var siguientes_pasos = $('#panel_checkbox_flujo').data('siguientes-pasos');

    if ($(this).is(':checked')) {
        if (siguientes_pasos && siguientes_pasos.length > 0) {
            if (siguientes_pasos.length > 1) {
                // Si hay múltiples pasos, mostramos el modal de selección
                var options_html = '';
                siguientes_pasos.forEach(function(paso) {
                    options_html += '<option value="' + paso.paso_id + '">' + paso.paso_nombre + '</option>';
                });
                $('#select_siguiente_paso').html(options_html);
                $('#modal_seleccionar_paso').modal('show');
            } else {
                // Si solo hay un paso, lo seleccionamos automáticamente
                handleSelectedNextStep(siguientes_pasos[0].paso_id);
            }
        } else {
            console.error("Error: No se encontraron los datos de los siguientes pasos en el panel. Revisa la función listarDetalle.");
        }
    } else {
        // Si se desmarca, siempre ocultamos el combo de agentes y limpiamos el paso seleccionado
        $('#panel_siguiente_asignado').hide();
        $('#selected_siguiente_paso_id').val('');
    }
});

// --- Evento para el botón de confirmar selección en el modal ---
$(document).on('click', '#btn_confirmar_paso_seleccionado', function() {
    var selectedPasoId = $('#select_siguiente_paso').val();
    if (selectedPasoId) {
        $('#modal_seleccionar_paso').modal('hide');
        handleSelectedNextStep(selectedPasoId);
    } else {
        swal("Atención", "Por favor, selecciona un paso.", "warning");
    }
});

init();

