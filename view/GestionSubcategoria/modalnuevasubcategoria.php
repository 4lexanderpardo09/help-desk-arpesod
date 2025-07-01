<div class="modal fade bd-example-modal-lg"
    id="modalnuevasubcategoria"
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
            <form method="post" id="cats_form">
                <div class="modal-body">

                    <input type="hidden" id="cats_id" name="cats_id">
                    <fieldset class="form-group">
                        <label class="form-label semibold" for="cat_id">Categoria</label>
                        <select class="form-control" id="cat_id" name="cat_id" placeholder="Seleccione una categoria">
                        </select>
                    </fieldset>
                    <div>
                        <div class="form-group">
                            <label class="form-label semibold" for="cats_nom">Nombre</label>
                            <input type="text" class="form-control" id="cats_nom" name="cats_nom" placeholder="Ingrese un nombre" required>
                            <div></div>
                        </div>
                    </div>
                    <fieldset class="form-group semibold">
                        <label class="form-label" for="cats_descrip">Descripcion</label>
                        <div class="summernote-theme-1">
                            <textarea id="cats_descrip" name="cats_descrip" class="summernote" name="name"></textarea>
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