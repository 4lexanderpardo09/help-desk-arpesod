// Función de inicialización (opcional, pero buena práctica)
function init() {
    // Código que se ejecuta al iniciar, si es necesario.
}

$(document).ready(function () {
    const usu_id = $('#user_idx').val();
    const usu_asig = $('#user_idx').val();
    const rol_id = $("#rol_idx").val();
    var rol_real_id = $('#rol_real_idx').val()


    // Si el rol es Administrador (o soporte, ej: rol_id=2), muestra el dashboard completo.
    // Si el rol es Usuario (rol_id=1), puedes decidir mostrar una vista más simple.
    if (rol_id == 2 && rol_real_id != 3) { 
        // Cargar todos los componentes del dashboard de reportes
        cargarKPIs(usu_asig);
        cargarGraficoTicketsPorMes(usu_asig);
        cargarTablaTopCategorias(usu_asig);
        cargarTablaTopUsuarios(usu_asig);
        $('#cargaagente').remove()
    } else if (rol_id == 1 && rol_real_id != 3) {
        // Para el usuario normal, puedes mantener la vista antigua o una versión simplificada.
        // Por ejemplo, solo mostrar sus propios tickets.
        totalTicketsUsuario(usu_id); // Función simplificada para usuario
        $('#cargaagente').remove()
    }else{
        cargarKPIs();
        cargarGraficoTicketsPorMes();
        cargarTablaTopCategorias();
        cargarTablaTopUsuarios();
        cargarGraficoCargaAgente();
    }
});

/**
 * Carga las tarjetas de KPI (Key Performance Indicators).
 * Realiza una única llamada para obtener abiertos, cerrados y tiempo promedio.
 */
function cargarKPIs(usu_asig) {

    if(usu_asig == null){
        $.ajax({
            url: '../../controller/reporte.php?op=get_kpis',
            method: 'POST',
            dataType: 'json',
            success: function (data) {
                const total = data.totalAbiertos + data.totalCerrados;
                $("#lbltotal").html(total);
                $("#lblabiertos").html(data.totalAbiertos);
                $("#lblcerrados").html(data.totalCerrados);
                 // --- INICIO DE LA NUEVA LÓGICA ---
        let tiempoFormateado = "N/A"; // Valor por defecto

        // Verificamos que tiempoPromedio no sea null o undefined
        if (data.tiempoPromedio) {
            
            const total_horas = parseFloat(data.tiempoPromedio); // Aseguramos que sea un número
            const horas_enteras = Math.floor(total_horas);

            const dias = Math.floor(horas_enteras / 24);
            const horas = horas_enteras % 24;

            let textoResultado = '';

            if (dias > 0) {
                // Maneja el plural para "día"
                textoResultado += dias + (dias === 1 ? ' dia' : ' dias');
            }

            if (horas > 0) {
                if (textoResultado !== '') {
                    textoResultado += ' '; // Agrega un espacio si ya hay días
                }
                // Maneja el plural para "hora"
                textoResultado += horas + (horas === 1 ? ' hora' : ' horas');
            }

            // Si el tiempo es menor a 1 hora, mostramos un texto por defecto
            if (textoResultado === '') {
                tiempoFormateado = "Menos de 1 hora";
            } else {
                tiempoFormateado = textoResultado;
            }
        }
        
         // 2. Revisamos si el texto resultante contiene la palabra "día"
         if (tiempoFormateado.includes('día')) {
            // Si contiene "día", le AÑADIMOS una clase para hacerlo más pequeño
            $("#lblpromedio").addClass('texto-largo');
        } else {
            // Si NO contiene "día", le QUITAMOS la clase para que vuelva a su tamaño normal
            $("#lblpromedio").removeClass('texto-largo');
        }

        // Asignamos el resultado final al elemento HTML
        $("#lblpromedio").html(tiempoFormateado);
        // --- FIN DE LA NUEVA LÓGICA ---
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar KPIs:", error);
            }
        });    
    }else{
        $.ajax({
            url: '../../controller/reporte.php?op=get_kpis_x_agente',
            method: 'POST',
            data: { usu_asig: usu_asig },
            dataType: 'json',
            success: function (data) {
                const total = data.totalAbiertos + data.totalCerrados;
                $("#lbltotal").html(total);
                $("#lblabiertos").html(data.totalAbiertos);
                $("#lblcerrados").html(data.totalCerrados);
                // Asegurarse de que el valor no sea null y añadir 'horas'
                const tiempoPromedio = data.tiempoPromedio ? data.tiempoPromedio + ' hrs' : 'N/A';
                $("#lblpromedio").html(tiempoPromedio);
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar KPIs:", error);
            }
        });
    }

    
}

/**
 * Carga el gráfico de líneas para la tendencia de tickets por mes.
 */
function cargarGraficoTicketsPorMes(usu_asig) {
    if(usu_asig == null){
        $.ajax({
            url: '../../controller/reporte.php?op=get_tickets_por_mes',
            method: 'POST',
            dataType: 'json',
            success: function (response) {
                const ctx = document.getElementById('line-chart-mes');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: response.labels,
                        datasets: [{
                            label: 'Tickets Creados',
                            data: response.data,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            fill: true,
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true } }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar gráfico de tickets por mes:", error);
            }
        });
    }else{
        $.ajax({
            url: '../../controller/reporte.php?op=get_tickets_por_mes_x_agente',
            method: 'POST',
            data: { usu_asig: usu_asig },
            dataType: 'json',
            success: function (response) {
                const ctx = document.getElementById('line-chart-mes');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: response.labels,
                        datasets: [{
                            label: 'Tickets Creados',
                            data: response.data,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            fill: true,
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true } }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar gráfico de tickets por mes:", error);
            }
        });
    }
}

