<div class="box">
  <div class="box-header with-border">
    <h3 id="db-connection-title" class="box-title">Visual Record Productivity</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
    <div style="clear:both;"></div>
    <div style="float: right;">
      <form id="twwvr-productivity-form">
        <select id="site" name="site" onchange="GetTWWVRProductivityData();">
          <option value="" selected>All Sites</option>
          <option value="00040">Eastside</option>
          <option value="00030">Westside</option>
          <option value="00020">Tucson</option>
        </select>
        <select id="company" name="company" onchange="GetTWWVRProductivityData();">
          <option value="<?=PLUMBING_ENTITYID?>" <?=(PLUMBING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Plumbing</option>
          <option value="<?=CONCRETE_ENTITYID?>" <?=(CONCRETE_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Concrete</option>
          <option value="<?=FRAMING_ENTITYID?>" <?=(FRAMING_ENTITYID==$data['page']['default_company_id'])?'selected':''?>>Framing</option>
        </select>
        <select id="days-old" name="days-old" onchange="GetTWWVRProductivityData();">
          <option value="7">7 days</option>
          <option value="14" selected>14 days</option>
          <option value="30">30 days</option>
          <option value="60">60 days</option>
          <option value="90">90 days</option>
        </select>
      </form>
    </div>
    <div id="twwvr-productivity-loader" class="loader">
      <img src="/assets/images/main/wheel.svg" alt="Loading..."> <span
              style="font-weight: bold">Loading. Please wait...</span>
    </div>
  </div>
  <div class="box-body no-padding">
    <table id="twwvr-productivity-data" class="table table-striped">
      <thead>
      <tr>
        <th>ID#</th>
        <th>Employee</th>
        <th>Site</th>
        <th>Total Megabytes</th>
        <th>Count</th>
      </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
  <!--
    <div class="box-footer">
        <div class="pull-right">
            <button class="btn btn-default btn-sm" role="link" onclick="location.href='/hyphen'">More <i class="fas fa-arrow-right fa-fw"></i></button>
        </div>
    </div>
   -->
</div>

<?= includePageScript('widgets', 'twwvr-productivity.js'); ?>