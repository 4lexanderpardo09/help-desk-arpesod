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
                        <label class="form-label" for="paso_orden">Numero del paso</label>
                        <input type="number" class="form-control" id="paso_orden" name="paso_orden" placeholder="Ingrese el numero del paso" required>
                        <div></div>
                    </div>
                    <fieldset class="form-group">
                        <label class="form-label" for="paso_nombre">Nombre del paso</label>
                        <input type="text" class="form-control" id="paso_nombre" name="paso_nombre" placeholder="Ingrese el nombre del paso" required>
                        <div></div>
                    </fieldset>
                    <fieldset class="form-group">
                        <label class="form-label" for="cargo_id_asignado">Cargo asigando</label>
                        <select class="form-control" id="cargo_id_asignado" name="cargo_id_asignado" placeholder="Seleccione una categoria" required>
                        </select>
                    </fieldset>
                    <fieldset>
                        <label class="form-label" for="paso_tiempo_habil">Tiempo de Resolución (Días Hábiles)</label>
                        <input type="number" class="form-control" id="paso_tiempo_habil" name="paso_tiempo_habil" value="1" required>
                    </fieldset>
                    <div class="checkbox" style="margin-top: 1rem;">
                        <input type="checkbox" id="requiere_seleccion_manual" name="requiere_seleccion_manual" value="1">
                        <label for="requiere_seleccion_manual">¿Requiere selección manual del anterior agente?</label>
                    </div>
                    <fieldset class="form-group semibold">
                        <label class="form-label" for="paso_descripcion">Descripción / Plantilla para el Agente</label>
                        <div class="summernote-theme-1">
                            <textarea id="paso_descripcion" name="paso_descripcion" class="summernote" name="name"></textarea>
                        </div>
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