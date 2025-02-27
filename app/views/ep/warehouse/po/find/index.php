<div class="main">
    <h2 class="page-header">Find Purchase Order</h2>
    <div class="panel">
        <div class="panel-body">
            <div class="col-md-10">
                <div class="pull-left">
                    <?php if (isset($data['params']['error'])) { echo '<h3 style="color: red;">', $data['params']['error'], '</h3>'; }  ?>
                    <form class="form-inline" method="post" action="/warehouse/po/lookup" name="form_po_find" id="form-po-find">
                      <div class="form-group">
                          <label for="po-num">PO#: </label>
                          <input type="number" class="form-control" name="po_num" id="po-num" value="" />
                      </div>
                      <div style="clear:both; padding:10px;"></div>
                      <div class="form-group">
                          <button class="btn btn-primary" type="submit" >Find Purchase Order</button>
                      </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
