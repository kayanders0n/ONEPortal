<?= (!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'inventory_data.js') : ''); ?>
<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'modals.js') : '');?>
<div class="main">
  <div style="float: right;">
    <label for="inventory-company-id">Company: </label>
    <select name="inventory_company_id" id="inventory-company-id" onchange="loadInventoryPOs();">
      <option value="<?= PLUMBING_ENTITYID ?>">Plumbing</option>
      <option value="<?= CONCRETE_ENTITYID ?>">Concrete</option>
      <option value="<?= FRAMING_ENTITYID ?>">Framing</option>
      <option value="<?= DOORTRIM_ENTITYID ?>">Door and Trim</option>
    </select>
    <label for="inventory-po-id">Inventory P/O: </label>
    <select name="inventory_po_id" id="inventory-po-id" onchange="loadInventoryPOItems();">
      <option value="0">-- Select P/O --</option>
    </select>
  </div>
  <h2 class="page-header">Inventory</h2>
  <div class="panel">
    <div class="panel-body">
      <div class="col-md-10">
        <div class="pull-left">
          <form class="form-inline" method="post" action="" name="form_inventory_material" id="form-inventory-material">
            <input type="hidden" name="user_name" id="user-name" value="<?=$data['user']['user_name'];?>">
            <input type="hidden" name="material_id" id="material-id" value="0">
            <div class="form-group">
              <label for="material-code">Part#: </label>
              <input type="text" class="form-control" name="material_code" id="material-code" value=""/>
              <label for="material-upc">UPC: </label>
              <input type="text" class="form-control" name="material_upc" id="material-upc" value=""/>
              <button class="btn btn-primary" type="submit" class="form-control"
                      onclick="findMaterial(); return false;">Find Material
              </button>
              <button class="btn btn-warning" type="button" class="form-control"
                      onclick="clearMaterial(); return false;">Clear
              </button>
            </div>
            <div style="clear:both; padding:10px;"></div>
            <div class="form-group">
              <span id="material-name"></span>
              <div id="inventory-count-entry" style="padding: 10px; display: none;">
                <label for="inventory-count">Units On Hand: </label>
                <input type="number" name="inventory_count" id="inventory-count" size="10" value="0">
                <label for="inventory-location">Location: </label>
                <input type="text" name="inventory_location" id="inventory-location" size="15" value="">
                <label for="inventory-add-descript">Description: </label>
                <input type="text" name="inventory_add_descript" id="inventory-add-descript" size="25" value="">
                <p style="padding: 5px;">
                  <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryCount(0);">0
                  </button>
                  <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryCount(1);">1
                  </button>
                  <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryCount(2);">2
                  </button>
                  <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryCount(3);">3
                  </button>
                  <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryCount(4);">4
                  </button>
                  <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryCount(5);">5
                  </button>
                  <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryCount(6);">6
                  </button>
                  <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryCount(7);">7
                  </button>
                  <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryCount(8);">8
                  </button>
                  <button type="button" class="btn btn-lg btn-outline-primary" onclick="addInventoryCount(9);">9
                  </button>
                </p>
                <p style="padding: 5px;">
                  <button type="button" class="btn btn-lg btn-warning" onclick="addInventoryCount(-1);">CLEAR</button>
                  <button type="button" class="btn btn-lg btn-primary" onclick="addInventory();">SUBMIT</button>
                </p>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <form>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-lg-12">
        <div class="loader" style="display: none;">
          <img src="/assets/images/main/wheel.svg" alt="Loading..."> <span style="font-weight: bold;">Loading. Please wait...</span>
        </div>
        <table id="inventory-data" class="table table-striped table-hover table-condensed data-table hidden">
          <thead>
          <tr>
            <th>#</th>
            <th>Description</th>
            <th class="text-right">Units</th>
            <th>Location</th>
            <th>Add. Description</th>
          </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </form>
</div>
<?php include 'modals/edit.php'; ?>
