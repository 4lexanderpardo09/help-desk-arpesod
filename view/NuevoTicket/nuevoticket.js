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
                console.log("Image detect...");
                myimagetreat(image[0]);
            },
            onPaste: function (e) {
                console.log("Text detect...");
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
    $('#emp_id').html('<option value="">Seleccionar</option>');
    $('#cat_id').html('<option value="">Seleccionar</option>');
    $('#cats_id').html('<option value="">Seleccionar</option>');
    $('#tick_descrip').summernote('code', '');

    // Cuando cambia el departamento
    $("#dp_id").off('change').on('change', function () {
        let dp_id = $(this).val();

        if (dp_id == 0) {
            $('#emp_id').html('<option value="">Seleccionar</option>');
            $('#cat_id').html('<option value="">Seleccionar</option>');
            $('#cats_id').html('<option value="">Seleccionar</option>');
            $('#tick_descrip').summernote('code', '');
        } else {
            // Cuando cambia la empresa
            $("#emp_id").off('change').on('change', function () {
                let emp_id = $(this).val();

                if (emp_id == 0) {
                    $('#cat_id').html('<option value="">Seleccionar</option>');
                    $('#cats_id').html('<option value="">Seleccionar</option>');
                    $('#tick_descrip').summernote('code', '');
                } else {
                    // Filtrar categorías por departamento y empresa
                    $.post("../../controller/categoria.php?op=combo", { dp_id: dp_id, emp_id: emp_id }, function (data) {
                        $('#cat_id').html('<option value="">Seleccionar</option>' + data);
                    });

                    // Cuando cambia la categoría
                    $("#cat_id").off('change').on('change', function () {
                        let cat_id = $(this).val();

                        if (cat_id == 0) {
                            $('#cats_id').html('<option value="">Seleccionar</option>');
                            $('#tick_descrip').summernote('code', '');
                        } else {
                            $.post("../../controller/subcategoria.php?op=combo", { cat_id: cat_id }, function (data) {
                                $('#cats_id').html('<option value="">Seleccionar</option>' + data);
                            });

                            // Cuando cambia la subcategoría
                            $("#cats_id").off('change').on('change', function () {
                                let cats_id = $(this).val();

                                if (cats_id == 0) {
                                    $('#tick_descrip').summernote('code', '');
                                    $("#error_procesodiv").addClass('hidden');
                                } else {
                                    $.post("../../controller/subcategoria.php?op=mostrar", { cats_id: cats_id }, function (data) {
                                        data = JSON.parse(data);
                                        $('#tick_descrip').summernote('code', data.subcategoria.cats_descrip);                               
                                        $('#pd_id').val(data.subcategoria.pd_id);
                                    });
                                }
                            });
                        }
                    });
                }
            });
        }
    });
}


function guardaryeditar(e) {
    e.preventDefault();
    var formData = new FormData($('#ticket_form')[0])

    if ($('#tick_titulo').val() == '') {
        swal("Atención", "Debe ingresar un título", "warning");
        return false;
    } if ($('#dp_id').val('') == '') {
        swal("Atención", "Debe seleccionar un departamento", "warning");
        return false;
    } if ($('#usu_asig').val('') == '') {
        swal("Atención", "Debe seleccionar un agente", "warning");
        return false;
    } if ($('#emp_id').val('') == '') {
        swal("Atención", "Debe seleccionar una empresa", "warning");
        return false;
    } if ($('#cat_id').val() == '') {
        swal("Atención", "Debe seleccionar una categoría", "warning");
        return false;
    } if ($('#cats_id').val() == '') {
        swal("Atención", "Debe seleccionar una subcategoria", "warning");
        return false;
    } if ($('pd_id').val() == '') {
        swal("Atención", "Debe seleccionar una prioridad", "warning");
        return false;
    } if ($('#tick_descrip').summernote('isEmpty')) {
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
            data = JSON.parse(data);
            $.post("../../controller/email.php?op=ticket_abierto", { tick_id: data[0].tick_id })
            $.post("../../controller/email.php?op=ticket_asignado", { tick_id: data[0].tick_id })
            $('#cat_id').val('');
            $('#tick_titulo').val('');
            $('#fileElem').val('');
            $('#dp_id').val('');
            $('#cats_id').val('');
            $('#pd_id').val('');
            $('#tick_descrip').summernote('reset');
            $('#usu_asig').val('');
            $('#error_proceso').prop('checked', false);
            $("#error_procesodiv").addClass('hidden')
            swal("Correcto", "Registrado correctamente ", "success");
        }
    })
}



init();