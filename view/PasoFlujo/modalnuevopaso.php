<div class="modal fade bd-example-modal-lg"
    id="modalnuevopaso"
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
            <form method="post" id="paso_form">
                <div class="modal-body">

                    <input type="hidden" id="paso_id" name="paso_id">
                    <input type="hidden" id="flujo_id" name="flujo_id">
                    <div class="form-group">
                        <label class="form-label semibold" for="paso_orden">Numero del paso</label>
                        <input type="number" class="form-control" id="paso_orden" name="paso_orden" placeholder="Ingrese el numero del paso" required>
                        <div></div>
                    </div>
                    <fieldset class="form-group">
                        <label class="form-label semibold" for="paso_nombre">Nombre del paso</label>
                        <input type="text" class="form-control" id="paso_nombre" name="paso_nombre" placeholder="Ingrese el nombre del paso" required>
                        <div></div>
                    </fieldset>
                    <fieldset class="form-group">
                        <label class="form-label semibold" for="usu_asig">Usuario asigando</label>
                        <select class="form-control" id="usu_asig" name="usu_asig" placeholder="Seleccione una categoria" required>
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