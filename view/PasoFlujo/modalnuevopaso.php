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
            <form method="post" id="paso_form" enctype="multipart/form-data">
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
                    <div class="form-group">
                        <label class="form-label" for="campo_id_referencia_jefe">Campo Plantilla para Jefe Inmediato (Opcional)</label>
                        <select class="form-control" id="campo_id_referencia_jefe" name="campo_id_referencia_jefe">
                            <option value="">-- Seleccione un Campo --</option>
                        </select>
                        <small class="text-muted">Si se selecciona "Jefe Inmediato" en asignaciones o firmas, se usará el valor de este campo para determinar el subordinado.</small>
                    </div>

                    <div id="usuarios_especificos_container" style="display: none;">
                        <fieldset class="form-group">
                            <label class="form-label" for="usuarios_especificos">Usuarios Específicos</label>
                            <select class="select2" id="usuarios_especificos" name="usuarios_especificos[]" multiple="multiple" data-placeholder="Seleccione usuarios específicos">
                            </select>
                        </fieldset>
                        <fieldset class="form-group" style="margin-top: 1rem;">
                            <label class="form-label" for="cargos_especificos">Cargos Específicos (Para asignación por Regional)</label>
                            <select class="select2" id="cargos_especificos" name="cargos_especificos[]" multiple="multiple" data-placeholder="Seleccione cargos específicos">
                            </select>
                        </fieldset>
                    </div>
                    <div class="checkbox" style="margin-top: 1rem;">
                        <input type="checkbox" id="es_tarea_nacional" name="es_tarea_nacional" value="1">
                        <label for="es_tarea_nacional">¿Es una tarea nacional?</label>
                    </div>
                    <div class="checkbox" style="margin-top: 1rem;">
                        <input type="checkbox" id="es_aprobacion" name="es_aprobacion" value="1">
                        <label for="es_aprobacion">¿Es un paso de aprobación?</label>
                    </div>
                    <div class="checkbox" style="margin-top: 1rem;">
                        <input type="checkbox" id="permite_cerrar" name="permite_cerrar" value="1">
                        <label for="permite_cerrar">¿Permite cerrar el ticket en este paso?</label>
                    </div>
                    <div class="checkbox" style="margin-top: 1rem;">
                        <input type="checkbox" id="necesita_aprobacion_jefe" name="necesita_aprobacion_jefe" value="1">
                        <label for="necesita_aprobacion_jefe">¿Necesita Aprobación Jefe Inmediato?</label>
                    </div>
                    <div class="checkbox" style="margin-top: 1rem;">
                        <input type="checkbox" id="es_paralelo" name="es_paralelo" value="1">
                        <label for="es_paralelo">¿Es un paso paralelo?</label>
                    </div>
                    <div class="checkbox" style="margin-top: 1rem;">
                        <input type="checkbox" id="requiere_firma" name="requiere_firma" value="1">
                        <label for="requiere_firma">¿Requiere Firma Digital?</label>
                    </div>

                    <div id="firma_config_container" style="display: none; margin-top: 1rem; border: 1px solid #ddd; padding: 10px;">
                        <h5>Configuración de Firmas</h5>
                        <p class="text-muted small">Si deja el usuario vacío, la firma aplicará para cualquiera que complete el paso (o el primero que lo haga).</p>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="tabla_firmas">
                                <thead>
                                    <tr>
                                        <th style="width: 20%;">Usuario (Opcional)</th>
                                        <th style="width: 20%;">Cargo (Opcional)</th>
                                        <th style="width: 10%;">Página</th>
                                        <th style="width: 20%;">Coord X</th>
                                        <th style="width: 20%;">Coord Y</th>
                                        <th style="width: 10%;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Rows added dynamically -->
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-sm btn-success" id="btn_add_firma"><i class="fa fa-plus"></i> Agregar Zona de Firma</button>
                        <input type="hidden" name="firma_config" id="firma_config">
                    </div>

                    <div class="checkbox" style="margin-top: 1rem;">
                        <input type="checkbox" id="requiere_campos_plantilla" name="requiere_campos_plantilla" value="1">
                        <label for="requiere_campos_plantilla">¿Requiere llenar campos de plantilla?</label>
                    </div>

                    <div id="campos_plantilla_container" style="display: none; margin-top: 1rem; border: 1px solid #ddd; padding: 10px;">
                        <h5>Configuración de Campos Dinámicos</h5>
                        <p class="text-muted small">Defina los campos que se deben llenar al iniciar el flujo.</p>
                        <table class="table table-bordered table-sm" id="tabla_campos_plantilla">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Etiqueta</th>
                                    <th style="width: 15%;">Código (Variable)</th>
                                    <th style="width: 15%;">Tipo</th>
                                    <th style="width: 10%;">Página</th>
                                    <th style="width: 15%;">Coord X</th>
                                    <th style="width: 15%;">Coord Y</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Rows added dynamically -->
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-success" id="btn_add_campo_plantilla"><i class="fa fa-plus"></i> Agregar Campo</button>
                        <input type="hidden" name="campos_plantilla_config" id="campos_plantilla_config">
                    </div>
                    <div class="form-group" style="margin-top: 1rem;">
                        <label class="form-label" for="paso_nom_adjunto">Archivo Adjunto</label>
                        <input type="file" class="form-control" id="paso_nom_adjunto" name="paso_nom_adjunto">
                        <input type="hidden" id="current_paso_nom_adjunto" name="current_paso_nom_adjunto">
                        <div id="paso_attachment_display" style="margin-top: 10px;"></div>
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
<div class="modal fade"
    id="modalCargueMasivo"
    tabindex="-1"
    role="dialog"
    aria-labelledby="cargueMasivoLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="../../cargues/carguepasosflujo.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="sheet_name" value="Pasosflujo">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="modal-close" data-dismiss="modal" aria-label="Close">
                        <i class="font-icon-close-2"></i>
                    </button>
                    <h4 class="modal-title" id="cargueMasivoLabel">Cargue Masivo de Subategorías</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="archivo_pasos">Seleccionar Archivo Excel</label>
                        <p class="form-text text-muted">El archivo debe tener las columnas: SUBCATEGORIA_ASOCIADA, ORDEN_PASO, NOMBRE_PASO, CARGO_ASIGNADO</p>
                        <input type="file" name="archivo_pasos" id="archivo_pasos" class="form-control" accept=".xlsx, .xls" required>
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