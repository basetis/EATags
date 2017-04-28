<?php
echo str_replace($this->lang->line('welcome'), $username);
echo anchor('/auth/logout/', 'Logout'); ?>