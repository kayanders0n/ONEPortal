<!-- Edit Modal -->
<div class="modal fade" id="modal-twwvr-edit" tabindex="-1" role="dialog" aria-labelledby="modal-twwvr-edit-label">
  <div class="modal-dialog" role="document">
    <div class="modal-content panel-primary">
      <div class="modal-header panel-heading">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modal-twwvr-edit-label">Edit Visual Record Item</h4>
      </div>
      <div class="modal-body" id="twwvr-edit-body">
        <div style="padding: 3px;"><strong>Activity Date:</strong> <span id="activity-date">Activity Date Goes Here</span></div>
        <div style="padding: 3px;"><strong>Type:</strong> <span id="record-type">Record Type Goes Here</span></div>
        <div style="padding: 3px;"><strong>Employee:</strong> <span id="employee">Employee Goes Here</span></div>
        <div style="padding: 3px;"><strong>Description:</strong> <span id="description">Description Goes Here</span></div>
        <div style="padding: 3px;"><strong>Recorded On:</strong> <span id="recorded-on">Recorded On Goes Here</span></div>
        <div style="padding: 3px;"><strong>Network:</strong> <span id="network">Network Goes Here</span></div>
        <br />
        <form id="form-twwvr-edit" name="form_twwvr_edit">
          <input type="hidden" name="item_id" id="item-id" value="0">
          <input type="hidden" name="user_name" id="user-name" value="<?=$data['user']['user_name'];?>">
          <table class="table table-striped table-responsive" id="twwvr-data-table">
            <tbody>
            <tr>
              <td><strong>Ticket#:</strong></td>
              <td><input type="text" name="task_num" id="task-num" value="" size="10"></td>
            </tr>
            <tr>
              <td><strong>Job#:</strong></td>
              <td><input type="text" name="job_num" id="job-num" value="" size="10"></td>
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
        <button type="button" id="delete-twwvr-data" style="float: left;" class="btn btn-danger" data-dismiss="modal" onclick="deleteTWWVRData('edit');">Delete</button>
        <button type="button" id="save-twwvr-data" class="btn btn-primary" data-dismiss="modal" onclick="saveTWWVRData();">Save</button>
        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>