<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'po_data.js') : '');?>
<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'po_details.js') : '');?>

<div class="main">
    <h2 class="page-header">Purchase Order Info - <?=$data['page']['po_num']?></h2>
    <div class="panel">
        <div class="panel-body">
            <div class="col-md-4">
                <form>
                    <input type="hidden" id="po-id" name="po_id" value="<?=$data['page']['po_id']?>" />
                    <input type="hidden" id="po-company-id" name="po_company_id" value="0" />
                </form>
                <table class="table table-striped table-responsive table-condensed table-hover no-margin">
                    <tr>
                        <td class="text-nowrap"><strong>Vendor:</strong></td>
                        <td><span id="vendor-name">Vendor Name</span></td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Description:</strong></td>
                        <td><span id="po-name">Purchase Order Name</span></td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Type:</strong></td>
                        <td><span id="po-type">PO Type</span></td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Status:</strong></td>
                        <td><span id="po-status">PO Status</span></td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Comment:</strong></td>
                        <td><span id="po-comment">PO Comment</span></td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>P/O Date:</strong></td>
                        <td><span id="po-date">P/O Date</span></td>
                    </tr>
                    <tr>
                      <td class="text-nowrap"><strong>Shipped To:</strong></td>
                      <td><span id="po-ship-to">Ship to Location</span></td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Created By:</strong></td>
                        <td><span id="created_by">Created By Name</span></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
              <div class="panel panel-success">
                <div class="panel-heading">
                  <h4 class="panel-title">
                    <span><i class="fa fa-truck fa-lg fa-fw"></i> Job Info</span>
                  </h4>
                </div>
                <table class="table table-striped table-responsive table-condensed table-hover no-margin">
                  <tr>
                    <td class="text-nowrap"><strong>Job#:</strong></td>
                    <td><span id="job-num">Job Number</span></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong>Community:</strong></td>
                    <td><span id="job-name">Community Name</span></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong>Lot#:</strong></td>
                    <td><span id="job-lot-num">Job Lot#</span></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong>Address:</strong></td>
                    <td><span id="job-address-link">Job Address</span></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong>Builder:</strong></td>
                    <td><span id="job-builder-name">Builder Name</span></td>
                  </tr>
                </table>
              </div>
            </div>
            <div class="col-md-4">
              <div class="panel panel-success">
                <div class="panel-heading">
                  <h4 class="panel-title">
                    <span><i class="fa fa-sticky-note fa-lg fa-fw"></i> Notes</span>
                  </h4>
                </div>
                <div style="padding: 10px;">
                  <div id="po-note"></div>
                  <form name="add_note_form" id="add-note-form" method="post" action="">
                    <h4>Add Note:</h4>
                    <input type="hidden" id="po-id" name="po_id" value="<?=$data['page']['po_id']?>" />
                    <input type="hidden" name="user_name" id="user-name" value="<?=$data['user']['user_name'];?>">
                    <textarea class="form-control" name="add_note" id="add-note" cols="50" rows="3"></textarea>
                    <button class="btn btn-primary" style="margin-top: 5px;" onclick="addPurchaseOrderNote(); return false;">Add Note</button>
                  </form>
                </div>
              </div>

              <div class="panel panel-warning">
                <div class="panel-heading">
                  <h4 class="panel-title">
                    <span><i class="fa fa-print fa-lg fa-fw"></i> Print</span>
                    <button class="btn btn-primary"  style="float:right;" onclick="printPurchaseOrder(); return false;">Print P/O</button>
                    <div style="clear: both;"></div>
                  </h4>
                </div>
                <div style="padding: 10px;">
                  <form name="print_po_form" id="print-po-form" method="post" action="">
                    <input type="hidden" id="po-id" name="po_id" value="<?=$data['page']['po_id']?>" />
                    <input type="hidden" id="po-num" name="po_num" value="<?=$data['page']['po_num']?>" />
                    <input type="hidden" name="user_employee_id" id="user-employee-id" value="<?=$data['user']['seq_id'];?>">
                    <input type="hidden" name="user_site" id="user-site" value="<?=$data['user']['site'];?>">
                    <input type="hidden" name="user_name" id="user-name" value="<?=$data['user']['user_name'];?>">
                    <div class="form-check">
                      <input type="radio" name="override_location" id="default_override_location" class="form-check-input" value="" checked>
                      <label class="form-check-label" for="default_override_location">Default</label>
                      <input type="radio" name="override_location" id="warner_override_location" class="form-check-input" value="40">
                      <label class="form-check-label" for="warner_override_location">Warner</label>
                      <input type="radio" name="override_location" id="buckeye_override_location" class="form-check-input" value="30">
                      <label class="form-check-label" for="buckeye_override_location">Buckeye</label>
                      <input type="radio" name="override_location" id="tucson_override_location" class="form-check-input" value="20">
                      <label class="form-check-label" for="tucson_override_location">Tucson</label>
                      <input type="radio" name="override_location" id="corporate_override_location" class="form-check-input" value="10">
                      <label class="form-check-label" for="corporate_override_location">Corporate</label>
                    </div>

                  </form>
                </div>
              </div>

            </div>

            <div style="clear:both;">&nbsp;</div>
            <div class="class="col-md-12 col-sm-12 col-lg-12"">
                <div style="margin-top: 15px;"><h3>Items</h3></div>
                <div class="loader">
                  <img src="/assets/images/main/wheel.svg" alt="Loading..."> <span style="font-weight: bold">Loading. Please wait...</span>
                </div>
                <table id="po-item-data" class="table table-striped table-hover table-condensed data-table hidden">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Description</th>
                      <th>Job#</th>
                      <th>Add. Description</th>
                      <th>Location</th>
                      <th class="text-right">Units</th>
                      <th class="text-right">Received</th>
                    </tr>
                  </thead>
                  <tbody>

                  </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
