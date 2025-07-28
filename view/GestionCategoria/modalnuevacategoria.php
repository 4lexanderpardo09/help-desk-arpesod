<div class="modal fade bd-example-modal-lg"
    id="modalnuevacategoria"
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
            <form method="post" id="cat_form">
                <div class="modal-body">

                    <input type="hidden" id="cat_id" name="cat_id">
                    <div class="form-group">
                        <label class="form-label semibold" for="cat_nom">Nombre</label>
                        <input type="text" class="form-control" id="cat_nom" name="cat_nom" placeholder="Ingrese un nombre" required>
                        <div></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label semibold" for="dp_id">Departamento</label>
                        <select id="dp_ids" name="dp_ids[]" class="select2" multiple="multiple" style="width: 100%;">
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label semibold" for="emp_id">Empresa</label>
                        <select id="emp_ids" name="emp_ids[]" class="select2" multiple="multiple" style="width: 100%;">
                        </select>
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