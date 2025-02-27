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
  <link rel="stylesheet" href="<?=config('assets');?>/css/login.css?ver=<?=rand();?>">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="//cdn.datatables.net/v/bs/dt-1.10.18/r-2.2.2/datatables.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
  <script src="//cdn.datatables.net/plug-ins/1.10.18/sorting/datetime-moment.js"></script>
  <script defer src="//use.fontawesome.com/releases/v5.13.0/js/all.js"></script>
</head>
<body>
<a id="top"></a>
<div class="container">
  <?php include VIEWS . '/alert.php' ?>
  <div class="login-wrapper">
    <div class="login-logo"><img src="/assets/images/main/logo.png" alt="The Whitton Way"></div>
