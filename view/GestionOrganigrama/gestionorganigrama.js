var tabla;

function init() {
    $("#organigrama_form").on("submit", function(e) {
        guardaryeditar(e);
    });
}

function guardaryeditar(e) {
    e.preventDefault();
    var formData = new FormData($("#organigrama_form")[0]);
    $.ajax({
        url: "../../controller/organigrama.php?op=guardar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos) {
            $('#organigrama_form')[0].reset();
            $("#modalorganigrama").modal('hide');
            $('#organigrama_data').DataTable().ajax.reload();
            drawChart(); // Redibujar el gráfico

            swal({
                title: "Correcto!",
                text: "Completado.",
                type: "success",
                confirmButtonClass: "btn-success"
            });
        }
    });
}

$(document).ready(function() {
    // Cargar combos del modal
    $.post("../../controller/organigrama.php?op=combo_cargos", function(data) {
        $('#car_id').html(data);
    });
    $.post("../../controller/organigrama.php?op=combo_cargos", function(data) {
        $('#jefe_car_id').html(data);
    });

    // Cargar tabla de jerarquías
    tabla = $('#organigrama_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": true,
        lengthChange: false,
        colReorder: true,
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        "ajax": {
            url: '../../controller/organigrama.php?op=listar',
            type: "post",
            dataType: "json",
            error: function(e) {
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
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        }
    }).DataTable();

    // Cargar gráfico
    google.charts.load('current', {packages:['orgchart']});
    google.charts.setOnLoadCallback(drawChart);
});

function drawChart() {
    $.ajax({
        url: "../../controller/organigrama.php?op=datos_grafico",
        dataType: "json",
        success: function(jsonData) {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Name');
            data.addColumn('string', 'Manager');
            data.addColumn('string', 'ToolTip');

            data.addRows(jsonData);

            var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
            chart.draw(data, {allowHtml:true});
        },
        error: function(e) {
            console.log(e.responseText);
        }
    });
}

function eliminar(org_id) {
    swal({
        title: "Advertencia",
        text: "¿Está seguro de eliminar la relación?",
        type: "error",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Si",
        cancelButtonText: "No",
        closeOnConfirm: false
    }, function(isConfirm) {
        if (isConfirm) {
            $.post("../../controller/organigrama.php?op=eliminar", { org_id: org_id }, function(data) {
                $('#organigrama_data').DataTable().ajax.reload();
                drawChart();
            });

            swal({
                title: "Correcto!",
                text: "Relación eliminada.",
                type: "success",
                confirmButtonClass: "btn-success"
            });
        }
    });
}

$('#btnnuevo').click(function() {
    $('#mdltitulo').html('Nueva Relación');
    $('#organigrama_form')[0].reset();
    $('#modalorganigrama').modal('show');
});

init();
