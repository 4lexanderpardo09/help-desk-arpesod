<div class="modal fade bd-example-modal-lg"
    id="modalnuevaregional"
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
            <form method="post" id="regional_form">
                <div class="modal-body">

                    <input type="hidden" id="reg_id" name="reg_id">
                    <div class="form-group">
                        <label class="form-label semibold" for="reg_nom">Nombre</label>
                        <input type="text" class="form-control" id="reg_nom" name="reg_nom" placeholder="Ingrese un nombre" required>
                        <div></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-rounded btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" name="action" id="#" value="add" class="btn btn-rounded btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>