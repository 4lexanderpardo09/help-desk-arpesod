 function init(){
    $('#ticket_form').on('submit', function(e){
        guardaryeditar(e);
    })
 }

$(document).ready(function() {
    $('#tick_descrip').summernote({
        height: 200,
        lang: "es-ES",
        callbacks: {
            onImageUpload: function(image) {
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

    $.post("../../controller/categoria.php?op=combo",function(data, status) {
        $('#cat_id').html(data);
    })

});

function guardaryeditar(e){
    e.preventDefault();

    if($('#cat_id').val() == '') {
        swal("Atención", "Debe seleccionar una categoría", "warning");
        return false;
    }if($('#tick_titulo').val() == '') {
        swal("Atención", "Debe ingresar un título", "warning");
        return false;
    }if($('#tick_descrip').summernote('isEmpty')) {
        swal("Atención", "Debe ingresar una descripción", "warning");
        return false;
    }

    var formData = new FormData($('#ticket_form')[0])
    $.ajax({
        url: "../../controller/ticket.php?op=insert", 
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos){
            $('#cat_id').val('');
            $('#tick_titulo').val('');
            $('#tick_descrip').summernote('reset');
            swal("Correcto", "Registrado correctamente ", "success");
        }
    })
}

init();