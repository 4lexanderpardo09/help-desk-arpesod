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

    $.post("../../controller/categoria.php?op=combo", function (data) {
        $('#cat_id').html('<option value="">Seleccionar</option>' + data);
    });

    $.post("../../controller/prioridad.php?op=combo", function (data) {
        $('#pd_id').html('<option value="">Seleccionar</option>' + data);
    });

    $.post("../../controller/departamento.php?op=combo", function (data) {
        $('#dp_id').html('<option value="">Seleccionar</option>' + data);
    });
    
    $('#usu_asig').html('<option value="">Seleccionar</option>');

    $("#dp_id").change(function(){
        dp_id = $(this).val();
        
        $.post("../../controller/usuario.php?op=usuariosxdepartamento",{dp_id:dp_id}, function(data) {
            $("#usu_asig").html(data);
        });  

        $.post("../../controller/categoria.php?op=combo", {dp_id:dp_id}, function (data) {
            $('#cat_id').html('<option value="">Seleccionar</option>' + data);
        });
    });


    $('#cats_id').html('<option value="">Seleccionar</option>');

    $("#cat_id").change(function(){
        cat_id = $(this).val();

        $.post("../../controller/subcategoria.php?op=combo",{cat_id:cat_id}, function (data) {
            $('#cats_id').html(data);
        });
    })

});

function guardaryeditar(e) {
    e.preventDefault();
    var formData = new FormData($('#ticket_form')[0])

    if ($('#cat_id').val() == '') {
        swal("Atención", "Debe seleccionar una categoría", "warning");
        return false;
    } if ($('#tick_titulo').val() == '') {
        swal("Atención", "Debe ingresar un título", "warning");
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
            $('#cats_id').val('');
            $('#pd_id').val('');
            $('#tick_descrip').summernote('reset');
            $('#usu_asig').val('');
            swal("Correcto", "Registrado correctamente ", "success");
        }
    })
}



init();