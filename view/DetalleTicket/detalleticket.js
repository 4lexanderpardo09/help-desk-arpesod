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

$(document).on('click', '#btnenviar', function () {
    // Validar que haya contenido en el summernote
    if ($('#tickd_descrip').summernote('isEmpty')) {
        swal("Atención", "Debe ingresar una respuesta", "warning");
        return false;
    }

    // Validar contra plantilla (si existe)
    var templateHtml = $('#tickd_descrip').data('template') || '';
    var currentHtml = $('#tickd_descrip').summernote('code') || '';
    var cleanTemplate = stripHtml(templateHtml);
    var cleanContent = stripHtml(currentHtml);

    // Si la plantilla no es vacía y el contenido limpio es exactamente igual -> bloquear
    if (cleanTemplate !== '' && cleanContent === cleanTemplate) {
        swal("Atención", "Debe agregar información adicional a la plantilla de descripción.", "warning");
        return false;
    }

    var tick_id = getUrlParameter('ID');
    var usu_id = $('#user_idx').val();
    var tickd_descrip = currentHtml;

    var formData = new FormData($('#detalle_form')[0]);

    formData.append("tick_id", tick_id);
    formData.append("usu_id", usu_id);
    formData.append("tickd_descrip", tickd_descrip);

    // --- Lógica del flujo: si va a avanzar, validar paso y posible asignado manual ---
    if ($('#checkbox_avanzar_flujo').is(':checked')) {
        var selected_siguiente_paso_id = $('#selected_siguiente_paso_id').val();
        if (!selected_siguiente_paso_id) {
            swal("Atención", "Debe seleccionar un paso siguiente para avanzar el flujo.", "warning");
            return false;
        }
        formData.append("siguiente_paso_id", selected_siguiente_paso_id);

        // Obtenemos la información del paso seleccionado para verificar si requiere selección manual
        var siguientes_pasos = $('#panel_checkbox_flujo').data('siguientes-pasos') || [];
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

    // Archivos: usar files API (más fiable que val().length)
    var fileInput = $('#fileElem')[0];
    var totalFile = fileInput && fileInput.files ? fileInput.files.length : 0;
    for (var i = 0; i < totalFile; i++) {
        formData.append('files[]', fileInput.files[i]);
    }

    $.ajax({
        url: "../../controller/ticket.php?op=insertdetalle",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            var resultado;
            try {
                resultado = typeof data === 'string' ? JSON.parse(data) : data;
            } catch (e) {
                console.error("Respuesta inválida insertdetalle:", data);
                swal("Error", "Respuesta inválida del servidor.", "error");
                return;
            }

            // Si el backend devuelve errores (forma similar a insert ticket)
            if (resultado && resultado.success === false) {
                var mensajes = resultado.errors && resultado.errors.length ? resultado.errors : ["No se pudo procesar la petición."];
                swal("Error", mensajes.join("\n"), "error");
                return;
            }

            // Verificamos la nueva bandera que envía el controlador
            if (resultado.reassigned) {
                // Si se reasignó, mostramos un mensaje de éxito y redirigimos
                swal({
                    title: "¡Correcto!",
                    text: "El ticket ha sido avanzado y reasignado.",
                    type: "success",
                    timer: 1500,
                    showConfirmButton: false
                });

                setTimeout(function() {
                    window.location.href = "../../view/ConsultarTicket/";
                }, 1600);

            } else {
                // Si no se reasignó (solo fue un comentario), recargamos los detalles
                $('#tickd_descrip').summernote('reset');
                $('#tickd_descrip').data('template', ''); // limpiar plantilla guardada si aplica
                $('#fileElem').val('');
                $('#checkbox_avanzar_flujo').prop('checked', false);
                $('#panel_siguiente_asignado').hide();
                listarDetalle(tick_id);
                swal("Correcto", "Respuesta enviada correctamente", "success");
            }
        },
        error: function (jqXHR) {
            console.error("Error AJAX insertdetalle:", jqXHR.responseText);
            var msg = "Ocurrió un error al comunicarse con el servidor.";
            try {
                var parsed = JSON.parse(jqXHR.responseText);
                if (parsed && parsed.errors) msg = parsed.errors.join("\n");
            } catch (e) {
                if (jqXHR.responseText) msg = jqXHR.responseText;
            }
            swal("Error", msg, "error");
        }
    });

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

        if (data.tick_estado_texto == 'Cerrado') {
            $('#boxdetalleticket').hide();
        };



    });

    $.post("../../controller/ticket.php?op=mostrar", { tick_id: tick_id }, function (data) {
        data = JSON.parse(data);
        var usu_asigx = $('#user_idx').val();
        // === Manejo seguro y robusto de mermaid ===
        if (data.timeline_graph && data.timeline_graph.length > 0) {
            $('#panel_linea_tiempo').show();

            const mermaidContainer = document.querySelector("#panel_linea_tiempo .mermaid");
            // Limpia el contenedor antes de dibujar
            mermaidContainer.innerHTML = '';

            // Normalizamos el texto: quitar espacios excesivos, convertir ; a saltos si quieres
            let graph = data.timeline_graph.trim();
            // opcional: si ves que el ; a final de línea causa problemas, reemplaza:
            graph = graph.replace(/graph\s+(TD|TB)/i, 'graph LR');

            // Inyectar como textContent para evitar que el navegador parsee HTML
            mermaidContainer.textContent = graph;

            // Asegurarnos que el panel está visible antes de renderizar
            // (en algunas versiones mermaid necesita layout en elementos visibles)
            setTimeout(function () {
                if (window.mermaid) {
                    try {
                        // Inicializamos sin startOnLoad (no queremos que busque automáticamente)
                        mermaid.initialize({ startOnLoad: false });
                    } catch (e) {
                        // algunas versiones ignoran initialize, no pasa nada
                    }

                    // Intentamos mermaid.render (maneja versiones modernas y devuelve SVG)
                    try {
                        const renderResult = mermaid.render
                            ? mermaid.render('mermaid_graph_' + Date.now(), graph)
                            : null;

                        // renderResult puede ser promesa o resultado inmediato
                        if (renderResult && typeof renderResult.then === 'function') {
                            renderResult.then(res => {
                                mermaidContainer.innerHTML = (res.svg || res);
                            }).catch(err => {
                                console.error("mermaid.render (promise) falló:", err);
                                // fallback a init
                                try { mermaid.init(undefined, mermaidContainer); } catch(e){ console.error(e); }
                            });
                        } else if (renderResult) {
                            // resultado inmediato
                            mermaidContainer.innerHTML = (renderResult.svg || renderResult);
                        } else {
                            // fallback para versiones que usan init
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
            }, 50); // un pequeño delay ayuda si el contenedor estaba oculto
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

        // Determinar si es el último paso
        var isLastStep = (!data.siguientes_pasos || data.siguientes_pasos.length === 0) && data.paso_actual_info;

        if (isLastStep && data.tick_estado_texto !== 'Cerrado') {
            // Último paso: Ocultar editor y mostrar solo botón de cerrar
            $('#tickd_descrip').closest('.form-group').hide();
            $('#detalle_form').hide();
            $('#btnenviar').hide();
            $('#btncerrarticket').show().prop('disabled', false);
        } else if (data.tick_estado_texto !== 'Cerrado') {
            // No es el último paso o no está en un flujo: Mostrar editor y deshabilitar cerrar
            $('#tickd_descrip').closest('.form-group').show();
            $('#detalle_form').show();
            $('#btnenviar').show();
            $('#btncerrarticket').show().prop('disabled', true);
        } else {
            // Ticket cerrado: Ocultar todo
            $('#boxdetalleticket').hide();
        }

        // Ocultamos el panel por defecto en cada recarga
        $('#panel_guia_paso').hide();
        $('#panel_aprobacion').hide();
    
        // Verificamos si el ticket está en un paso activo de un flujo
        if (data.paso_actual_info) {
            var pasoInfo = data.paso_actual_info;

            if (pasoInfo.es_aprobacion == 1) {
                $('#panel_aprobacion').show();
                $('#boxdetalleticket').hide();
            } else {
                // Mostramos y llenamos el panel de guía
                $('#panel_guia_paso').show();
                $('#guia_paso_nombre').text('Paso Actual: ' + pasoInfo.paso_nombre);
                $('#guia_paso_tiempo').text(pasoInfo.paso_tiempo_habil);
        
                // Llenamos el editor Summernote con la descripción del paso
                // Si la descripción está vacía, no ponemos nada.
                if (pasoInfo.paso_descripcion) {
                    var pasoTemplate = pasoInfo.paso_descripcion;
                    $('#tickd_descrip').summernote('code', pasoTemplate);
                    // Guardamos la plantilla para compararla antes de enviar la respuesta
                    $('#tickd_descrip').data('template', pasoTemplate);
                } else {
                    $('#tickd_descrip').data('template', ''); // limpiar si no hay

                }
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
            $.post("../../controller/flujopaso.php?op=get_usuarios_por_paso", { paso_id: selected_paso_info.paso_id }, function(data) {
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

