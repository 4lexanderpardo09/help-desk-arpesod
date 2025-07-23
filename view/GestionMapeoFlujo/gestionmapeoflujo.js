var tabla;

function init() {
    $("#flujomapeo_form").on("submit", function(e) {
        guardaryeditar(e);
    });
}

function guardaryeditar(e) {
    e.preventDefault();
    var formData = new FormData($("#flujomapeo_form")[0]);
    $.ajax({
        url: "../../controller/flujomapeo.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function() {
            $('#flujomapeo_form')[0].reset();
            $("#modalnuevoflujomapeo").modal('hide');
            $('#flujomapeo_data').DataTable().ajax.reload();
            swal("¡Correcto!", "Registro guardado exitosamente.", "success");
        }
    });
}

function configurarCombos() {
    // 1. Cargar combos iniciales que no dependen de otros
    $.post("../../controller/empresa.php?op=combo", function(data) {
        $('#emp_id').html('<option value="">Seleccionar Empresa</option>' + data);
    });

    $.post("../../controller/departamento.php?op=combo", function(data) {
        $('#dp_id').html('<option value="">Seleccionar Departamento</option>' + data);
    });

    $.post("../../controller/cargo.php?op=combo", function(data) {
        $('#creador_car_id').html('<option value="">Seleccionar Cargo</option>' + data);
        $('#asignado_car_id').html('<option value="">Seleccionar Cargo</option>' + data);
    });

    // 2. Definir los eventos CHANGE para los combos dependientes
    
    // Cuando cambie Empresa O Departamento -> Cargar Categorías
    $("#emp_id, #dp_id").on('change', function() {
        var emp_id = $("#emp_id").val();
        var dp_id = $("#dp_id").val();

        // Limpiamos los combos hijos
        $('#cat_id').html('<option value="">Seleccione Empresa y Depto.</option>').trigger('change');
        $('#cats_id').html('<option value="">Seleccione una Categoría</option>').trigger('change');

        if (emp_id && dp_id) {
            $.post("../../controller/categoria.php?op=combo", { dp_id: dp_id, emp_id: emp_id }, function(data) {
                $('#cat_id').html('<option value="">Seleccionar Categoría</option>' + data);
            });
        }
    });

    // Cuando cambie Categoría -> Cargar Subcategorías
    $("#cat_id").on('change', function() {
        var cat_id = $(this).val();
        if (cat_id) {
            $.post("../../controller/subcategoria.php?op=combo", { cat_id: cat_id }, function(data) {
                $('#cats_id').html('<option value="">Seleccionar Subcategoría</option>' + data);
            });
        } else {
            $('#cats_id').html('<option value="">Seleccione una Categoría</option>');
        }
    });
}

$(document).ready(function() {

    // Inicializar Select2 en los combos del modal
    $('#cat_id,#dp_id,#emp_id,#cats_id, #creador_car_id, #asignado_car_id').select2({
        dropdownParent: $('#modalnuevoflujomapeo')
    });
    configurarCombos();
    
    // Cargar combo de subcategorías
    $.post("../../controller/subcategoria.php?op=combo", function(data) {
        $('#cats_id').html(data);
    });

    // Cargar combos de cargos
    $.post("../../controller/cargo.php?op=combo", function(data) {
        $('#creador_car_id').html(data);
        $('#asignado_car_id').html(data);
    });

    tabla = $('#flujomapeo_data').dataTable({
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
            url: '../../controller/flujomapeo.php?op=listar',
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
});

function editar(map_id) {
    $('#mdltitulo').html('Editar Regla');
    $.post("../../controller/flujomapeo.php?op=mostrar", { map_id: map_id }, function(data) {
        data = JSON.parse(data);
        $('#map_id').val(data.map_id);
        $('#cats_id').val(data.cats_id).trigger('change');
        $('#emp_id').val(data.emp_id).trigger('change');
        $('#dp_id').val(data.dp_id).trigger('change');
        $('#creador_car_id').val(data.creador_car_id).trigger('change');
        $('#asignado_car_id').val(data.asignado_car_id).trigger('change');
        $('#modalnuevoflujomapeo').modal('show');

        $.post("../../controller/categoria.php?op=combo", { dp_id: data.dp_id, emp_id: data.emp_id }, function(categooriadata) {
            $('#cat_id').html(categooriadata);
            $("#cat_id").val(data.cat_id);
            $.post("../../controller/subcategoria.php?op=combo", { cat_id: data.cat_id }, function (subcategoriadata) {
                $('#cats_id').html(subcategoriadata);
                $("#cats_id").val(data.cats_id);
            });
        });
    });
}

function eliminar(map_id) {
    swal({
        title: "Advertencia",
        text: "¿Está seguro de eliminar esta regla de asignación?",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "No, cancelar",
        closeOnConfirm: false
    },
    function(isConfirm) {
        if (isConfirm) {
            $.post("../../controller/flujomapeo.php?op=eliminar", { map_id: map_id }, function() {
                $('#flujomapeo_data').DataTable().ajax.reload();
                swal("¡Eliminado!", "La regla ha sido eliminada.", "success");
            });
        }
    });
}

$('#btnnuevoflujomapeo').on('click', function() {
    $('#mdltitulo').html('Nueva Regla');
    $('#flujomapeo_form')[0].reset();
    $('#map_id').val('');
    // Resetear los combos de select2 a su estado inicial
    $('#cats_id').val(null).trigger('change');
    $('#creador_car_id').val(null).trigger('change');
    $('#asignado_car_id').val(null).trigger('change');
    $('#emp_id').val(null).trigger('change');
    $('#dp_id').val(null).trigger('change');
    $('#modalnuevoflujomapeo').modal('show');
});

$('#modalnuevoflujomapeo').on('hidden.bs.modal', function () {
    $("#flujomapeo_form")[0].reset();
    $("#map_id").html('');
    $("#cats_id").val('');
    $("#creador_car_id").val('');
    $('#asignado_car_id').val('');
    $('#emp_id').val('');
    $('#dp_id').val('');

});

init();