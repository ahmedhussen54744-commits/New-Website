<?php
/**
 * Search form
 * @package Infobd_3D
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label class="screen-reader-text" for="s"><?php esc_html_e( 'Search', 'infobd-3d' ); ?></label>
    <div style="display:flex; gap:8px;">
        <input type="search" id="s" name="s" placeholder="<?php esc_attr_e( 'Search...', 'infobd-3d' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" style="flex:1; padding:12px 14px; background: var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text);">
        <button type="submit" class="btn"><?php esc_html_e( 'Search', 'infobd-3d' ); ?></button>
    </div>
</form>
