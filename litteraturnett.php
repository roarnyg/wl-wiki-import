<?php
/*
   Plugin Name: Litteraturnett Forfatterdatabase
   Plugin URI: https://wordpress.org/plugins/wl-literaturnett/
   Description: Litteraturnett
   Author: WP Hosting
   Author URI: https://www.wp-hosting.no/
   Text-domain: literaturnett 
   Domain Path: /languages
   Version: 1.0.0
   License: AGPLv3 or later
   License URI: http://www.gnu.org/licenses/agpl-3.0.html

   This file is part of the WordPress plugin Litteraturnett Forfatterdatabase
   Copyright (C) 2018 WP Hosting AS

   Litteraturnett Forfatterdatabase is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Litteraturnett Forfatterdatabase is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.



 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


/* Instantiate the singleton, stash it in a global and add hooks. IOK 2018-02-07 */
require_once("Litteraturnett.class.php");
global $Litteraturnett;
$Litteraturnett = Litteraturnett::instance();
register_activation_hook(__FILE__,array($Litteraturnett,'activate'));
register_deactivation_hook(__FILE__,'Litteraturnett::deactivate');
register_uninstall_hook(__FILE__, 'Litteraturnett::uninstall');
if (is_admin()) {
        add_action('admin_init',array($Litteraturnett,'admin_init'));
        add_action('admin_menu',array($Litteraturnett,'admin_menu'));
}
add_action('init',array($Litteraturnett,'init'));
add_action( 'plugins_loaded', array($Litteraturnett,'plugins_loaded'));


?>
