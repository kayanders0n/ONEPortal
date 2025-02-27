<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'catalog_data.js') : '');?>
<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'modals.js') : '');?>

<div class="main">
    <h2 class="page-header">Find Catalog Item</h2>
    <div class="panel">
        <div class="panel-body">
            <div class="col-md-10">
                <div class="pull-left">
                    <form class="form-inline" method="post" name="form_catalog_data" id="form-catalog-data">
                        <input type="hidden" name="user_employee_id" id="user-employee-id" value="<?=$data['user']['seq_id'];?>">
                        <div class="form-group">
                            <label for="category-id">Product Line: </label>
                            <select class="form-control" name="category_id" id="category-id">
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="product-search">Product Search: </label>
                            <input class="form-control" name="product_search" id="product-search" size="55" autofocus />
                        </div>
                        <div class="form-group" style="display: none;">
                          <label for="product-upc">UPC: </label>
                          <input class="form-control" name="product_upc" id="product-upc" size="15" />
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit" onclick="loadCatalogData(); return false;">Search</button>
                            <button class="btn btn-warning" type="button" onclick="clearData(); return false;">Clear</button>
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
                <div class="loader hidden">
                    <img src="/assets/images/main/wheel.svg" alt="Loading..."> <span style="font-weight: bold">Loading. Please wait...</span>
                </div>
                <table id="catalog-data" class="table table-striped table-hover table-condensed data-table hidden">
                    <thead>
                    <tr>
                        <th>Takeoff#</th>
                        <th>Takeoff Name</th>
                        <th>Product Line</th>
                        <th>Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>
<?php include 'modals/prices.php'; ?>
<?php include 'modals/details.php'; ?>
