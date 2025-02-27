<!-- Edit Modal -->
<div class="modal fade" id="modal-job-ticket-edit" tabindex="-1" role="dialog" aria-labelledby="modal-job-ticket-edit-label">
  <div class="modal-dialog" role="document">
    <div class="modal-content panel-primary">
      <div class="modal-header panel-heading">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modal-job-ticket-edit-label">Ticket</h4>
      </div>
      <div class="modal-body" id="job-ticket-edit-body">
        <div style="padding: 3px;"><strong>Ticket #:</strong> <span id="table-id">Ticket # Goes Here</span></div>
        <div style="padding: 3px;"><strong>Date:</strong> <span id="start-date">Date Goes Here</span></div>
        <div style="padding: 3px;"><strong>Submitted: </strong> <span id="submit-by">Submitted By Goes Here</span></div>
        <div style="padding: 3px;"><strong>Assigned: </strong> <span id="assign-to">Assigned To Goes Here</span></div>
        <div style="padding: 3px;"><strong>Requested: </strong> <span id="ticket-comment">Requested By Goes Here</span></div>
        <div style="padding: 3px;"><strong>Completed: </strong> <span id="finish-date">Completed Date Goes Here</span></div>
        <div style="padding: 3px;"><strong>Type of Work: </strong> <span id="ticket-type">Type of Work Goes Here</span></div>
        <div style="padding: 3px;"><strong>WORK TO BE DONE:<br/></strong> <span id="ticket-name">Description Goes Here</span><br/><br/><span id="ticket-note">Note Goes Here</span></div>
        <form name="add_note_form" id="add-note-form" method="post" action="">
          <h4>Add Note:</h4>
          <input type="hidden" id="item-id" name="item_id" value=""/>
          <input type="hidden" name="user_name" id="user-name" value="<?= $data['user']['user_name']; ?>">
          <textarea class="form-control" name="add_note" id="add-note" cols="50" rows="3"></textarea>
          <button class="btn btn-primary" type="button" style="margin-top: 5px;" onclick="updateTicketNote(); return false;">Add Note</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>