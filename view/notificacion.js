usu_id = $('#user_idx').val();


$(document).ready(function(){
    mostrar_notificacion()  
    $.post("../../controller/notificacion.php?op=notificacionespendientes", {usu_id:usu_id},function(data) {
        $('#lblmenulist').html(data)
    });   
})

function mostrar_notificacion(){

    var formData = new FormData();
    
    formData.append('usu_id',usu_id);

    $.ajax({
        url: "../../controller/notificacion.php?op=mostrar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
           if(data != ''){
            data = JSON.parse(data);
            $.notify({
                icon: 'glyphicon glyphicon-star',
                message: data.not_mensaje,
                url:'http://localhost:8000/view/DetalleTicket/?ID='+data.tick_id    
            })

            $.post("../../controller/notificacion.php?op=actualizar", {not_id:data.not_id},function(data) {
            }); 

            }

        if( $('#dd-notification').attr('aria-expanded') == 'false' ) {
            $.post("../../controller/notificacion.php?op=notificacionespendientes", {usu_id:usu_id},function(data) {
                $('#lblmenulist').html(data);
            }); 
        }

        $.post("../../controller/notificacion.php?op=contar", {usu_id:usu_id},function(data) {
        data = JSON.parse(data);
        $('#lblcontar').html(data.totalnotificaciones)

        if(data.totalnotificaciones != 0){
            $('#dd-notification').addClass('active');
        }

        }); 
         
        }
    })


}

function verNotificacion(not_id){
    $.post("../../controller/notificacion.php?op=leido", {not_id:not_id});
}

setInterval(function(){
    mostrar_notificacion();
},5000);