// Variable global para guardar las instancias de los gráficos y poder actualizarlos.
var charts = {};

/**
 * Función principal que carga o recarga todos los componentes del dashboard con los filtros aplicados.
 * @param {object} filtros - Un objeto con los filtros, ej: { dp_id: 12 }
 */
function cargarDashboard() {

    const filtros = {
        dp_id: $('#filtro_departamento').val(),
        cats_id: $('#filtro_subcategoria').val(),
        tick_id: $('#filtro_ticket_id').val()
    };

    console.log("Cargando dashboard con filtros:", filtros);
    cargarKPIs(filtros);
    cargarGraficoTicketsPorMes(filtros);
    cargarTablaTopCategorias(filtros);
    cargarTablaTopUsuarios(filtros);
    cargarGraficoCargaAgente(filtros);
    cargarGraficoTiempoAgente(filtros);
    cargarGraficoErroresTipo(filtros);
    cargarTablaRendimientoPaso(filtros);
    cargarTablaErroresAgente(filtros);
}

/**
 * Lógica principal que se ejecuta al cargar la página.
 * Detecta el rol del usuario y muestra la vista correspondiente.
 */
$(document).ready(function () {
    const rol_id_real = parseInt($('#rol_id_real').val());
    const usu_id = parseInt($('#user_idx').val());
    // IMPORTANTE: Asegúrate de tener un input oculto en tu vista con el dp_id del usuario de la sesión.
    // Ej: <input type="hidden" id="user_dp_id" value="<?php echo $_SESSION['dp_id']; ?>">
    const dp_id = parseInt($('#user_dp_id').val());



    if (rol_id_real === 3) { // Es Administrador
        $('#panel-filtros').show();
        cargarFiltros();
        cargarDashboard(); // Carga inicial sin filtros

        // Evento que se activa cuando el Admin cambia la selección del filtro
        $('#filtro_departamento, #filtro_subcategoria').on('change', function () {
            // Si se cambia un combo, se limpia el filtro de ID de ticket
            $('#filtro_ticket_id').val('');
            cargarDashboard();
        });

        $('#btn_buscar_ticket').on('click', function () {
            // Si se busca por un ID, es buena idea limpiar los otros filtros para evitar conflictos.
            $('#filtro_departamento, #filtro_subcategoria').val(null).trigger('change.select2');
            // Llamamos a la función principal para recargar todos los datos con el nuevo filtro.
            cargarDashboard();
        });

        $('#btn_limpiar_filtros').on('click', function () {
            $('#filtro_departamento, #filtro_subcategoria').val(null).trigger('change.select2');
            $('#filtro_ticket_id').val('');
            cargarDashboard(); // Recarga con los filtros limpios
        });

    } else if (dp_id > 0) { // Es Jefe de Departamento (tiene un dp_id asignado)
        $('#panel-filtros').hide();
        cargarDashboard({ dp_id: dp_id }); // Carga el dashboard filtrado por su departamento

    } else { // Es Agente normal
        $('#panel-filtros').hide();
        // Ocultamos todos los contenedores de gráficos y tablas
        $('.card').hide();
        // Mostramos solo sus KPIs personales
        totalTicketsUsuario(usu_id);
    }
});

/**
 * Carga las opciones en el filtro de departamentos para el Administrador.
 */
function cargarFiltros() {
    $.post("../../controller/reporte.php?op=get_filtros_departamento", function (data) {
        var options = '<option value="">Todos los Departamentos</option>';

        data.forEach(function (depto) {
            options += `<option value="${depto.dp_id}">${depto.dp_nom}</option>`;
        });
        $('#filtro_departamento').html(options);
    });

    $.post("../../controller/reporte.php?op=get_filtros_subcategoria", function (data) {
        var options = '<option value="">Todas las Subcategorías</option>';
        data.forEach(function (subcat) {
            options += `<option value="${subcat.cats_id}">${subcat.cats_nom}</option>`;
        });
        $('#filtro_subcategoria').html(options);
    });
}

/**
 * Función de ayuda para destruir un gráfico si ya existe, antes de volver a crearlo.
 * @param {string} chartId - El ID del canvas del gráfico.
 */
function destruirChartSiExiste(chartId) {
    if (charts[chartId]) {
        charts[chartId].destroy();
    }
}

// --- FUNCIONES DE CARGA DE KPIs (AHORA TODAS ACEPTAN FILTROS) ---

