var tabla;

function init() {
    $("#flujo_form").on("submit", function(e){
        guardaryeditar(e);
    })
}

function guardaryeditar(e){
    e.preventDefault();
    var formData = new FormData($("#flujo_form")[0])
    $.ajax({
        url: "../../controller/flujo.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos){
            $("#flujo_form")[0].reset();
            $("#flujo_nom").html('');
            $("#flujo_id").val('');
            $("#cats_id").val('');
            $('#cat_id').val('');
            $('#emp_id').val('');
            $('#dp_id').val('');
            $("#modalnuevoflujo").modal('hide');
            $("#flujo_data").DataTable().ajax.reload();
            swal({
                title: "Guardado!",
                text: "Se ha guardado correctamente el nuevo registro.",
                type: "success",
                confirmButtonClass: "btn-success"
            });          
        }
    })
}

function ver(flujo_id) {
    window.location.href = '/view/PasoFlujo/?ID='+ flujo_id
}


$(document).ready(function () {

    configurarCombos();

    tabla = $('#flujo_data').dataTable({
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
            url: '../../controller/flujo.php?op=listar',
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

function editar(flujo_id) {
    $("#mdltitulo").html('Editar registro');

    $.post("../../controller/flujo.php?op=mostrar", {flujo_id:flujo_id}, function(data) {
        data = JSON.parse(data);
        $('#flujo_id').val(data.flujo_id);
        $('#flujo_nom').val(data.flujo_nom);
        $('#emp_id').val(data.emp_id);
        $('#dp_id').val(data.dp_id);
         // Marcar o desmarcar el checkbox según el valor de la BD
        if (data.requiere_aprobacion_jefe == 1) {
            $('#requiere_aprobacion_jefe').prop('checked', true);
        } else {
            $('#requiere_aprobacion_jefe').prop('checked', false);
        }

        $.post("../../controller/categoria.php?op=combo", { dp_id: data.dp_id, emp_id: data.emp_id }, function(categooriadata) {
            $('#cat_id').html('<option value="">Seleccionar Categoría</option>' + categooriadata);
            $("#cat_id").val(data.cat_id);
            $.post("../../controller/subcategoria.php?op=combo", { cat_id: data.cat_id }, function (subcategoriadata) {
                $('#cats_id').html('<option value="">Seleccionar</option>' + subcategoriadata);
                $("#cats_id").val(data.cats_id);
            });
        });

    });    

    $("#modalnuevoflujo").modal("show");
}
function eliminar(flujo_id) {
    swal({
        title: "¿Estas que quieres eliminar esta flujo?",
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
            $.post("../../controller/flujo.php?op=eliminar", {flujo_id:flujo_id}, function(data) {
                $('#flujo_data').DataTable().ajax.reload(); 
                swal({
                    title: "Eliminado!",
                    text: "flujo eliminada correctamente",
                    type: "success",
                    confirmButtonClass: "btn-success"
                }); 
            });
        } else {
            swal({
                title: "Cancelado",
                text: "La flujo no fue eliminada",
                type: "error",
                confirmButtonClass: "btn-danger"
                
            });
        }
    });
}

$(document).on("click", "#btnnuevoflujo", function(){
    $("#mdltitulo").html('Nuevo registro');
    $("#flujo_form")[0].reset();
    $("#modalnuevoflujo").modal("show");
});

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

            $("#cat_id").off('change').on('change', function () {
                cat_id = $(this).val();

                $.post("../../controller/subcategoria.php?op=combo", { cat_id: cat_id }, function (data) {
                    $('#cats_id').html('<option value="">Seleccionar</option>' + data);
                });

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

$('#modalnuevoflujo').on('hidden.bs.modal', function () {
    $("#flujo_form")[0].reset();
    $("#flujo_nom").html('');
    $("#flujo_id").val('');
    $("#cats_id").val('');
    $('#cat_id').val('');
    $('#emp_id').val('');
    $('#dp_id').val('');

});

init();