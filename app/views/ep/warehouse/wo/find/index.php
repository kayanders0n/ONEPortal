<div class="main">
    <h2 class="page-header">Find Work Order</h2>
    <button id="barcode-stop-scanning" class="btn btn-danger" style="display:none; float: right; margin: 10px;" type="button" onclick="stopScanning();">Stop Scanning</button>
    <div class="panel">
        <div class="panel-body">
            <div class="col-md-10">
                <div class="pull-left">
                    <?php if (isset($data['params']['error'])) { echo '<h3 style="color: red;">', $data['params']['error'], '</h3>'; }  ?>
                    <form class="form-inline" method="post" action="/warehouse/wo/lookup" name="form_wo_find" id="form-wo-find">
                      <div class="form-group">
                          <label for="wo-num">WO#: </label>
                          <input type="number" class="form-control" name="wo_num" id="wo-num" value="" />
                      </div>
                      <button class="btn btn-warning" type="button" onclick="startScanning('wo-num');">Barcode</button>
                      <strong>- or -</strong>
                      <div style="clear:both; padding: 10px;"></div>
                      <div class="form-group">
                        <label for="job-num">Job#: </label>
                        <input type="number" class="form-control" name="job_num" id="job-num" value="" onblur="loadWorkOrderList();"/>
                      </div>
                      <button class="btn btn-warning" type="button" onclick="startScanning('job-num');">Barcode</button>
                      <div class="form-group">
                        <label for="work-order-list">Work Orders: </label>
                        <select class="form-control" name="work_order_list" id="work-order-list" onchange="setWorkOrderNum();">
                          <option value="0" selected>-- Select Work Order --</option>
                        </select>
                      </div>
                      <div style="clear:both; padding:10px;"></div>
                      <div class="form-group">
                          <button id="find-work-order" class="btn btn-primary" type="submit" >Find Work Order</button>
                      </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= config('assets'); ?>/js/html5-qrcode.min.js"></script>

<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'find.js') : '');?>
<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'barcode.js') : '');?>
