function init(){
    // Esta función se puede dejar vacía o usar para inicializar otros componentes si es necesario.
}

$(document).ready(function(){
    // Obtenemos el ID del ticket desde la URL
    var tick_id = getUrlParameter('ID');

    // Cargar la cabecera del ticket (título, estado, etc.)
    $.post("../../controller/ticket.php?op=mostrar", { tick_id : tick_id }, function (data) {
        data = JSON.parse(data);
        $('#lbltickestadoh').html(data.tick_estado);
        $('#lblprioridadh').html(data.pd_nom);
        $('#lblnomusuarioh').html(data.usu_nom + ' ' + data.usu_ape);
        $('#lblfechacreah').html(data.fech_crea);
        $('#lblticketidh').html("Detalle del tikect #" + data.tick_id);
        $('#cat_id').val(data.cat_nom);
        $('#cats_id').val(data.cats_nom);
        $('#tick_titulo').val(data.tick_titulo);
        // Usamos summernote para mostrar la descripción con formato
        $('#tickd_descripusu').summernote('code', data.tick_descrip);
    });

    // Cargar el historial completo del ticket (comentarios, asignaciones, etc.)
    $.post("../../controller/ticket.php?op=listarhistorial", { tick_id : tick_id }, function (data) {
        $('#lbldetalle').html(data);
    });

    // Inicializar el editor Summernote en modo "solo lectura"
    $('#tickd_descripusu').summernote({
        height: 150,
        toolbar: [], // Sin barra de herramientas
    });
    // Deshabilitar la edición
    $('#tickd_descripusu').summernote('disable');

});

// Función para obtener parámetros de la URL
var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

init();
