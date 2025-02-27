<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'bids_data.js') : '');?>
<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'modals.js') : '');?>

<div class="main">
    <h2 class="page-header">Bids</h2>
    <div class="panel">
        <div class="panel-body">
            <div class="col-md-10">
                <div class="pull-left">
                    <form class="form-inline" method="post" name="form_bids_data" id="form-bids-data">
                      <div class="form-group">
                        <label for="date-type">Date Type: </label>
                        <select name="date_type" id="date-type" onchange="loadBidsData();">
                          <option value="due" selected>Due Date</option>
                          <option value="sent">Sent Date</option>
                          <option value="award">Award Date</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="date-start">Start Date: </label>
                        <input class="form-control" type="text" name="date_start" id="date-start" value="<?=date('m/d/Y', strtotime('first day of this month'))?>" size="10" date-format="mm/dd/yyyy" onchange="loadBidsData();" />
                      </div>
                      <div class="form-group">
                        <label for="date-end">Start Date: </label>
                        <input class="form-control" type="text" name="date_end" id="date-end" value="<?=date('m/d/Y', strtotime('last day of next month'))?>" size="10" date-format="mm/dd/yyyy" onchange="loadBidsData();" />
                      </div>
                      <div class="form-group">
                        <label for="search-type">Search for: </label>
                        <select name="search_type" id="search-type" onchange="loadBidsData();">
                          <option value="" selected>All</option>
                          <option value="missing">Missing Bids</option>
                          <option value="awarded">Awarded</option>
                          <option value="declined">Declined</option>
                          <option value="sent">Bids Sent</option>
                          <option value="notsent">Bids Not Sent</option>
                        </select>
                      </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <br />
    <form>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-lg-12">
                <div class="loader">
                    <img src="/assets/images/main/wheel.svg" alt="Loading..."> <span style="font-weight: bold">Loading. Please wait...</span>
                </div>
                <div style="float: left;">
                  <button type="button" class="btn btn-primary" onclick="showBidsModal('new', 'edit');">Create New Bid</button>
                </div>
                <div style="float: right;">
                  <button type="button" class="btn btn-sm btn-info" onclick="bidInfoAll('show');">Show All Bid Info</button>
                  <button type="button" class="btn btn-sm btn-info" onclick="bidInfoAll('hide')">Hide All Bid Info</button>
                </div>
                <div style="clear: both; padding: 5px;"></div>
                <table id="bids-data" class="table table-striped table-hover table-condensed data-table hidden">
                    <thead>
                    <tr>
                        <th>Bid#</th>
                        <th>Customer</th>
                        <th>Project</th>
                        <th>Series</th>
                        <th>City</th>
                        <th>Area</th>
                        <th>#Lots</th>
                        <th>Date Due</th>
                        <th>Date Sent</th>
                        <th>Date Award</th>
                        <th>Bids</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

<?php //include 'modals/add.php'; ?>
<?php include 'modals/edit.php'; ?>
