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
                    <input type="hidden" id="regla_id" name="regla_id">
                    <fieldset class="form-group">
                        <label class="form-label" for="cat_id">Categoria</label>
                        <select class="form-control" id="cat_id" name="cat_id" required>
                        </select>
                    </fieldset>
                    <fieldset class="form-group">
                        <label for="cats_id"><b>Si</b> un ticket es de la subcategoría:</label>
                        <select class="form-control" id="cats_id" name="cats_id" required>
                        </select>
                    </fieldset>
                    <fieldset class="form-group">
                        <label for="creador_per_id"><b>Y/O</b> es creado por alguien con el perfil:</label>
                        <select id="creador_per_ids" name="creador_per_ids[]" class="select2" multiple="multiple" style="width: 100%;">
                        </select>
                    </fieldset>
                    <fieldset class="form-group">
                        <label for="creador_car_id"><b>Y</b> es creado por alguien con el cargo:</label>
                        <div>
                            <button type="button" class="btn btn-sm btn-primary" id="select_all_creador">Seleccionar Todos</button>
                            <button type="button" class="btn btn-sm btn-secondary" id="deselect_all_creador">Deseleccionar Todos</button>
                        </div>
                        <select id="creador_car_ids" name="creador_car_ids[]" class="select2" multiple="multiple" style="width: 100%;">
                        </select>
                    </fieldset>
                    <fieldset class="form-group">
                        <label for="asignado_car_id"><b>Entonces</b> asignar a alguien con el cargo:</label>
                        <div>
                            <button type="button" class="btn btn-sm btn-primary" id="select_all_asignado">Seleccionar Todos</button>
                            <button type="button" class="btn btn-sm btn-secondary" id="deselect_all_asignado">Deseleccionar Todos</button>
                        </div>
                        <select id="asignado_car_ids" name="asignado_car_ids[]" class="select2" multiple="multiple" style="width: 100%;">
                        </select>
                    </fieldset>
                    <div class="form-group">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-rounded btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="submit" name="action" id="#" value="add" class="btn btn-rounded btn-primary">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade"
    id="modalCargueMasivo"
    tabindex="-1"
    role="dialog"
    aria-labelledby="cargueMasivoLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="../../cargues/cargueflujomapeo.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="sheet_name" value="FlujoMapeo">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="modal-close" data-dismiss="modal" aria-label="Close">
                        <i class="font-icon-close-2"></i>
                    </button>
                    <h4 class="modal-title" id="cargueMasivoLabel">Cargue Masivo de Mapeo de Flujos</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="archivo_mapeo">Seleccionar Archivo Excel</label>
                        <p class="form-text text-muted">El archivo debe tener las columnas: SUBCATEGORIA, CARGOS_CREADORES, CARGOS_ASIGNADOS.</p>
                        <input type="file" name="archivo_mapeo" id="archivo_mapeo" class="form-control" accept=".xlsx, .xls" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-rounded btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-rounded btn-primary">Subir Archivo</button>
                </div>
            </div>
        </form>
    </div>
</div>