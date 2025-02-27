<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php if (isset($data['page']['meta-refresh'])) : ?>
  <meta http-equiv="refresh" content="<?=$data['page']['meta-refresh']?>">
  <?php endif; ?>
  <meta name="description" content="<?=($data['page']['description'] ?? '');?>">
  <meta name="author" content="<?=($data['page']['author'] ?? '');?>">
  <link rel="shortcut icon" type="image/x-icon" href="<?=config('assets');?>/images/main/whitton.ico" />
  <link rel="apple-touch-icon" sizes="144x144" href="<?=config('assets');?>/images/main/whitton_144.png" />
  <link rel="apple-touch-icon" sizes="114x114" href="<?=config('assets');?>/images/main/whitton_114.png" />
  <link rel="apple-touch-icon" sizes="72x72" href="<?=config('assets');?>/images/main/whitton_72.png" />
  <link rel="apple-touch-icon" sizes="57x57" href="<?=config('assets');?>/images/main/whitton_57.png" />
  <title>TWW - <?=($data['page']['title'] ?? '');?></title>
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.13.0/css/all.css">
  <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:400,500,700,300">
  <link rel="stylesheet" href="//cdn.datatables.net/v/bs/dt-1.10.18/r-2.2.2/datatables.min.css" />
  <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
  <!-- Custom styles for this template -->
  <link rel="stylesheet" href="<?=config('assets');?>/css/custom.css?ver=<?=rand();?>">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="//cdn.datatables.net/v/bs/dt-1.10.18/r-2.2.2/datatables.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
  <script src="//cdn.datatables.net/plug-ins/1.10.18/sorting/datetime-moment.js"></script>
  <script defer src="//use.fontawesome.com/releases/v5.13.0/js/all.js"></script>
</head>
<body class="sidebar-mini">
<a id="top"></a>
<div id="wrapper">
  <header>
    <div class="main-header">
      <a href="/" class="logo">
        <span class="logo-mini"><img src="<?=config('assets');?>/images/main/icon.png"></span>
        <span class="logo-lg"><img src="<?=config('assets');?>/images/main/logo.png"></span>
      </a>
      <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
          <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav navbar-right">
            <li><a><?=($data['user']['description'] ?? '');?></a></li>
            <li><a href="/auth/logout" title="Log Out"><i class="fas fa-sign-out-alt fa-lg"></i></a></li>
          </ul>
        </div>
      </nav>
    </div>
  </header>
    <?php include 'sidebar.php'; ?>
  <div class="content-wrapper">
    <div class="content-header">
      <div class="portal-badge" data-container="body" data-toggle="popover" data-placement="right" data-content="<?=strtoupper(config('app.portal_full'));?>"><?=strtoupper(config('app.portal'));?></div>
      <h1><?=(!empty($data['path'][1]) ? $data['nav_icons'][$data['path'][1]] . ' ' . ucwords(str_replace('-', ' ', $data['path'][1])) : $data['nav_icons']['dashboard'] . ' ' . 'Dashboard');?>
        <small></small>
      </h1>
    </div>
    <div class="content">
      <div class="container-fluid">
          <?php include 'alert.php'; ?>
