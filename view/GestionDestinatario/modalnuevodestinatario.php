<div class="modal fade bd-example-modal-lg"
    id="modalnuevodestinatario"
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
            <form method="post" id="dest_form">
                <div class="modal-body">

                    <input type="hidden" id="dest_id" name="dest_id">
                    <fieldset class="form-group">
                        <label class="form-label semibold" for="answer_id">Respuesta</label>
                        <select class="form-control" id="answer_id" name="answer_id" placeholder="Seleccione una categoria">
                        </select>
                    </fieldset>
                    <fieldset class="form-group">
                        <label class="form-label semibold" for="usu_id">Destinatario</label>
                        <select class="form-control" id="usu_id" name="usu_id" placeholder="Seleccione una categoria">
                        </select>
                    </fieldset>
                    <fieldset class="form-group">
                        <label class="form-label semibold" for="dp_id">Departamento</label>
                        <select class="form-control" id="dp_id" name="dp_id" placeholder="Seleccione una categoria">
                        </select>
                    </fieldset>
                    <fieldset class="form-group">
                        <label class="form-label semibold" for="cat_id">Categoria</label>
                        <select class="form-control" id="cat_id" name="cat_id" placeholder="Seleccione una categoria">
                        </select>
                    </fieldset>
                    <fieldset class="form-group">
                        <label class="form-label semibold" for="cats_id">Subcategoria</label>
                        <select class="form-control" id="cats_id" name="cats_id" placeholder="Seleccione una categoria">
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