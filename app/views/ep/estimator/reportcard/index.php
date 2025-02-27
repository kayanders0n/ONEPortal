<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'estimator_data.js') : '');?>
<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'modals.js') : '');?>

<div class="main">
    <h2 class="page-header">Report Card</h2>
    <div class="panel">
        <div class="panel-body">
            <div class="col-md-10">
                <div class="pull-left">
                    <form class="form-inline" method="post" name="form_estimator_data" id="form-estimator-data">
                        <div class="form-group">
                            <label for="type-id">Company: </label>
                            <select class="form-control" name="company_id" id="company-id" onchange="changeCompany(); loadEstimatorList(); loadEstimatorData();">
                                <option value="<?=PLUMBING_ENTITYID?>" <?=(PLUMBING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Plumbing</option>
                                <option value="<?=CONCRETE_ENTITYID?>" <?=(CONCRETE_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Concrete</option>
                                <option value="<?=FRAMING_ENTITYID?>" <?=(FRAMING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Framing</option>
                                <option value="<?=DOORTRIM_ENTITYID?>" <?=(DOORTRIM_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Door and Trim</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="type-id">Estimator: </label>
                            <select class="form-control" name="estimator_id" id="estimator-id" onchange="changeEstimator(); loadEstimatorData();">
                                <option value="0">All</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="type-id">Builder: </label>
                            <select class="form-control" name="builder_id" id="builder-id" onchange="loadEstimatorData();">
                                <option value="0">All</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit" onclick="loadEstimatorData(); return false;">Refresh</button>
                        </div>
                    </form>
                </div>
            </div>
            <!--<div class="col-md-10">
                <br/>
                    <div class="pull-left" style="border: 2px solid gray; background: darkgray; color: white;">
                        <p style="text-align: center; margin: 10px; height: 2em; line-height: 2em; font-weight: bold;">
                            Total Communities: <span id="total-communities"></span>
                        </p>
                    </div>
                    <div class="pull-left" style="border: 2px solid gray; background: darkgray; color: white;">
                        <p style="text-align: center; margin: 10px; height: 2em; line-height: 2em; font-weight: bold;">
                            Total Communities: <span id="total-communities"></span>
                        </p>
                    </div>
                <br/>
            </div>-->
            <div class="col-md-10">
                <div class="pull-left">
                    <br/>
                    <table class="table table-bordered" style="font-size: 1.25em; background-color: var(--border-color);">
                        <tr>
                            <th>
                                <strong>Total Communities: </strong><span id="total-communities" style="color: var(--primary-color);">loading...</span>
                            </th>
                            <th>
                                <strong>Last 45: </strong><span id="total-startcount" style="color: var(--primary-color);">loading...</span>
                            </th>
                            <th>
                                <strong>Cost Date: </strong><span id="proposal-costdate-count" style="color: var(--primary-color);">loading...</span>
                            </th>
                            <th>
                                <strong>Contract Date: </strong><span id="proposal-contractdate-count" style="color: var(--primary-color);">loading...</span>
                            </th>
                            <th>
                                <strong>Options: </strong><span id="options-costmargin-count" style="color: var(--primary-color);">loading...</span>
                            </th>
                            <th>
                                <strong>Billing Adjustments: </strong><span id="billing-adj-count" style="color: var(--primary-color);">loading...</span>
                            </th>
                            <th>
                                <strong>A/R Queue: </strong><span id="po-review-count" style="color: var(--primary-color);">loading...</span>
                            </th>
                            <td style="background-color: #FFFFFF; padding: 3px;">
                                <button type="button" class="btn btn-info btn-sm" style="float: left;" onclick="captureEstimatorTotals();">Capture <span class="glyphicon glyphicon-menu-right" ></span></button>
                            </td>
                        </tr>
                    </table>
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
                <table id="estimator-data" class="table table-striped table-hover table-condensed data-table hidden">
                    <thead>
                    <tr>
                        <th>Comm#</th>
                        <th>Name</th>
                        <th>Builder</th>
                        <th>Estimator</th>
                        <th>Lots/Jobs</th>
                        <th style="text-align: left;">Cost Date</th>
                        <th style="text-align: left;">Contract Date</th>
                        <th style="text-align: left;">Options Date</th>
                        <th>Billing Adj</th>
                        <th>A/R Queue</th>
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
