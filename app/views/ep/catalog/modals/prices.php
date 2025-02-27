<!-- Prices Modal -->

<style>
  .catalog-data-scrollbar {
    position: relative;
    height: 300px;
    overflow: auto;
    display: block;
  }
  .table-wrapper-scroll-y {

  }
</style>
<div class="modal fade" id="modal-catalog-prices" tabindex="-1" role="dialog" aria-labelledby="modal-catalog-prices-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modal-catalog-prices-label">View Product Details</h4>
            </div>
            <div class="modal-body" id="catalog-prices-body">
                <div style="padding: 3px;"><strong>Takeoff#:</strong> <span id="material-code">Takeoff# Goes Here</span></div>
                <div style="padding: 3px;"><strong>Description:</strong> <span id="material-name">Takeoff Name Goes Here</span></div>
                <div style="padding: 3px;"><strong>Product Line:</strong> <span id="category-name">Product Line Name Goes Here</span></div>
                <div style="padding: 3px;"><strong>UPC:</strong> <span id="material-upc">UPC Code Goes Here</span></div>
                <br />
                <form id="form-catalog-prices" name="from_catalog_prices">
                    <input type="hidden" name="item_id" id="item-id" value="0">
                    <input type="hidden" name="user_name" id="user-name" value="<?=$data['user']['user_name'];?>">
                    <div class="catalog-data-scrollbar">
                      <table class="table table-striped table-responsive" id="catalog-prices-table">
                          <thead>
                              <tr>
                                  <th>Supplier</th>
                                  <th>Site</th>
                                  <th class="text-right">Price</th>
                                  <th>Reference</th>
                              </tr>
                          </thead>
                          <tbody>
                          </tbody>
                      </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>