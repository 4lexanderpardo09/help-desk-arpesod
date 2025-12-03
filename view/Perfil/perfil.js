$(document).ready(function () {
    var usu_id = $('#user_idx').val();

    // Cargar firma actual
    cargarFirma();

    $('#firma_form').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('usu_id', usu_id);
        formData.append('op', 'guardar_firma');

        $.ajax({
            url: '../../controller/usuario.php?op=guardar_firma',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                var data = JSON.parse(response);
                if (data.status === 'success') {
                    swal("¡Éxito!", data.message, "success");
                    cargarFirma();
                } else {
                    swal("Error", data.message, "error");
                }
            },
            error: function () {
                swal("Error", "No se pudo guardar la firma.", "error");
            }
        });
    });

    function cargarFirma() {
        $.post('../../controller/usuario.php?op=obtener_firma', { usu_id: usu_id }, function (response) {
            var data = JSON.parse(response);
            if (data.status === 'success') {
                $('#firma_preview').html('<img src="../../public/img/firmas/' + usu_id + '/' + data.firma + '" style="max-width: 100%; max-height: 150px;">');
            } else {
                $('#firma_preview').html('<span class="text-muted">No hay firma cargada</span>');
            }
        });
    }
});
