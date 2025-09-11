// Variable global para guardar las instancias de los gráficos y poder actualizarlos.
var charts = {};

/**
 * Función principal que carga o recarga todos los componentes del dashboard con los filtros aplicados.
 * @param {object} filtros - Un objeto con los filtros, ej: { dp_id: 12 }
 */
function cargarDashboard(override_filtros = {}) {

    let filtros = {
        dp_id: $('#filtro_departamento').val(),
        cats_id: $('#filtro_subcategoria').val(),
        tick_id: $('#filtro_ticket_id').val()
    };

    // Sobrescribir o añadir filtros pasados como argumentos
    filtros = { ...filtros, ...override_filtros };

    cargarKPIs(filtros);
    cargarKPIsAvanzados(filtros); // <-- nuevas métricas: primera respuesta, SLA, aging, reopen
    cargarGraficoTicketsPorMes(filtros);
    cargarTablaTopCategorias(filtros);
    cargarTablaTopUsuarios(filtros);
    cargarGraficoCargaAgente(filtros);
    cargarGraficoTiempoAgente(filtros); // ahora intenta usar versión optimizada con fallback
    cargarGraficoErroresTipo(filtros);
    cargarTablaRendimientoPaso(filtros);
    cargarTablaErroresAgente(filtros);
    cargarTablaResueltosAgente(filtros);
    cargarTopCategoriasTiempo(filtros); // top por tiempo de resolución
}

/**
 * Lógica principal que se ejecuta al cargar la página.
 * Detecta el rol del usuario y muestra la vista correspondiente.
 */
