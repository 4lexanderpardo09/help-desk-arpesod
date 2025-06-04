function init() {

}

$(document).ready(function () {
    var usu_id = $('#user_idx').val()
    totalTickets(usu_id);
    totalTicketsAbiertos(usu_id);
    totalTicketsCerrados(usu_id);

    if($("#rol_idx").val()==1){
        totalCategoriaGraficoUsuario(usu_id);
    }else{
        totalCategoriaGrafico();
    }

});

function totalTickets(usu_id) {

    if ($("#rol_idx").val() == 1) {
        $.post("../../controller/usuario.php?op=total", { usu_id: usu_id }, function (data) {
            data = JSON.parse(data);
            $("#lbltotal").html(data.TOTAL)
        });
    } else {
        $.post("../../controller/ticket.php?op=total", function (data) {
            data = JSON.parse(data)
            console.log(data);
            $("#lbltotal").html(data.TOTAL)
        });
    }
}

function totalTicketsAbiertos(usu_id) {

    if ($("#rol_idx").val() == 1) {
        $.post("../../controller/usuario.php?op=totalabierto", { usu_id: usu_id }, function (data) {
            data = JSON.parse(data);
            $("#lblabiertos").html(data.TOTAL)
        });
    } else {
        $.post("../../controller/ticket.php?op=totalabierto", function (data) {
            data = JSON.parse(data)
            console.log(data);
            $("#lblabiertos").html(data.TOTAL)
        });
    }
}

function totalTicketsCerrados(usu_id) {

    if ($("#rol_idx").val() == 1) {
        $.post("../../controller/usuario.php?op=totalcerrado", { usu_id: usu_id }, function (data) {
            data = JSON.parse(data);
            $("#lblcerrados").html(data.TOTAL)
        });
    } else {
        $.post("../../controller/ticket.php?op=totalcerrado", function (data) {
            data = JSON.parse(data)
            console.log(data);
            $("#lblcerrados").html(data.TOTAL)
        });
    }
}

function totalCategoriaGrafico() {
    $.ajax({
        url: '../../controller/ticket.php?op=grafico',  
        method: 'POST',
        dataType: 'json',
        success: function (response) {
            const labels = response.map(item => item.nom);
            const data = response.map(item => item.total);

            const ctx = document.getElementById('bar-chart');
            
            

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total por categoría',
                        data: data,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Cantidad de tickets por categoría'
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
}

function totalCategoriaGraficoUsuario(usu_id) {
    $.ajax({
        url: '../../controller/usuario.php?op=graficousuario',  
        method: 'POST',
        data: {usu_id:usu_id},
        dataType: 'json',
        success: function (response) {
            const labels = response.map(item => item.nom);
            const data = response.map(item => item.total);

            const ctx = document.getElementById('bar-chart');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total por categoría',
                        data: data,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Cantidad de tickets por categoría'
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
}



init();