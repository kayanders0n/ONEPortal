<div class="box">
    <div class="box-header with-border">
        <h3 id="db-connection-title" class="box-title">Database Connections (<span id="db-connection-total">0</span>)</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body no-padding">
        <table id="db-connection-data" class="table table-striped">
            <thead>
              <tr>
                <th>ID#</th>
                <th>IP</th>
                <th>Username</th>
                <th>Connected</th>
                <th>Email</th>
                <th>Process</th>
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

<?=includePageScript('widgets', 'db-connections.js');?>