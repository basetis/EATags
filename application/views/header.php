<?
  $CI =& get_instance();

  $cookies_enabled = $this->input->cookie('eatags_confirm_cookies',TRUE);


  if( ! isset($current_page) ){
    log_message('debug', 'current_page not setted => no active class on menu ' );
    $current_page = 'home';
  }

  $menu_classes = array(

    'about' => '',
    'features' => '',
    'sign_in' => '',
    'sign_up' => '',
    'account' => '',
    'faqs' => '',
    'recover_password' => '',
    'privacy_policy' => '',
    'legal_advise' => '',
    'features_config' => '',
    'profile_config' => '',
  );

  $menu_classes[$current_page] = 'active';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $this->lang->line('header_meta_description') ?>" />
    <meta name="keywords" content="<?= $this->lang->line('header_meta_keywords') ?>" />
    <link rel="canonical" href="https://eatags.com/" />


    <title><?= $this->lang->line('header_title') ?></title>
    <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>" />

    <link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic' rel='stylesheet' type='text/css'>

  <!-- <link rel="stylesheet/less" href="<?php //echo base_url('assets/bootstrap/less/bootstrap.less'); ?>" />
  <link rel="stylesheet/less" href="<?php //echo base_url('assets/bootstrap/less/responsive.less'); ?>" /> -->

  <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/bootstrap/css/bootstrap.css'); ?>">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/bootstrap/css/responsive.css'); ?>">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/bootstrap/css/features.css'); ?>">

  <!-- <script src="<?php //echo base_url('assets/less/less-1.3.0.min.js'); ?>"></script> -->

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>



  <script src="<?=base_url('assets/js/bootstrap.min.js');?>" type="text/javascript"></script>

  <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>

  <div class="navbar navbar-fixed-top navbar-eat">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <i class="icon-circle-arrow-down eat-icon-invert"></i>
          </a>
          <a class="brand" id="logo" title="<?= $this->lang->line('header_title') ?>" href="<?php echo base_url(); ?>"></a>
          <div class="nav-collapse">
            <ul class="nav">

              <li class="divider-vertical"></li>
              <li class="<?php echo $menu_classes['features']?>">
                <a href="<?php echo base_url($this->lang->line('header_link_features')); ?>">
                  <i class="icon-tags eat-icon-invert"></i> <?= $this->lang->line('header_features') ?></a>
              </li>
              <li class="divider-vertical"></li>
              <li class="<?php echo $menu_classes['faqs']?>">
                <a href="<?php echo $this->lang->line('header_link_blog'); ?>">
                  <i class="icon-book eat-icon-invert"></i> <?= $this->lang->line('header_blog') ?></a>
              </li>
              <li class="divider-vertical"></li>
              <li class="dropdown">
                <a href="#"
                      class="dropdown-toggle"
                      data-toggle="dropdown">
                        <i class="icon-flag eat-icon-invert"></i> <?= $this->lang->line('header_change_lang') ?>
                      <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                  <li><a href="en-US" class="lang"><?= $this->lang->line('header_english') ?></a></li>
                  <li><a href="es-ES" class="lang"><?= $this->lang->line('header_spanish') ?></a></li>
                  <li><a href="ca-ES" class="lang"><?= $this->lang->line('header_catalan') ?></a></li>
                  <form action="" method="post" style="display:none;"><input type="hidden" id="lang" name="lang" value=""></form>
                </ul>
              </li>
              <li class="divider-vertical"></li>
              <?php
                  if ($CI->tank_auth->is_logged_in(TRUE)) { ?>
                    <li class="<?php echo $menu_classes['profile_config']?>">
                      <a href="<?php echo base_url($this->lang->line('header_link_account_profile')); ?>"> <i class="icon-cog eat-icon-invert"></i> <?= $this->lang->line('header_profile') ?></a>
                    </li>
                    <li class="divider"></li>
                    <li class="<?php echo $menu_classes['features_config']?>">
                      <a href="<?php echo base_url($this->lang->line('header_link_account_features')); ?>"> <i class="icon-fire eat-icon-invert"></i> <?= $this->lang->line('header_features_configuration') ?></a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="<?php echo base_url($this->lang->line('header_link_auth_logout'));?>"> <i class="icon-remove  eat-icon-invert"></i> <?= $this->lang->line('header_disconnect') ?></a>
                    </li>
                  <?php } else { ?>
                    <li class="<?php echo $menu_classes['sign_in']?>">
                      <a id="sign_in" href="<?=base_url($this->lang->line('header_link_auth_login'));?>"> <i class="icon-login eat-icon-invert"></i><?= $this->lang->line('header_sign_in') ?></a>
                    </li>
              <li class="divider-vertical"></li>
                    <li class="<?php echo $menu_classes['sign_up']?>">
                      <a id="sign_up" href="<?=base_url($this->lang->line('header_link_auth_register'));?>"><i class="icon-register eat-icon-invert"></i><?= $this->lang->line('header_sign_up') ?></a>
                    </li>
                  <?php } ?>
               <!-- <li class="dropdown">
                <a href="#"
                      class="dropdown-toggle"
                      data-toggle="dropdown">
                      <?php if ($CI->tank_auth->is_logged_in(TRUE)) { ?>
                        <i class="icon-user eat-icon-invert"></i> <?=$CI->tank_auth->get_username();?>
                      <?php } else { ?>
                        <i class="icon-wrench eat-icon-invert"></i> <?= $this->lang->line('header_account') ?>
                      <?php } ?>
                      <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                  <?php
                  if ($CI->tank_auth->is_logged_in(TRUE)) { ?>
                    <li class="<?php echo $menu_classes['profile_config']?>">
                      <a href="<?php echo base_url($this->lang->line('header_link_account_profile')); ?>"> <i class="icon-cog eat-icon-invert"></i> <?= $this->lang->line('header_profile') ?></a>
                    </li>
                    <li class="divider"></li>
                    <li class="<?php echo $menu_classes['features_config']?>">
                      <a href="<?php echo base_url($this->lang->line('header_link_account_features')); ?>"> <i class="icon-fire eat-icon-invert"></i> <?= $this->lang->line('header_features_configuration') ?></a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="<?php echo base_url($this->lang->line('header_link_auth_logout'));?>"> <i class="icon-remove  eat-icon-invert"></i> <?= $this->lang->line('header_disconnect') ?></a>
                    </li>
                  <?php } else { ?>
                    <li class="<?php echo $menu_classes['sign_in']?>">
                      <a href="<?=base_url($this->lang->line('header_link_auth_login'));?>"><?= $this->lang->line('header_sign_in') ?></a>
                    </li>
                    <li class="divider"></li>
                    <li class="<?php echo $menu_classes['sign_up']?>">
                      <a href="<?=base_url($this->lang->line('header_link_auth_register'));?>"><?= $this->lang->line('header_sign_up') ?></a>
                    </li>
                  <?php } ?>
                </ul>
              </li> -->
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>


    <div class="container"><!-- ATTENTION: container is closed at footer view -->

<?php

  if( $this->session->flashdata('alert_message') ){
    echo '<div class="alert alert-info">' . $this->session->flashdata('alert_message') . '</div>';
  }
  if(!isset($cookies_enabled) || strlen($cookies_enabled) == 0 ){
    $this->load->language('cookies');
    echo '<div id="alert-cookies" class="alert alert-info">'. $this->lang->line('cookies_alert').' <button class="btn" id="accept_cookies" onclick="set_accept_cookies();">'. $this->lang->line('cookies_alert_accept').'</button></div>';
  }
?>
<script type="text/javascript">
  function set_accept_cookies() {
    $.post( "<?php echo base_url('home/accept_cookies')?>", function( data ) {

      $("#alert-cookies").hide();
    });
}
</script>





