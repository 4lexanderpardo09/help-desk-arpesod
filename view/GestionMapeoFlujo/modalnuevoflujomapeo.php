<div class="modal fade bd-example-modal-lg"
    id="modalnuevoflujomapeo"
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
            <form method="post" id="flujomapeo_form">
                <div class="modal-body">
                    
                    <input type="hidden" id="map_id" name="map_id">
                    <fieldset class="form-group">
                        <label class="form-label semibold" for="emp_id">Empresa</label>
                        <select class="form-control" id="emp_id" name="emp_id" data-placeholder="Seleccione" required>
                        </select>
                    </fieldset>
                    <fieldset class="form-group">
                        <label class="form-label semibold" for="dp_id">Departamento</label>
                        <select class="form-control" id="dp_id" name="dp_id" data-placeholder="Seleccione" required>
                        </select>
                    </fieldset>
                    <fieldset class="form-group">
                        <label class="form-label semibold" for="cat_id">Categoria</label>
                        <select class="form-control" id="cat_id" name="cat_id" data-placeholder="Seleccione" required>
                        </select>
                    </fieldset>
                    <fieldset class="form-group">
                        <label for="cats_id"><b>Si</b> un ticket es de la subcategoría:</label>
                        <select class="form-control" id="cats_id" name="cats_id" data-placeholder="Seleccione" required>                            
                        </select>                    
                    </fieldset>
                    <fieldset class="form-group">
                         <label for="creador_car_id"><b>Y</b> es creado por alguien con el cargo:</label>
                        <select class="form-control" id="creador_car_id" name="creador_car_id" data-placeholder="Seleccione"" required>
                        </select>
                    </fieldset>
                    <fieldset class="form-group">
                         <label for="asignado_car_id"><b>Entonces</b> asignar a alguien con el cargo:</label>
                        <select class="form-control" id="asignado_car_id" name="asignado_car_id" data-placeholder="Seleccione" required>                            
                        </select>
                    </fieldset>
                    <div class="form-group">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-rounded btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" name="action" id="#" value="add" class="btn btn-rounded btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>