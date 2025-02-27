<!-- Edit Modal -->
<div class="modal fade" id="modal-hyphen-edit" tabindex="-1" role="dialog" aria-labelledby="modal-hyphen-edit-label">
  <div class="modal-dialog" role="document">
    <div class="modal-content panel-primary">
      <div class="modal-header panel-heading">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modal-hyphen-edit-label">Edit Hyphen Order</h4>
      </div>
      <div class="modal-body" id="hyphen-edit-body">
        <div style="padding: 3px;"><strong>Action:</strong> <span id="action-name">Action Name Goes Here</span></div>
        <div style="padding: 3px;"><strong>Address:</strong> <span id="jobsite-address">Jobsite Address Goes Here</span></div>
        <br />
        <form id="form-hyphen-edit" name="form_hyphen_edit">
          <input type="hidden" name="item_id" id="item-id" value="0">
          <input type="hidden" name="user_name" id="user-name" value="<?=$data['user']['user_name'];?>">
          <table class="table table-striped table-responsive" id="hyphen-data-table">
            <tbody>
            <tr>
              <td><strong>Account Code:</strong></td>
              <td><input type="text" name="account_code" id="account-code" value="" size="10" readonly></td>
            </tr>
            <tr>
              <td><strong>Community Code:</strong></td>
              <td><input type="text" name="project_code" id="project-code" value="" size="10"></td>
            </tr>
            <tr>
              <td><strong>Lot#:</strong></td>
              <td><input type="text" name="jobsite_lotnum" id="jobsite-lotnum" value="" size="10"></td>
            </tr>
            </tbody>
          </table>
          <div style="padding: 3px;">Created: <span id="created-on" style="font-weight: bold;">Created On Goes Here</span></div>
          <div style="padding: 3px;">Modified: <span id="modified-on" style="font-weight: bold;">Modified On Goes Here</span></div>
          <div style="padding: 3px;">Modified By: <span id="modified-by" style="font-weight: bold;">Modified By Goes Here</span></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="delete-hyphen-data" style="float: left;" class="btn btn-warning" data-dismiss="modal" onclick="deleteHyphenData();">Delete</button>
        <button type="button" id="save-hyphen-data" class="btn btn-primary" data-dismiss="modal" onclick="saveHyphenData();">Save</button>
        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>