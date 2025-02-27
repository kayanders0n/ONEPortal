<!-- Edit Modal -->
<div class="modal fade" id="modal-bids-edit" tabindex="-1" role="dialog" aria-labelledby="modal-bids-edit-label">
  <div class="modal-dialog" role="document">
    <div class="modal-content panel-primary">
      <div class="modal-header panel-heading">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modal-bids-edit-label">Edit/New Title Goes Here</h4>
      </div>
      <div class="modal-body" id="bids-edit-body">
        <form id="form-bids-edit" name="form_bids_edit">
          <input type="hidden" name="item_id" id="item-id" value="0">
          <input type="hidden" name="user_name" id="user-name" value="<?=$data['user']['user_name'];?>">
          <table class="table table-striped table-responsive" id="bids-data-table">
            <tbody>
            <tr>
              <td><strong>Customer Name:</strong></td>
              <td><input type="text" id="customer-name" name="customer_name" size="50" value=""/></td>
            </tr>
            <tr>
              <td><strong>Project Name:</strong></td>
              <td><input type="text" id="project-name" name="project_name" size="50" value=""/></td>
            </tr>
            <tr>
              <td><strong>Project Series:</strong></td>
              <td><input type="text" id="project-series" name="project_series" value=""/></td>
            </tr>
            <tr>
              <td><strong>Project City:</strong></td>
              <td><input type="text" id="project-city" name="project_city" size="50" value=""/></td>
            </tr>
            <tr>
              <td><strong>Project Area:</strong></td>
              <td><select name="project_area" id="project-area">
                  <option value=""></option>
                  <option value="EAST">East</option>
                  <option value="WEST">West</option>
                  <option value="TUC">Tucson</option>
                </select>
              </td>
            </tr>
            <tr>
              <td><strong>Lot Count:</strong></td>
              <td><input type="number" id="lot-count" name="lot_count" value=""/></td>
            </tr>
            <tr>
              <td><strong>Date Due/Date Sent:</strong></td>
              <td><input type="text" id="bid-date-due" name="bid_date_due" value=""/>&nbsp;<input type="text" id="bid-date-sent" name="bid_date_sent" value=""/></td>
            </tr>
            <tr>
              <td><strong>Date Award:</strong></td>
              <td><input type="text" id="bid-date-award" name="bid_date_award" value=""/></td>
            </tr>
            <tr>
              <td><strong>Company:</strong></td>
              <td>
                <select name="company_id" id="company-id" onchange="bidCompanyChange();">
                  <option value="0">-- Select Company --</option>
                  <option value="<?=PLUMBING_ENTITYID?>" <?=(PLUMBING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Plumbing</option>
                  <option value="<?=CONCRETE_ENTITYID?>" <?=(CONCRETE_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Concrete</option>
                  <option value="<?=FRAMING_ENTITYID?>" <?=(FRAMING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Framing</option>
                  <option value="<?=DOORTRIM_ENTITYID?>" <?=(DOORTRIM_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Door and Trim</option>
                </select>
              </td>
            </tr>
            <tr>
              <td><strong>Profit Margin:</strong></td>
              <td><input type="text" id="bid-profit-margin" name="bid_profit_margin" value="" disabled/></td>
            </tr>
            <tr>
              <td><strong>Bid Status:</strong></td>
              <td>
                <select name="bid_status" id="bid-status" disabled>
                  <option value=""></option>
                  <option value="N/A">N/A</option>
                  <option value="DECLINED">Declined</option>
                  <option value="AWARDED">Awarded</option>
                </select>
              </td>
            </tr>
            </tbody>
          </table>
          <div id="bid-data-misc">
            <div style="padding: 3px;"><strong>Notes:<br/></strong> <span id="bid-note">Notes Goes Here</span></div>
            <br/>
            <div style="padding: 3px; float: left;">Created: <span id="created-on" style="font-weight: bold;">Created On Goes Here</span></div>
            <div style="padding: 3px; float: left;">Created By: <span id="created-by" style="font-weight: bold;">Created By Goes Here</span></div>
            <div style="clear: both;"></div>
            <div style="padding: 3px; float: left;">Modified: <span id="modified-on" style="font-weight: bold;">Modified On Goes Here</span></div>
            <div style="padding: 3px; float: left;">Modified By: <span id="modified-by" style="font-weight: bold;">Modified By Goes Here</span></div>
            <div style="clear: both;"></div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="save-bids-data" class="btn btn-primary" onclick="saveBidsData();">Save</button>
        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>