function init(){

}

$(document).ready(function() {
    // Esta pequeña función activa el plugin en nuestro campo de contraseña
        $(function() {
            $('#usu_pass').password({
                // Texto que aparece al pasar el mouse sobre el ojo (opcional)
                show: 'Mostrar Contraseña',
                hide: 'Ocultar Contraseña',
                // Ícono del ojo. Tu plantilla usa "font-icon", así que esto debería funcionar.
                eyeClass: 'font-icon-eye'
            });
        });
});

$(document).on('click', '#btnsoporte', function(){

    if($('#rol_id').val()==1){
        $('#lbltitulo').html('Soporte')
        $('#btnsoporte').html('Acceso usuario')
        $('#rol_id').val(2)
        $('#imgtipo').attr('src','public/img/user-2.png');
    }else{
        $('#lbltitulo').html('Usuario')
        $('#btnsoporte').html('Acceso soporte')
        $('#rol_id').val(1)
        $('#imgtipo').attr('src','public/img/user-1.png');

    }

    

})

init();