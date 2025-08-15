<div class="modal fade bd-example-modal-lg"
    id="modalnuevarespuesta"
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
            <form method="post" id="answer_form">
                <div class="modal-body">

                    <input type="hidden" id="answer_id" name="answer_id">
                    <div class="form-group">
                        <label class="form-label semibold" for="answer_nom">Nombre</label>
                        <input type="text" class="form-control" id="answer_nom" name="answer_nom" placeholder="Ingrese un nombre" required>
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <input type="checkbox" id="es_error_proceso" name="es_error_proceso">
                            <label for="es_error_proceso">Es error de proceso</label>
                        </div>
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