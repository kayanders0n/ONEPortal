<!-- Edit Modal -->
<div class="modal fade" id="modal-inventory-po-edit" tabindex="-1" role="dialog" aria-labelledby="modal-inventory-po-edit-label">
  <div class="modal-dialog" role="document">
    <div class="modal-content panel-primary">
      <div class="modal-header panel-heading">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modal-inventory-po-edit-label">Edit Inventory P/O Item</h4>
      </div>
      <div class="modal-body" id="inventory-po-edit-body">
        <div style="padding: 3px;"><strong>Takeoff#:</strong> <span id="material-code">Takeoff# Goes Here</span></div>
        <div style="padding: 3px;"><strong>Takeoff Name:</strong> <span id="material-name">Takeoff Name Goes Here</span></div>
        <br />
        <form id="form-inventory-po-edit" name="form_inventory_po_edit">
          <input type="hidden" name="item_id" id="item-id" value="0">
          <input type="hidden" name="user_name" id="user-name" value="<?=$data['user']['user_name'];?>">
          <table class="table table-striped table-responsive" id="inventory-po-data-table">
            <tbody>
            <tr>
              <td><strong>Units on Hand:</strong></td>
              <td><input type="number" name="inventory_count" id="inventory-count" size="10" value=""></td>
            </tr>
            <tr>
              <td><strong>Location:</strong></td>
              <td><input type="text" name="location" id="location" value="" size="15"></td>
            </tr>
            <tr>
              <td><strong>Description:</strong></td>
              <td><input type="text" name="add_descript" id="add-descript" value="" size="25"></td>
            </tr>
            <tr>
              <td colspan="99">
                <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryPODataCount(0);">0</button>
                <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryPODataCount(1);">1</button>
                <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryPODataCount(2);">2</button>
                <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryPODataCount(3);">3</button>
                <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryPODataCount(4);">4</button>
                <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryPODataCount(5);">5</button>
                <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryPODataCount(6);">6</button>
                <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryPODataCount(7);">7</button>
                <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryPODataCount(8);">8</button>
                <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryPODataCount(9);">9</button>
                <button type="button" class="btn btn-lg btn-warning" onclick="addInventoryPODataCount(-1);">CLEAR</button>
              </td>
            </tr>
            </tbody>
          </table>
          <div style="padding: 3px;">Created: <span id="created-on" style="font-weight: bold;">Created On Goes Here</span></div>
          <div style="padding: 3px;">Modified: <span id="modified-on" style="font-weight: bold;">Modified On Goes Here</span></div>
          <div style="padding: 3px;">Modified By: <span id="modified-by" style="font-weight: bold;">Modified By Goes Here</span></div>
        </form>
      </div>
      <div style="clear: both;"></div>
      <div class="modal-footer">
        <button type="button" id="delete-inventory-po-data" style="float: left;" class="btn btn-danger" data-dismiss="modal" onclick="deleteInventoryPOData('edit');">Delete</button>
        <button type="button" id="save-twwvr-data" class="btn btn-primary" data-dismiss="modal" onclick="saveInventoryPOData();">Save</button>
        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>