var tabla;

function init() {
    $("#cats_form").on("submit", function(e){
        guardaryeditar(e);
    })
}

function guardaryeditar(e){
    e.preventDefault();
    var formData = new FormData($("#cats_form")[0])
    $.ajax({
        url: "../../controller/subcategoria.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos){
            $("#cats_form")[0].reset();
            $('#cats_descrip').summernote('code', '');
            $('#emp_id').val('');
            $('#dp_id').val('');
            $('#cat_id').html('<option value="">Seleccione una Empresa y Departamento</option>');
            $("#cats_id").val('');
            $("#modalnuevasubcategoria").modal('hide');
            $("#cats_data").DataTable().ajax.reload();
            swal({
                title: "Guardado!",
                text: "Se ha guardado correctamente el nuevo registro.",
                type: "success",
                confirmButtonClass: "btn-success"
            });          
        }
    })
}


$(document).ready(function () {

    configurarCombos();
    mostrarprioridad();

    descripcionSubcategoria();

    tabla = $('#cats_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": true,
        lengthChange: false,
        colReorder: true,
        "buttons": [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5',
        ],
        "ajax": {
            url: '../../controller/subcategoria.php?op=listar',
            type: 'post',
            dataType: 'json',
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,
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


})

function editar(cats_id) {
    $("#mdltitulo").html('Editar registro');

    $.post("../../controller/subcategoria.php?op=mostrar", {cats_id:cats_id}, function(data) {
        data = JSON.parse(data);
        
        $("#emp_id").val(data.emp_id);
        $('#dp_id').val(data.dp_id);
        $("#cat_id").val(data.cat_id);  
        $('#cats_id').val(data.cats_id);
        $('#cats_nom').val(data.cats_nom);
        $('#pd_id').val(data.pd_id);
        $('#cats_descrip').summernote('code',data.cats_descrip);

        $.post("../../controller/categoria.php?op=combo", { dp_id: data.dp_id, emp_id: data.emp_id }, function(categoriasData) {
            $('#cat_id').html('<option value="">Seleccionar Categoría</option>' + categoriasData);
            $("#cat_id").val(data.cat_id);
        });

    });    

    $("#modalnuevasubcategoria").modal("show");
}
function eliminar(cats_id) {
    swal({
        title: "¿Estas que quieres eliminar esta subcategoria?",
        text: "Una vez eliminado no podrás volver a recuperarlo",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: false
    },
    function(isConfirm) {
        if (isConfirm) {
            $.post("../../controller/subcategoria.php?op=eliminar", {cats_id:cats_id}, function(data) {
                $('#cats_data').DataTable().ajax.reload(); 
                swal({
                    title: "Eliminado!",
                    text: "subcategoria eliminada correctamente",
                    type: "success",
                    confirmButtonClass: "btn-success"
                }); 
            });
        } else {
            swal({
                title: "Cancelado",
                text: "La subcategoria no fue eliminada",
                type: "error",
                confirmButtonClass: "btn-danger"
                
            });
        }
    });
}

function descripcionSubcategoria(){
    $('#cats_descrip').summernote({
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
}

function configurarCombos() {

    // 1. Cargar el combo de Empresas
    $.post("../../controller/empresa.php?op=combo", function(data) {
        $('#emp_id').html('<option value="">Seleccionar Empresa</option>' + data);
    });

    // 2. Cargar el combo de Departamentos
    $.post("../../controller/departamento.php?op=combo", function(data) {
        $('#dp_id').html('<option value="">Seleccionar Departamento</option>' + data);
    });

    // 3. Función que se encargará de cargar las categorías
    function cargarCategorias() {
        var emp_id = $("#emp_id").val();
        var dp_id = $("#dp_id").val();

        if (emp_id && dp_id) {
            $.post("../../controller/categoria.php?op=combo", { dp_id: dp_id, emp_id: emp_id }, function(data) {
                $('#cat_id').html('<option value="">Seleccionar Categoría</option>' + data);
            });
        } else {
            $('#cat_id').html('<option value="">Seleccione una Empresa y Departamento</option>');
        }
    }

    // 4. Asignamos el evento 'change' a los combos de Empresa y Departamento.
    // Cada vez que uno de ellos cambie, se ejecutará la función cargarCategorias.
    $("#emp_id").on('change', cargarCategorias);
    $("#dp_id").on('change', cargarCategorias);
}

function mostrarprioridad(){
    $.post("../../controller/prioridad.php?op=combo", function (data) {
        $('#pd_id').html(data);
    });
}

$(document).on("click", "#btnnuevasubcategoria", function() {
    $("#cats_form")[0].reset();
    
    $('#cats_descrip').summernote('code', '');

    $('#emp_id').val('');
    $('#dp_id').val('');
    $('#cat_id').html('<option value="">Seleccione una Empresa y Departamento</option>');
    
    $("#cats_id").val('');

    $("#mdltitulo").html('Nuevo registro');
    $("#modalnuevasubcategoria").modal("show");
});


$('#modalnuevasubcategoria').on('hidden.bs.modal', function() {
    // Esta función ahora sirve como un seguro de limpieza
    $("#cats_form")[0].reset();
    $('#cats_descrip').summernote('code', '');
    $('#emp_id').val('');
    $('#dp_id').val('');
    $('#cat_id').html('<option value="">Seleccione una Empresa y Departamento</option>');
    $("#cats_id").val('');
});

init();