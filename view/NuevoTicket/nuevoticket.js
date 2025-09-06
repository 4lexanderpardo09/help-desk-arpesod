function init() {
    $('#ticket_form').on('submit', function (e) {
        guardaryeditar(e);
    })
}

$(document).ready(function () {

    $('#tick_descrip').summernote({
        height: 200,
        lang: "es-ES",
        callbacks: {
            onImageUpload: function (image) {
                ("Image detect...");
                myimagetreat(image[0]);
            },
            onPaste: function (e) {
                ("Text detect...");
            }
        },
        buttons: {
            image: function() {
                var fileInput = document.createElement('input');
                fileInput.setAttribute('type', 'file');
                fileInput.setAttribute('accept', 'image/*');
                fileInput.addEventListener('change', function() {
                    var file = this.files[0];
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var dataURL = e.target.result;
                        $('#tick_descrip').summernote('insertImage', dataURL);
                    };
                    reader.readAsDataURL(file);
                });
                fileInput.click();
            }
        }
    });

    
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
                $('#tick_descrip').summernote("insertNode", image[0]);
            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    $.post("../../controller/prioridad.php?op=combo", function (data) {
        $('#pd_id').html('<option value="">Seleccionar</option>' + data);
    });

    $.post("../../controller/departamento.php?op=combo", function (data) {
        $('#dp_id').html('<option value="">Seleccionar</option>' + data);
    });

    $.post("../../controller/empresa.php?op=combo", function (data) {
        $('#emp_id').html('<option value="">Seleccionar</option>' + data);
    });

    categoriasAnidadas();

});

function categoriasAnidadas() {
    var user_cargo_id = 0;
    // Inicializamos los combos con Select2
    $('#dp_id, #emp_id, #cat_id, #cats_id, #usu_asig, #pd_id').select2();

    // Guardamos el cargo del usuario en una variable global al cargar la página
    user_cargo_id = $('#user_cargo_id').val();

    // 1. Cargar los combos iniciales que no dependen de otros
    $.post("../../controller/departamento.php?op=combo", function (data) {
        $('#dp_id').html('<option value="">Seleccione Departamento</option>' + data);
    });

    $.post("../../controller/empresa.php?op=combo", function (data) {
        $('#emp_id').html('<option value="">Seleccione Empresa</option>' + data);
    });

    // --- EVENTOS "CHANGE" INDEPENDIENTES ---

    // 2. Evento para Departamento y Empresa
    $('#dp_id, #emp_id').on('change', function () {
        var dp_id = $('#dp_id').val();
        var emp_id = $('#emp_id').val();

        // Limpiamos los combos hijos
        $('#cat_id').html('<option value="">Seleccione</option>');
        $('#cats_id').html('<option value="">Seleccione</option>');
        $('#tick_descrip').summernote('code', '');
        $('#panel_asignacion_manual').hide();

        if (dp_id && emp_id) {
            // Si ambos tienen valor, cargamos las Categorías
            $.post("../../controller/categoria.php?op=combo", { dp_id: dp_id, emp_id: emp_id }, function (data) {
                $('#cat_id').html('<option value="">Seleccione Categoría</option>' + data);
            });
        }
    });

    // 3. Evento para Categoría
    $('#cat_id').on('change', function () {
        var cat_id = $(this).val();

        // Limpiamos los combos hijos
        $('#cats_id').html('<option value="">Seleccione</option>');
        $('#tick_descrip').summernote('code', '');
        $('#panel_asignacion_manual').hide();

        if (cat_id) {
            // Si se selecciona una categoría, cargamos las Subcategorías permitidas para el cargo del usuario
            $.post("../../controller/subcategoria.php?op=combo_filtrado", { cat_id: cat_id, creador_car_id: user_cargo_id }, function (data) {
                $('#cats_id').html('<option value="">Seleccione Subcategoría</option>' + data);
            });
        }
    });

    // 4. Evento para Subcategoría
    $('#cats_id').on('change', function () {
        var cats_id = $(this).val();

        // Limpiamos los campos dependientes
        $('#tick_descrip').summernote('code', '');
        $('#panel_asignacion_manual').hide();
        $('#usu_asig').html('');

        if (cats_id) {
            // a. Llenar la descripción por defecto
            $.post("../../controller/subcategoria.php?op=mostrar", { cats_id: cats_id }, function (data) {
                data = JSON.parse(data);
                
                if (data.subcategoria && data.subcategoria.cats_descrip) {
                    $('#tick_descrip').summernote('code', data.subcategoria.cats_descrip);
                    $('#pd_id').val(data.subcategoria.pd_id).trigger('change');
                }
            });

            // b. Verificar si se necesita asignación manual
            $.post("../../controller/ticket.php?op=verificar_inicio_flujo", { cats_id: cats_id }, function (data) {
                if (data.requiere_seleccion) {
                    var options = '<option value="">Seleccione un agente...</option>';
                    data.usuarios.forEach(function (user) {
                        options += `<option value="${user.usu_id}">${user.usu_nom} ${user.usu_ape} (${user.reg_nom})</option>`;
                    });
                    $('#usu_asig').html(options);
                    $('#panel_asignacion_manual').show();
                }
            });
        }
    });
}


