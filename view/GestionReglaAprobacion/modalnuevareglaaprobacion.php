<div class="modal fade bd-example-modal-lg"
    id="modalnuevareglaaprobacion"
    tabindex="-1"
    role="dialog"
    aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="modal-close" data-dismiss="modal" aria-label="Close">
                    <i class="font-icon-close-2"></i>
                </button>
                <h4 class="modal-title" id="mdltitulo"></h4>
            </div>
            <form method="post" id="reglaaprobacion_form">
                <div class="modal-body">

                    <input type="hidden" id="regla_id" name="regla_id">
                    <fieldset class="form-group">
                        <label for="creador_car_id"><b>Si</b> un ticket es creado por alguien con el cargo:</label>
                        <select class="form-control" id="creador_car_id" name="creador_car_id" data-placeholder="Seleccione" required>
                        </select>
                    </fieldset>
                    <fieldset class="form-group">
                        <label for="aprobador_usu_id"><b>Entonces</b> debe ser aprobado SIEMPRE por el usuario:</label>
                        <select class="form-control" id="aprobador_usu_id" name="aprobador_usu_id" data-placeholder="Seleccione" required>                            
                        </select>                    
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-rounded btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" name="action" id="#" value="add" class="btn btn-rounded btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>