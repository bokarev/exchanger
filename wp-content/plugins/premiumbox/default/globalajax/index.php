<?php
if( !defined( 'ABSPATH')){ exit(); }

global $premiumbox;
$premiumbox->include_patch(__FILE__, 'settings');
$premiumbox->include_patch(__FILE__, 'filters');