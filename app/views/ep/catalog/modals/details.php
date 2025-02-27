<!-- Details Modal -->
<div class="modal fade" id="modal-catalog-details" tabindex="-1" role="dialog" aria-labelledby="modal-catalog-details-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modal-catalog-details-label">View Product Details</h4>
            </div>
            <div class="modal-body" id="catalog-details-body">
                <div style="padding: 3px;"><strong>Takeoff#:</strong> <span id="material-code">Takeoff# Goes Here</span></div>
                <div style="padding: 3px;"><strong>Description:</strong> <span id="material-name">Takeoff Name Goes Here</span></div>
                <div style="padding: 3px;"><strong>Product Line:</strong> <span id="category-name">Product Line Name Goes Here</span></div>
                <br />
                <form id="form-catalog-details" name="form_catalog_details" action="" onsubmit="return false;">
                    <input type="hidden" name="item_id" id="item-id" value="0">
                    <input type="hidden" name="material_upc" id="material-upc" value="">
                    <input type="hidden" name="user_name" id="user-name" value="<?=$data['user']['user_name'];?>">
                    <table class="table table-striped table-responsive" id="catalog-upc-table">
                      <thead>
                      <tr>
                        <th>UPC</th>
                      </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  <label for="add-material-upc">Add UPC: </label>
                  <input type="text" class="form-control" name="add_material_upc" id="add-material-upc" value="" tabindex="0" autofocus/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="validateUPCDataAndSave()">Save</button>
                <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>