<?php
/**
 * Sidebar template
 * @package Infobd_3D
 */
if ( ! is_active_sidebar( 'sidebar-1' ) ) return; ?>
<aside class="site-sidebar">
    <?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
