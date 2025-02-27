      </div>
    </div>
  </div>

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <?=config('app.version');?>
    </div>
      <?=config('app.copyright');?> - Unauthorized access is prohibited!
  </footer>
</div>
<?php if (empty($data['path'][1])) {  ?>
  <script src="<?= config('assets'); ?>/js/chart.3.5.1.min.js"></script>
  <script src="<?= config('assets'); ?>/js/chartjs-plugin-datalabels.js">
    Chart.register(ChartDataLabels);
  </script>
<?php } ?>
<script src="<?=config('assets');?>/js/template.js"></script>
<script src="<?=config('assets');?>/js/alert.js"></script>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover({
            animation: true,
            trigger: 'hover'
        });
    })
</script>
<?=(!empty($data['page']['slug']) ? includePageScript($data['page']['slug'], 'ready.js') : '');?>
</body>
</html>
