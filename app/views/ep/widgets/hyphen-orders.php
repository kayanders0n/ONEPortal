<style>
  .dash-ind-num {
    text-shadow: -1px 0 #000, 0 1px #000, 1px 0 #000, 0 -1px #000;
  }
</style>

<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Hyphen Orders</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>
  <div class="box-body no-padding" style="padding-top: 20px;">
    <div class="col-sm-6 col-md-4">
      <div class="small-box bg-red">
        <div class="inner text-center">
          <p>Records</p>
          <h3 id="hyphen-waiting-total" class="dash-ind-num">0</h3>
        </div>
        <a href="/hyphen" class="small-box-footer">
          More <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
    <div class="col-sm-6 col-md-4">
      <div class="small-box bg-red">
        <div class="inner text-center">
          <p>Orders</p>
          <h3 id="hyphen-waiting-orders" class="dash-ind-num">0</h3>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4">
      <div class="small-box bg-red">
        <div class="inner text-center">
          <p>Documents</p>
          <h3 id="hyphen-waiting-documents" class="dash-ind-num">0</h3>
        </div>
      </div>
    </div>
  </div>
  <!--
  <div class="box-footer">
    <div class="pull-right">
      <button class="btn btn-default btn-sm" role="link" onclick="location.href='/hyphen'">More <i class="fas fa-arrow-right fa-fw"></i></button>
    </div>
  </div>
  -->
</div>
<?=includePageScript('widgets', 'hyphen-orders.js');?>
