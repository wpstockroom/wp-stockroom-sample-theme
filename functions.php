<?php

include_once __DIR__ .'/class-wp-stockroom-updater.php';
//                         REPLACE THIS DOMAIN.
add_filter( "update_themes_wpstockroom.com", array( 'WP_Stockroom_Updater', 'check_update' ),10, 4 );
