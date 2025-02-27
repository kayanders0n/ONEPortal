<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Estimator A/R Queue</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
        <div style="clear:both;"></div>
        <div style="float: right;">
            <form id="estimator-ar-queue-form">
                <select id="company" name="company" onchange="LoadEstimatorARQueueChart();">
                    <option value="<?=PLUMBING_ENTITYID?>" <?=(PLUMBING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Plumbing</option>
                    <option value="<?=CONCRETE_ENTITYID?>" <?=(CONCRETE_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Concrete</option>
                    <option value="<?=FRAMING_ENTITYID?>" <?=(FRAMING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Framing</option>
                    <option value="<?=DOORTRIM_ENTITYID?>" <?=(DOORTRIM_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Door & Trim</option>
                </select>
            </form>
        </div>
        <div id="estimator-ar-queue-loader" class="loader">
            <img src="/assets/images/main/wheel.svg" alt="Loading..."> <span style="font-weight: bold">Loading. Please wait...</span>
        </div>
    </div>
    <div class="box-body">
        <div class="chart">
            <canvas id="estimator-ar-queue-chart" style="height: 230px; width: 433px;" height="230" width="433"></canvas>
        </div>
    </div>
</div>
<?= includePageScript('widgets', 'estimator-ar-queue-chart.js'); ?>