/**
 * Carga el gráfico de barras para la carga de trabajo por agente.
 */
function cargarGraficoCargaAgente() {
    $.ajax({
        url: '../../controller/reporte.php?op=get_carga_por_agente',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            const ctx = document.getElementById('bar-chart-agente');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: response.labels,
                    datasets: [{
                        label: 'Tickets Abiertos Asignados',
                        data: response.data,
                        backgroundColor: 'rgba(255, 159, 64, 0.6)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y', // Hace el gráfico horizontal para mejor legibilidad de nombres
                    scales: { x: { beginAtZero: true } },
                    plugins: { legend: { display: false } }
                }
            });
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar gráfico de carga por agente:", error);
        }
    });
}

/**
 * Carga la tabla con el top 10 de categorías y subcategorías.
 */
function cargarTablaTopCategorias(usu_asig) {
    if(usu_asig == null){
        $.ajax({
            url: '../../controller/reporte.php?op=get_top_categorias',
            method: 'POST',
            dataType: 'json',
            success: function (data) {
                const tbody = $('#tabla-categorias tbody');
                tbody.empty(); // Limpiar tabla antes de agregar nuevos datos
                data.forEach(item => {
                    const row = `<tr>
                                    <td>${item.categoria}</td>
                                    <td>${item.subcategoria}</td>
                                    <td><span class="label label-pill label-primary">${item.cantidad_de_tickets}</span></td>
                                 </tr>`;
                    tbody.append(row);
                });
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar top categorías:", error);
            }
        });
    }else{
        $.ajax({
            url: '../../controller/reporte.php?op=get_top_categorias_x_agente',
            method: 'POST',
            data: {usu_asig:usu_asig},
            dataType: 'json',
            success: function (data) {
                const tbody = $('#tabla-categorias tbody');
                tbody.empty(); // Limpiar tabla antes de agregar nuevos datos
                data.forEach(item => {
                    const row = `<tr>
                                    <td>${item.categoria}</td>
                                    <td>${item.subcategoria}</td>
                                    <td><span class="label label-pill label-primary">${item.cantidad_de_tickets}</span></td>
                                 </tr>`;
                    tbody.append(row);
                });
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar top categorías:", error);
            }
        });
    }
}

/**
 * Carga la tabla con el top 10 de usuarios que crean más tickets.
 */
function cargarTablaTopUsuarios(usu_asig) {
    if(usu_asig == null){
        $.ajax({
            url: '../../controller/reporte.php?op=get_top_usuarios',
            method: 'POST',
            dataType: 'json',
            success: function (data) {
                const tbody = $('#tabla-usuarios tbody');
                tbody.empty(); // Limpiar tabla
                data.forEach(item => {
                    const departamento = item.departamento || 'Sin Departamento';
                    const row = `<tr>
                                    <td>${item.nombre_usuario}</td>
                                    <td>${departamento}</td>
                                    <td><span class="label label-pill label-success">${item.tickets_creados}</span></td>
                                 </tr>`;
                    tbody.append(row);
                });
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar top usuarios:", error);
            }
        });
    }else{
        $.ajax({
            url: '../../controller/reporte.php?op=get_top_usuarios_x_agente',
            method: 'POST',
            data: {usu_asig:usu_asig},
            dataType: 'json',
            success: function (data) {
                const tbody = $('#tabla-usuarios tbody');
                tbody.empty(); // Limpiar tabla
                data.forEach(item => {
                    const departamento = item.departamento || 'Sin Departamento';
                    const row = `<tr>
                                    <td>${item.nombre_usuario}</td>
                                    <td>${departamento}</td>
                                    <td><span class="label label-pill label-success">${item.tickets_creados}</span></td>
                                 </tr>`;
                    tbody.append(row);
                });
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar top usuarios:", error);
            }
        });
    }
}

/**
 * Función simplificada para usuarios no-admin, similar a tu lógica original.
 * @param {number} usu_id El ID del usuario.
 */
function totalTicketsUsuario(usu_id) {
    // Esconder elementos que el usuario no necesita ver
    $('#lblpromedio').closest('.col-sm-6').hide();
    $('canvas').closest('.card').hide();
    $('table').closest('.row').hide();
    
    // Cargar sus KPIs personales
    $.post("../../controller/usuario.php?op=total", { usu_id: usu_id }, function (data) {
        $("#lbltotal").html(JSON.parse(data).TOTAL);
    });
    $.post("../../controller/usuario.php?op=totalabierto", { usu_id: usu_id }, function (data) {
        $("#lblabiertos").html(JSON.parse(data).TOTAL);
    });
    $.post("../../controller/usuario.php?op=totalcerrado", { usu_id: usu_id }, function (data) {
        $("#lblcerrados").html(JSON.parse(data).TOTAL);
    });
}

// Llama a la función de inicialización.
init();