function cargarKPIs(filtros) {
    $.ajax({
        url: '../../controller/reporte.php?op=get_kpis',
        method: 'POST',
        data: filtros,
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
    });
}

function cargarGraficoTicketsPorMes(filtros) {
    destruirChartSiExiste('line-chart-mes');
    $.ajax({
        url: '../../controller/reporte.php?op=get_tickets_por_mes',
        method: 'POST',
        data: filtros,
        dataType: 'json',
        success: function (response) {
            const ctx = document.getElementById('line-chart-mes');
            charts['line-chart-mes'] = new Chart(ctx, {
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
        }
    });
}

function cargarGraficoCargaAgente(filtros) {
    destruirChartSiExiste('bar-chart-agente');
    $.ajax({
        url: '../../controller/reporte.php?op=get_carga_por_agente',
        method: 'POST',
        data: filtros,
        dataType: 'json',
        success: function (response) {
            const ctx = document.getElementById('bar-chart-agente');
            charts['bar-chart-agente'] = new Chart(ctx, {
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
        }
    });
}

function cargarTablaTopCategorias(filtros) {
    $.ajax({
        url: '../../controller/reporte.php?op=get_top_categorias',
        method: 'POST',
        data: filtros,
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
    });
}

function cargarTablaTopUsuarios(filtros) {
    $.ajax({
        url: '../../controller/reporte.php?op=get_top_usuarios',
        method: 'POST',
        data: filtros,
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
        }
    });
}

function cargarGraficoTiempoAgente(filtros) {
    destruirChartSiExiste('bar-chart-tiempo-agente');
    $.ajax({
        url: '../../controller/reporte.php?op=get_tiempo_agente',
        method: 'POST',
        data: filtros,
        dataType: 'json',
        success: function (response) {
            const ctx = document.getElementById('bar-chart-tiempo-agente');
            charts['bar-chart-tiempo-agente'] = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: response.labels,
                    datasets: [{
                        label: 'Horas Promedio por Tarea',
                        data: response.data,
                        backgroundColor: 'rgba(153, 102, 255, 0.6)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y', // Gráfico horizontal
                    scales: { x: { beginAtZero: true } }
                }
            });
        }
    });
}

function cargarGraficoErroresTipo(filtros) {
    destruirChartSiExiste('pie-chart-errores-tipo');
    $.ajax({
        url: '../../controller/reporte.php?op=get_errores_tipo',
        method: 'POST',
        data: filtros,
        dataType: 'json',
        success: function (response) {
            const ctx = document.getElementById('pie-chart-errores-tipo');
            charts['pie-chart-errores-tipo'] = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: response.labels,
                    datasets: [{
                        label: 'Total Errores',
                        data: response.data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)'
                        ]
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }
    });
}

function cargarTablaRendimientoPaso(filtros) {
    $.ajax({
        url: '../../controller/reporte.php?op=get_rendimiento_paso',
        method: 'POST',
        data: filtros,
        dataType: 'json',
        success: function (data) {
            const tbody = $('#tabla-rendimiento-paso tbody');
            tbody.empty();
            data.forEach(item => {
                const horas = parseFloat(item.horas_promedio_paso).toFixed(1);
                const row = `<tr>
                                <td>${item.paso_nombre}</td>
                                <td>${horas} hrs</td>
                                <td><span class="label label-pill label-success">${item.a_tiempo}</span></td>
                                <td><span class="label label-pill label-danger">${item.atrasado}</span></td>
                             </tr>`;
                tbody.append(row);
            });
        }
    });
}

function cargarTablaErroresAgente(filtros) {
    $.ajax({
        url: '../../controller/reporte.php?op=get_errores_agente',
        method: 'POST',
        data: filtros,
        dataType: 'json',
        success: function (data) {
            const tbody = $('#tabla-errores-agente tbody');
            tbody.empty();
            data.forEach(item => {
                const row = `<tr>
                                <td>${item.usu_nom} ${item.usu_ape}</td>
                                <td><span class="label label-pill label-danger">${item.total_errores_atribuidos}</span></td>
                             </tr>`;
                tbody.append(row);
            });
        }
    });
}

function totalTicketsUsuario(usu_id) {
    // Esconder los contenedores de KPIs avanzados
    $('.card').hide();
    // Mostrar solo las tarjetas de KPIs básicos
    $('.statistic-box').closest('.col-sm-6').show();

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