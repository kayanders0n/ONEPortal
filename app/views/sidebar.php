<div id="sidebar">
  <div class="nav nav-sidebar" style="display: none;" id="ajax-progress">
    <div class="well" style="margin-bottom: 0;">
      <img src="/assets/images/main/wheel.svg" alt="Loading..."> <span style="font-weight: bold">Please wait...</span>
    </div>
  </div>
  <form class="sidebar-form" method="get" action="https://www.google.com/search" target="_blank">
    <div class="input-group">
      <input type="text" class="form-control" placeholder="Search..." name="q">
      <span class="input-group-btn">
        <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
      </span>
    </div>
  </form>
  <ul class="sidebar-menu tree" data-widget="tree">
    <li <?=(empty($data['path'][1]) ? 'class="active"' : '')?>><a href="/"><i class="fas fa-tachometer-alt fa-fw"></i> <span>Dashboard</span></a></li>
    <?php
    $i = 0;
    if (!empty($data['nav_items'])) {
      foreach ($data['nav_items'] as $name0 => $slug0) {
        ?>
      <li <?=(!empty($data['path'][1]) && $data['path'][1] == $slug0 ? 'class="active" ' : '')?>>
        <a href="/<?=$slug0;?>"><?=$data['nav_icons'][$slug0];?> <span><?=$name0;?></span></a>
        <?php
        if (!empty($data['page_nav']) && count($data['page_nav'])) { ?>
          <ul class="treeview-menu">
            <?php
            foreach ($data['page_nav'] as $name1 => $slug1) {
              echo '<li ' . ('/' . implode('/', $data['path']) == $slug1 ? 'class="active"' : '') . '><a href="' . $slug1 . '"><i class="fas fa-angle-right"></i> ' . $name1 . '</a></li>';
            }
            ?>
          </ul>
          </li>
        <?php
        }
      }
    } ?>
  </ul>
</div>
