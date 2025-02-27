<style>
    #birthdays-data li { background: #FAEBD7; }
    #birthdays-data li:nth-child(odd) { background: white; }
    .label-as-badge {
        border-radius: 1em;
    }
</style>
<div>
  <div class="panel panel-default">
   <div class="panel-body" style="background: var(--primary-color); text-align: center;">
     <i class="fas fa-birthday-cake" style="font-size: 1.25em; color: #3c1321; margin-right: 5px;"></i>
     <span style="color: white;">HAPPY BIRTHDAY</span>
     <i class="fas fa-birthday-cake" style="font-size: 1.25em; color:  #3c1321; margin-left: 5px;"></i>
   </div>
    <ul id="birthdays-data" class="list-group">
    </ul>
  </div>
</div>

<?= includePageScript('widgets', 'birthdays.js'); ?>
