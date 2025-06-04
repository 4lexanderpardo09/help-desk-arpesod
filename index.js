function init(){

}

$(document).ready(function() {
    

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