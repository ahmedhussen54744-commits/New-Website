<?php
/**
 * Copyright Protection — 10 features
 *
 * @package Infobd_Mega
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class Infobd_Mega_Copyright {

    public function __construct() {
        add_action( 'wp_footer', array( $this, 'inject_protection' ), 99 );
        add_filter( 'the_content', array( $this, 'append_invisible_signature' ), 999 );
    }

    public function inject_protection() {
        if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) return;
        $opts = Infobd_Mega_Options::all();
        $warn = esc_js( $opts['cp_warning_text'] ?? '' );
        $site = esc_url( home_url( '/' ) );
        ?>
        <script>
        (function(){
            var O = window.InfobdMega && InfobdMega.opts ? InfobdMega.opts : {};
            var warn = <?php echo wp_json_encode( $warn ); ?>;

            function notify(){
                if (!warn) return;
                var n = document.createElement('div');
                n.textContent = warn;
                n.setAttribute('role','alert');
                n.style.cssText = 'position:fixed;top:24px;left:50%;transform:translateX(-50%) translateY(-200%);background:#ff2d55;color:#fff;padding:12px 22px;border-radius:30px;font-weight:700;z-index:99999;box-shadow:0 10px 30px rgba(0,0,0,.4);transition:transform .4s cubic-bezier(.2,.9,.3,1.4);font-family:system-ui,sans-serif;';
                document.body.appendChild(n);
                requestAnimationFrame(function(){ n.style.transform='translateX(-50%) translateY(0)'; });
                setTimeout(function(){ n.style.transform='translateX(-50%) translateY(-200%)'; setTimeout(function(){n.remove();},500); }, 2200);
            }

            // Right-click
            if (parseInt(O.cp_disable_right_click||0,10)){
                document.addEventListener('contextmenu', function(e){
                    if (e.target && (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA')) return;
                    e.preventDefault(); notify();
                });
            }
            // Text selection
            if (parseInt(O.cp_disable_text_select||0,10)){
                document.body.style.userSelect='none';
                document.body.style.webkitUserSelect='none';
            }
            // Copy
            if (parseInt(O.cp_disable_copy||0,10)){
                document.addEventListener('copy', function(e){
                    var sel = window.getSelection().toString();
                    if (parseInt(O.cp_append_source_on_copy||0,10) && sel.length > 50){
                        e.clipboardData.setData('text/plain', sel + '\n\n— ' + <?php echo wp_json_encode( $site ); ?>);
                        e.preventDefault();
                        notify();
                        return;
                    }
                    e.preventDefault(); notify();
                });
                document.addEventListener('cut', function(e){ e.preventDefault(); notify(); });
            }
            // Drag start (image protection)
            if (parseInt(O.cp_disable_drag||0,10)){
                document.addEventListener('dragstart', function(e){
                    if (e.target.tagName==='IMG'){ e.preventDefault(); notify(); }
                });
            }
            // Print Screen / common shortcuts
            if (parseInt(O.cp_disable_print_screen||0,10) || parseInt(O.cp_disable_view_source||0,10) || parseInt(O.cp_disable_devtools||0,10)){
                document.addEventListener('keydown', function(e){
                    var k = e.key, ck = e.ctrlKey || e.metaKey, sk = e.shiftKey;
                    // F12
                    if (parseInt(O.cp_disable_devtools||0,10) && k === 'F12'){ e.preventDefault(); notify(); }
                    // Ctrl/Cmd + U (view source)
                    if (parseInt(O.cp_disable_view_source||0,10) && ck && (k==='u' || k==='U')){ e.preventDefault(); notify(); }
                    // Ctrl/Cmd + S (save)
                    if (parseInt(O.cp_disable_view_source||0,10) && ck && (k==='s' || k==='S')){ e.preventDefault(); notify(); }
                    // Ctrl/Cmd + Shift + I/J/C (devtools)
                    if (parseInt(O.cp_disable_devtools||0,10) && ck && sk && ['I','J','C','i','j','c'].indexOf(k) > -1){ e.preventDefault(); notify(); }
                    // Ctrl/Cmd + P (print)
                    if (parseInt(O.cp_disable_print_screen||0,10) && ck && (k==='p' || k==='P')){ e.preventDefault(); notify(); }
                });
            }
            // Image right-click guard fallback
            document.querySelectorAll('img').forEach(function(img){
                img.setAttribute('draggable','false');
                img.style.pointerEvents = 'auto';
            });
        })();
        </script>
        <style>
            img { -webkit-user-drag: none !important; user-drag: none !important; -webkit-touch-callout: none !important; }
        </style>
        <?php
    }

    public function append_invisible_signature( $content ) {
        if ( ! is_singular() || ! in_the_loop() || ! is_main_query() ) return $content;
        // Append a hidden signature so copied text traces back
        $sig = '<span aria-hidden="true" style="display:none !important;">© ' . esc_html( get_bloginfo( 'name' ) ) . ' — ' . esc_url( home_url( '/' ) ) . '</span>';
        return $content . $sig;
    }
}
