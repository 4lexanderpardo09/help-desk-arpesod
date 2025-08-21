<div class="modal fade" id="modalorganigrama">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="modal-close" data-dismiss="modal" aria-label="Close">
					<i class="font-icon-close-2"></i>
				</button>
				<h4 class="modal-title" id="mdltitulo"></h4>
			</div>
			<form method="post" id="organigrama_form">
				<div class="modal-body">
					<input type="hidden" id="org_id" name="org_id">

					<div class="form-group">
						<label class="form-label" for="car_id">Cargo</label>
						<select class="select2" id="car_id" name="car_id" data-placeholder="Seleccionar" style="width: 100%;">
						</select>
					</div>

                    <div class="form-group">
						<label class="form-label" for="jefe_car_id">Reporta a (Jefe)</label>
						<select class="select2" id="jefe_car_id" name="jefe_car_id" data-placeholder="Seleccionar" style="width: 100%;">
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