function guardaryeditar(e) {
    e.preventDefault();
    var formData = new FormData($('#ticket_form')[0])

    if ($('#tick_titulo').val().trim() == '') {
        swal("Atención", "Debe ingresar un título", "warning");
        return false;

    } else if ($('#dp_id').val() == null || $('#dp_id').val() == '') {
        swal("Atención", "Debe seleccionar un departamento", "warning");
        return false;
    } else if ($('#emp_id').val() == null || $('#emp_id').val() == '') {
        swal("Atención", "Debe seleccionar una empresa", "warning");
        return false;

    } else if ($('#cat_id').val() == null || $('#cat_id').val() == '') {
        swal("Atención", "Debe seleccionar una categoría", "warning");
        return false;

    } else if ($('#cats_id').val() == null || $('#cats_id').val() == '') {
        swal("Atención", "Debe seleccionar una subcategoría", "warning");
        return false;
    } else if ($('#panel_asignacion_manual').is(':visible') && ($('#usu_asig').val() == null || $('#usu_asig').val() == '')) {
        swal("Atención", "Esta subcategoría requiere que seleccione un agente para la asignación.", "warning");
        return false;

    } else if ($('#pd_id').val() == null || $('#pd_id').val() == '') {
        swal("Atención", "Debe seleccionar una prioridad", "warning");
        return false;

    } else if ($('#tick_descrip').summernote('isEmpty')) {
        swal("Atención", "Debe ingresar una descripción", "warning");
        return false;
    }

    var totalFile = $('#fileElem').val().length;

    for (var i = 0; i < totalFile; i++) {
        formData.append('files[]', $('#fileElem')[0].files[i]);
    }

    $.ajax({
        url: "../../controller/ticket.php?op=insert",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            // Intentamos asegurarnos de que tenemos un objeto JS
            var resp = data;
            try {
                if (typeof data === 'string' || data instanceof String) {
                    resp = JSON.parse(data);
                }
            } catch (err) {
                console.error("Respuesta no JSON:", data, err);
                swal("Error", "Respuesta inválida del servidor.", "error");
                return;
            }

            // Caso nuevo: backend devuelve { success: false, errors: [...] }
            if (resp && resp.hasOwnProperty('success') && resp.success === false) {
                var mensajes = resp.errors && resp.errors.length ? resp.errors : ["No se pudo crear el ticket."];
                // Unimos con saltos de línea para mostrar en swal
                swal("No se creó el ticket", mensajes.join("\n"), "error");
                return;
            }

            // Caso nuevo: success true con tick_id explícito
            var tickId = null;
            if (resp && resp.hasOwnProperty('tick_id')) {
                tickId = resp.tick_id;
            }
            // Caso refactor/antiguo: resp.ticket es array o resp es array
            if (!tickId && resp && resp.ticket && Array.isArray(resp.ticket) && resp.ticket.length > 0) {
                tickId = resp.ticket[0].tick_id || resp.ticket[0].tick_id;
            }
            if (!tickId && Array.isArray(resp) && resp.length > 0 && resp[0].tick_id) {
                tickId = resp[0].tick_id;
            }

            if (!tickId) {
                // si no obtenemos tick_id, intentar leer datos.ticket o asumir éxito parcial
                console.warn("No se encontró tick_id en la respuesta:", resp);
            }

            // Si todo quedó bien: enviar correos (si tenemos tickId)
            if (tickId) {
                $.post("../../controller/email.php?op=ticket_abierto", { tick_id: tickId });
                $.post("../../controller/email.php?op=ticket_asignado", { tick_id: tickId });
            }

            // Reset del formulario
            $('#cat_id').val('');
            $('#tick_titulo').val('');
            $('#fileElem').val('');
            $('#dp_id').val('');
            $('#cats_id').val('');
            $('#pd_id').val('');
            $('#tick_descrip').summernote('reset');
            $('#usu_asig').val('');
            $('#error_proceso').prop('checked', false);
            $("#error_procesodiv").addClass('hidden');

            swal("Correcto", "Registrado correctamente", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("Error AJAX:", textStatus, errorThrown, jqXHR.responseText);
            var msg = "Ocurrió un error al comunicarse con el servidor.";
            // si el backend devolvió JSON con detalles en responseText, intentar parsearlo
            try {
                var parsed = JSON.parse(jqXHR.responseText);
                if (parsed && parsed.errors && parsed.errors.length) {
                    msg = parsed.errors.join("\n");
                } else if (parsed && parsed.message) {
                    msg = parsed.message;
                }
            } catch (e) {
                // no es JSON: usar texto plano
                if (jqXHR.responseText) msg = jqXHR.responseText;
            }
            swal("Error", msg, "error");
        }
    });
}



init();