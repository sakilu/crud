<!DOCTYPE html>
<html>

<head>
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <title><?= get_site_config('backend_title') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <!-- Required Plugin CSS -->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>vendor/plugins/tagmanager/tagmanager.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>vendor/plugins/daterange/daterangepicker.css">
    <link rel="stylesheet" type="text/css"
          href="<?= base_url() ?>vendor/plugins/datepicker/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" type="text/css"
          href="<?= base_url() ?>vendor/plugins/colorpicker/css/bootstrap-colorpicker.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>vendor/plugins/select2/css/core.css">

    <!-- Font CSS (Via CDN) -->
    <link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700'>

    <!-- Glyphicons Pro CSS(font) -->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/fonts/glyphicons-pro/glyphicons-pro.css">

    <!-- Icomoon CSS(font) -->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/fonts/icomoon/icomoon.css">

    <!-- Iconsweets CSS(font) -->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/fonts/iconsweets/iconsweets.css">

    <!-- Octicons CSS(font) -->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/fonts/octicons/octicons.css">

    <!-- Stateface CSS(font) -->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/fonts/stateface/stateface.css">

    <!-- Animate CSS -->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>vendor/plugins/animate/animate.min.css">

    <!-- Vendor CSS -->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>vendor/plugins/magnific/magnific-popup.css">

    <!-- Theme CSS -->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/skin/default_skin/css/theme.css">


    <!-- Admin Forms CSS -->
    <link rel="stylesheet" type="text/css"
          href="<?= base_url() ?>assets/admin-tools/admin-forms/css/admin-forms.min.css">
    <!-- Datatables CSS -->
    <link rel="stylesheet" type="text/css"
          href="<?= base_url() ?>vendor/plugins/datatables/media/css/dataTables.bootstrap.css">
    <!-- Datatables Editor Addon CSS -->
    <link rel="stylesheet" type="text/css"
          href="<?= base_url() ?>vendor/plugins/datatables/extensions/Editor/css/dataTables.editor.css">
    <!-- Datatables ColReorder Addon CSS -->
    <link rel="stylesheet" type="text/css"
          href="<?= base_url() ?>vendor/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css">

    <!-- Adup CSS -->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>crud/css/backend.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>crud/css/jquery.dataTables.yadcf.css">

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= base_url() ?>assets/img/favicon.ico">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <script src="<?= base_url() ?>vendor/jquery/jquery-1.11.1.min.js"></script>
    <script src="<?= base_url() ?>vendor/jquery/jquery_ui/jquery-ui.min.js"></script>

<!--    <link href="--><?//= base_url() ?><!--assets/css/jquery.appendGrid-1.6.1.css" rel="stylesheet"/>-->
    <script src="<?= base_url() ?>crud/js/jquery.appendGrid-1.6.1.js"></script>

</head>

<body class="sb-top sb-top-sm">

<!-- Start: Main -->
<div id="main">

    <!-----------------------------------------------------------------+ 
       ".navbar" Helper Classes: 
    -------------------------------------------------------------------+ 
       * Positioning Classes: 
        '.navbar-static-top' - Static top positioned navbar
        '.navbar-static-top' - Fixed top positioned navbar

       * Available Skin Classes:
         .bg-dark    .bg-primary   .bg-success   
         .bg-info    .bg-warning   .bg-danger
         .bg-alert   .bg-system 
    -------------------------------------------------------------------+
      Example: <header class="navbar navbar-fixed-top bg-primary">
      Results: Fixed top navbar with blue background 
    ------------------------------------------------------------------->

    <!-- Start: Header -->
    <header class="navbar navbar-fixed-top navbar-shadow">
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown menu-merge">
                <a href="#" class="dropdown-toggle fw600 p15" data-toggle="dropdown">
                    <span class="hidden-xs pl15"><?= $this->auth->get_name() ?></span>
                    <span class="caret caret-tp hidden-xs"></span>
                </a>
                <ul class="dropdown-menu list-group dropdown-persist w250" role="menu">
                    <li class="list-group-item">
                        <a href="<?= base_url(sprintf('%s/login/form', $this->crud->get_prefix())) ?>"
                           class="animated animated-short fadeInUp">
                            <span class="fa fa-gear"></span> 變更密碼 </a>
                    </li>
                    <li class="dropdown-footer">
                        <a href="<?= base_url($this->crud->get_module_url() . '/logout') ?>" class="">
                            <span class="fa fa-power-off pr5"></span> 登出 </a>
                    </li>
                </ul>
            </li>
            <li id="toggle_sidemenu_t">
                <span class="fa fa-caret-up"></span>
            </li>
        </ul>
    </header>