<div class="modal fade bd-example-modal-lg"
    id="modalnuevoflujo"
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
            <form method="post" id="flujo_form">
                <div class="modal-body">
                    
                    <input type="hidden" id="flujo_id" name="flujo_id">
                    <div class="form-group">
                        <label class="form-label semibold" for="flujo_nom">Nombre</label>
                        <input type="text" class="form-control" id="flujo_nom" name="flujo_nom" placeholder="Ingrese un nombre" required>
                        <div></div>
                    </div>
                    <fieldset class="form-group">
                        <label class="form-label semibold" for="emp_id">Empresa</label>
                        <select class="form-control" id="emp_id" name="emp_id" placeholder="Seleccione una categoria" required>
                        </select>
                    </fieldset>
                    <fieldset class="form-group">
                        <label class="form-label semibold" for="dp_id">Departamento</label>
                        <select class="form-control" id="dp_id" name="dp_id" placeholder="Seleccione una categoria" required>
                        </select>
                    </fieldset>
                    <fieldset class="form-group">
                        <label class="form-label semibold" for="cat_id">Categoria</label>
                        <select class="form-control" id="cat_id" name="cat_id" placeholder="Seleccione una categoria" required>
                        </select>
                    </fieldset>
                    <fieldset class="form-group">
                        <label class="form-label semibold" for="cats_id">Subcategoria</label>
                        <select class="form-control" id="cats_id" name="cats_id" placeholder="Seleccione una categoria" required>
                        </select>
                    </fieldset>
                    <div class="checkbox">
                            <input type="checkbox" id="requiere_aprobacion_jefe" name="requiere_aprobacion_jefe" value="1">
                            <label for="requiere_aprobacion_jefe">Requiere Aprobación del Jefe para iniciar el flujo</label>
                        </div>
                    </div>
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