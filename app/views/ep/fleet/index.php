<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'fleet_data.js') : '');?>
<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'modals.js') : '');?>

<div class="main">
    <h2 class="page-header">Fleet</h2>
    <div class="panel">
        <div class="panel-body">
            <div class="col-md-10">
                <div class="pull-left">
                    <form class="form-inline" method="post" name="form_fleet_data" id="form-fleet-data">
                        <div class="form-group">
                            <label for="type-id">Company: </label>
                            <select class="form-control" name="company_id" id="company-id" onchange="changeCompany(); loadFleetData();">
                                <option value="<?=PLUMBING_ENTITYID?>" <?=(PLUMBING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Plumbing</option>
                                <option value="<?=CONCRETE_ENTITYID?>" <?=(CONCRETE_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Concrete</option>
                                <option value="<?=FRAMING_ENTITYID?>" <?=(FRAMING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Framing</option>
                                <option value="<?=DOORTRIM_ENTITYID?>" <?=(DOORTRIM_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Door and Trim</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit" onclick="loadFleetData(); return false;">Refresh</button>
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
                <table id="fleet-data" class="table table-striped table-hover table-condensed data-table hidden">
                    <thead>
                    <tr>
                        <th>Vehicle#</th>
                        <th>Name</th>
                        <th>Configuration</th>
                        <th>License<span style="float:right">Expires</span></th>
                        <th>Last Odm.</th>
                        <th>Odm. Date</th>
                        <th>Last Oil Change</th>
                        <th>Location</th>
                        <th>Cmpy</th>
                         <th>GPS</th>
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
