<style>
  .dash-ind-num {
    text-shadow: -1px 0 #000, 0 1px #000, 1px 0 #000, 0 -1px #000;
  }
</style>

<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">Network Connectivity Check</h3><span id="network-ping-time"></span>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>
  <div class="box-body no-padding" style="padding-top: 20px;">
    <div class="col-sm-6 col-md-4">
      <div id="tucson-ping-box" class="small-box bg-red">
        <div class="inner text-center">
          <p>Tucson</p>
          <h3 id="tucson-ping-time" class="dash-ind-num">---</h3>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4">
      <div id="buckeye-ping-box"  class="small-box bg-red">
        <div class="inner text-center">
          <p>Buckeye</p>
          <h3 id="buckeye-ping-time" class="dash-ind-num">---</h3>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-4">
      <div id="warner-ping-box" class="small-box bg-red">
        <div class="inner text-center">
          <p>Warner</p>
          <h3 id="warner-ping-time" class="dash-ind-num">---</h3>
        </div>
      </div>
    </div>
  </div>
</div>
<?=includePageScript('widgets', 'network-ping.js');?>
