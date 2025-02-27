<!-- Edit Modal -->
<div class="modal fade" id="modal-estimator-edit" tabindex="-1" role="dialog" aria-labelledby="modal-estimator-edit-label">
  <div class="modal-dialog" role="document">
    <div class="modal-content panel-primary">
      <div class="modal-header panel-heading">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modal-estimator-edit-label">Edit Project</h4>
      </div>
      <div class="modal-body" id="estimator-edit-body">
        <div style="padding: 3px;"><strong>Community:</strong> <span id="project-name">Community Name Goes Here</span></div>
        <div style="padding: 3px;"><strong>Crossroads:</strong> <span id="project-crossroads">Community Cross Roads Goes Here</span></div>
        <div style="padding: 3px;"><strong>Notes:<br/></strong> <span id="project-note">Notes Goes Here</span></div>

        <br />
        <form id="form-estimator-edit" name="form_estimator_edit">
          <input type="hidden" name="item_id" id="item-id" value="0">
          <input type="hidden" name="company_id" id="company-id" value="0">
          <input type="hidden" name="user_name" id="user-name" value="<?=$data['user']['user_name'];?>">
          <table class="table table-striped table-responsive" id="estimator-data-table">
            <tbody>
            <tr>
              <td><strong>Price Increase:</strong></td>
              <td><select name="price_increase" id="price-increase">
                  <option value="0"></option>
                  <option value="2">Pending</option>
                  <option value="1">Completed</option>
                </select></td>
            </tr>
            </tbody>
          </table>
          <div style="padding: 3px;">Created: <span id="created-on" style="font-weight: bold;">Created On Goes Here</span></div>
          <div style="padding: 3px;">Created By: <span id="created-by" style="font-weight: bold;">Created By Goes Here</span></div>
          <div style="padding: 3px;">Modified: <span id="modified-on" style="font-weight: bold;">Modified On Goes Here</span></div>
          <div style="padding: 3px;">Modified By: <span id="modified-by" style="font-weight: bold;">Modified By Goes Here</span></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="save-estimator-data" class="btn btn-primary" data-dismiss="modal" onclick="saveEstimatorReportCardData();">Save</button>
        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>