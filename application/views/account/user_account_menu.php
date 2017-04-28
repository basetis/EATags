<?php
    // ACCOUNT MENU
        if(!isset($current_section)){
            $current_section = 'none';
        }
?>
    <div class="well span3 hidden-phone">
        <ul class="nav nav-list">
<?php   if($current_page == 'profile_config'){ ?>
            <li class="active"><a href="#"><?= $this->lang->line('account_menu_profile') ?></a></li>
<?php   } else { ?>
            <li><a href="<?php echo base_url($this->lang->line('header_link_account_profile')); ?>"><?= $this->lang->line('account_menu_profile') ?></a></li>
<?php   } ?>

<?php   if($current_page == 'features_config' && $current_section == 'features_config'){ ?>
            <li class="active"><a href="#"><?= $this->lang->line('account_menu_summary') ?></a></li>
<?php   } else { ?>
            <li><a href="<?php echo base_url($this->lang->line('header_link_account_features')); ?>"><?= $this->lang->line('account_menu_summary') ?></a></li>
<?php   } ?>

<?php   if ($this->evernote->user_granted_evernote_access($this->session->userdata('evernote_access_token')) != 1) {
            if ($current_page == 'require_evernote_access') { ?>
                <li class="active"><a href="#"><?= $this->lang->line('account_require_en_login') ?></a></li>
<?php       } else { ?>
                <li><a href="<?php echo base_url($this->lang->line('header_link_account_require_evernote')); ?>"><?= $this->lang->line('account_require_en_login') ?></a></li>
<?php       }
        } ?>

            <li class="divider"></li>
            <li class="nav-header"><?= $this->lang->line('account_menu_configuration') ?></li>
<?php   foreach ($user_features as $key => $feature) {
            if( $feature['config_required'] ){
                if($feature['name'] != $current_section) {?>
                    <li><a href="<?php echo base_url($this->lang->line('header_link_account_features') . $feature['keyname']); ?>"><?php echo $feature['name'] ?></a></li>
<?php           } else { ?>
                    <li class="active">
                        <a href="#"><?php echo $feature['name'] ?></a>
                    </li>
<?php           }
            }
        } ?>
        </ul>
    </div>