$(document).ready(function () {
    const rol_id_real = parseInt($('#rol_id_real').val());
    const usu_id = parseInt($('#user_idx').val());
    const dp_id = parseInt($('#user_dp_id').val());
    const is_jefe = parseInt($('#is_jefe_depto').val());

    if (rol_id_real === 3) { // Es Administrador
        $('#panel-filtros').show();
        cargarFiltros();
        cargarDashboard(); // Carga inicial sin filtros

        // Eventos para Admin
        $('#filtro_departamento, #filtro_subcategoria').on('change', function () {
            $('#filtro_ticket_id').val('');
            cargarDashboard();
        });

        $('#btn_buscar_ticket').on('click', function () {
            $('#filtro_departamento, #filtro_subcategoria').val(null).trigger('change.select2');
            cargarDashboard();
        });

        $('#btn_limpiar_filtros').on('click', function () {
            $('#filtro_departamento, #filtro_subcategoria').val(null).trigger('change.select2');
            $('#filtro_ticket_id').val('');
            cargarDashboard();
        });

    } else if (is_jefe === 1) { // Es Jefe de Departamento
        $('#panel-filtros').show();
        $('#filtro_departamento').closest('.col-md-4').hide(); // Oculta el filtro de depto.
        cargarFiltros(); // Carga los otros filtros como subcategoría
        cargarDashboard({ dp_id: dp_id }); // Carga el dashboard pre-filtrado

        // Eventos para Jefe de Depto (sin el filtro de depto)
        $('#filtro_subcategoria').on('change', function () {
            $('#filtro_ticket_id').val('');
            cargarDashboard({ dp_id: dp_id });
        });

        $('#btn_buscar_ticket').on('click', function () {
            $('#filtro_subcategoria').val(null).trigger('change.select2');
            cargarDashboard({ dp_id: dp_id });
        });

        $('#btn_limpiar_filtros').on('click', function () {
            $('#filtro_subcategoria').val(null).trigger('change.select2');
            $('#filtro_ticket_id').val('');
            cargarDashboard({ dp_id: dp_id });
        });

    } else { // Es Agente o usuario normal
        $('#panel-filtros').hide();
        cargarDashboard({ usu_id: usu_id });
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
        try { charts[chartId].destroy(); } catch (e) { /* safe */ }
        delete charts[chartId];
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
            const total = (data.totalAbiertos || 0) + (data.totalCerrados || 0);
            $("#lbltotal").html(total);
            $("#lblabiertos").html(data.totalAbiertos || 0);
            $("#lblcerrados").html(data.totalCerrados || 0);

            // --- INICIO DE LA NUEVA LÓGICA ---
            let tiempoFormateado = "N/A"; // Valor por defecto

            // Verificamos que tiempoPromedio no sea null o undefined
            if (data.tiempoPromedio !== null && data.tiempoPromedio !== undefined && data.tiempoPromedio !== '') {

                const total_horas = parseFloat(data.tiempoPromedio); // Aseguramos que sea un número
                if (!isNaN(total_horas)) {
                    const horas_enteras = Math.floor(total_horas);

                    const dias = Math.floor(horas_enteras / 24);
                    const horas = horas_enteras % 24;

                    let textoResultado = '';

                    if (dias > 0) {
                        // Maneja el plural para "día" (sin y con acento)
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
            }

            // 2. Revisamos si el texto resultante contiene la palabra "dia" (soportamos con/sin acento)
            if (tiempoFormateado.toLowerCase().includes('dia') || tiempoFormateado.toLowerCase().includes('día')) {
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

/**
 * Nuevas llamadas a KPIs avanzados (primera respuesta, SLA, aging backlog, reopen rate).
 * Actualiza elementos si estos existen en el DOM.
 */
function cargarKPIsAvanzados(filtros) {
    // 1) Primera respuesta
    $.ajax({
        url: '../../controller/reporte.php?op=get_tiempo_primera_respuesta',
        method: 'POST',
        data: filtros,
        dataType: 'json',
        success: function (resp) {
            var val = resp && (resp.horas_promedio_primera_respuesta !== undefined) ? resp.horas_promedio_primera_respuesta : resp;
            if ($('#lblPrimeraRespuesta').length) {
                $('#lblPrimeraRespuesta').html(val === null ? 'N/A' : (val + ' hrs'));
            }
        },
        error: function () {
            if ($('#lblPrimeraRespuesta').length) $('#lblPrimeraRespuesta').html('N/A');
        }
    });

    // 2) SLA compliance (por defecto 48h)
    var slaData = Object.assign({}, filtros, { hours: 48 });
    $.ajax({
        url: '../../controller/reporte.php?op=get_sla_compliance',
        method: 'POST',
        data: slaData,
        dataType: 'json',
        success: function (resp) {
            if (!resp) return;
            var pct = resp.pct === null ? 'N/A' : resp.pct + '%';
            if ($('#lblSLACompliance').length) $('#lblSLACompliance').html(pct);
            // Si existe una barra de progreso, la actualizamos (ej: #bar-sla)
            if ($('#bar-sla').length && resp.pct !== null) $('#bar-sla').css('width', resp.pct + '%');
        },
        error: function () {
            if ($('#lblSLACompliance').length) $('#lblSLACompliance').html('N/A');
        }
    });

    // 3) Aging backlog
    $.ajax({
        url: '../../controller/reporte.php?op=get_aging_backlog',
        method: 'POST',
        data: filtros,
        dataType: 'json',
        success: function (resp) {
            if (!resp) return;
            if ($('#tabla-aging tbody').length) {
                var tbody = $('#tabla-aging tbody');
                tbody.empty();
                resp.forEach(function (r) {
                    const row = `<tr><td>${r.rango}</td><td><span class="label label-pill label-primary">${r.total}</span></td></tr>`;
                    tbody.append(row);
                });
            }
        },
        error: function () {
            // silenciar
        }
    });

    // 4) Reopen rate
    $.ajax({
        url: '../../controller/reporte.php?op=get_reopen_rate',
        method: 'POST',
        data: filtros,
        dataType: 'json',
        success: function (resp) {
            if (!resp) return;
            var pct = resp.pct === null ? 'N/A' : resp.pct + '%';
            if ($('#lblReopenRate').length) $('#lblReopenRate').html(pct);
        },
        error: function () {
            if ($('#lblReopenRate').length) $('#lblReopenRate').html('N/A');
        }
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

/**
 * Intentamos usar la versión optimizada del endpoint para tiempo por agente.
 * Si falla (404 / error), hacemos fallback al endpoint original.
 */
function cargarGraficoTiempoAgente(filtros) {
    destruirChartSiExiste('bar-chart-tiempo-agente');

    function renderTiempoAgente(payload) {
        // payload: { labels: [...], data: [...] } OR { labels:..., data:... } OR { rows: [...] } OR (fallback) rows array
        let labels = [], data = [];

        if (Array.isArray(payload)) {
            // payload es directamente un array de filas [{usu_nom, usu_ape, horas_promedio_respuesta}, ...]
            labels = payload.map(r => (r.usu_nom || '') + ' ' + (r.usu_ape || ''));
            data = payload.map(r => parseFloat(r.horas_promedio_respuesta || 0));
        } else if (payload && Array.isArray(payload.rows)) {
            labels = payload.rows.map(r => (r.usu_nom || '') + ' ' + (r.usu_ape || ''));
            data = payload.rows.map(r => parseFloat(r.horas_promedio_respuesta || 0));
        } else if (payload && Array.isArray(payload.labels) && Array.isArray(payload.data)) {
            labels = payload.labels;
            data = payload.data.map(v => parseFloat(v || 0));
        } else if (payload && Array.isArray(payload.datos)) {
            // por si tu backend devuelve {datos: [...]}
            labels = payload.datos.map(r => (r.usu_nom || '') + ' ' + (r.usu_ape || ''));
            data = payload.datos.map(r => parseFloat(r.horas_promedio_respuesta || 0));
        } else {
            console.warn('Formato de payload inesperado para tiempo-agente:', payload);
            // no podemos renderizar
            if ($('#bar-chart-tiempo-agente').length) {
                $('#bar-chart-tiempo-agente').closest('.card-block').append('<div class="text-muted small">No hay datos para el gráfico de tiempo por agente.</div>');
            }
            return;
        }

        const ctx = document.getElementById('bar-chart-tiempo-agente');
        charts['bar-chart-tiempo-agente'] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Horas Promedio por Tarea',
                    data: data,
                    backgroundColor: 'rgba(153, 102, 255, 0.6)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: { x: { beginAtZero: true } }
            }
        });
    }

    // Intento 1: endpoint optimizado
    $.ajax({
        url: '../../controller/reporte.php?op=get_tiempo_agente_opt',
        method: 'POST',
        data: filtros,
        dataType: 'json',
        success: function (datos) {
            // Si 'datos' no es array, puede ser {labels,data} o {rows: [...]}, lo manejamos en renderTiempoAgente
            renderTiempoAgente(datos);
        },
        error: function (xhr, status, err) {
            console.warn('get_tiempo_agente_opt falló:', status, err);
            // Fallback: intentar endpoint original
            $.ajax({
                url: '../../controller/reporte.php?op=get_tiempo_agente',
                method: 'POST',
                data: filtros,
                dataType: 'json',
                success: function (response) {
                    // la versión original devuelve { labels: [...], data: [...] }
                    if (response && Array.isArray(response.labels) && Array.isArray(response.data)) {
                        renderTiempoAgente({ labels: response.labels, data: response.data });
                    } else {
                        // Por si la versión original devolvió otra cosa
                        renderTiempoAgente(response);
                    }
                },
                error: function (xhr2, status2, err2) {
                    console.error('Ambos endpoints fallaron para tiempo por agente:', status2, err2);
                    if ($('#bar-chart-tiempo-agente').length) {
                        $('#bar-chart-tiempo-agente').closest('.card-block').append('<div class="text-muted small">No se pudo cargar el gráfico de tiempo por agente.</div>');
                    }
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

function cargarTablaResueltosAgente(filtros) {
    $.ajax({
        url: '../../controller/reporte.php?op=get_tickets_resueltos',
        method: 'POST',
        data: filtros,
        dataType: 'json',
        success: function (data) {
            const tbody = $('#tabla-resueltos-agente tbody');
            tbody.empty();
            data.forEach(item => {
                const row = `<tr>
                                <td>${item.agente}</td>
                                <td><span class="label label-pill label-info">${item.total_cerrados}</span></td>
                             </tr>`;
                tbody.append(row);
            });
        }
    });
}

/**
 * Top categorías por tiempo promedio de resolución (nuevo endpoint).
 * Llena #tabla-top-categorias-tiempo si existe.
 */
function cargarTopCategoriasTiempo(filtros) {
    $.ajax({
        url: '../../controller/reporte.php?op=get_top_categorias_tiempo',
        method: 'POST',
        data: filtros,
        dataType: 'json',
        success: function (data) {
            if ($('#tabla-top-categorias-tiempo tbody').length) {
                const tbody = $('#tabla-top-categorias-tiempo tbody');
                tbody.empty();
                data.forEach(item => {
                    const horas = isNaN(parseFloat(item.hrs_promedio)) ? 'N/A' : parseFloat(item.hrs_promedio).toFixed(1);
                    const row = `<tr>
                                    <td>${item.cat_nom}</td>
                                    <td>${item.cant}</td>
                                    <td>${horas} hrs</td>
                                </tr>`;
                    tbody.append(row);
                });
            }
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
