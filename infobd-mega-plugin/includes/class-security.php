<?php
/**
 * Security hardening — 15+ features
 *
 * @package Infobd_Mega
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class Infobd_Mega_Security {

    public function __construct() {
        $opts = Infobd_Mega_Options::all();

        // Disable XML-RPC
        if ( ! empty( $opts['sec_disable_xmlrpc'] ) ) {
            add_filter( 'xmlrpc_enabled', '__return_false' );
            add_filter( 'wp_headers', array( $this, 'remove_xpingback' ) );
            add_filter( 'pings_open', '__return_false' );
        }

        // Restrict REST anonymous
        if ( ! empty( $opts['sec_disable_rest_anon'] ) ) {
            add_filter( 'rest_authentication_errors', array( $this, 'restrict_rest_anonymous' ) );
        }

        // Remove version
        if ( ! empty( $opts['sec_remove_version'] ) ) {
            remove_action( 'wp_head', 'wp_generator' );
            add_filter( 'the_generator', '__return_empty_string' );
            add_filter( 'style_loader_src', array( $this, 'strip_version' ), 9999 );
            add_filter( 'script_loader_src', array( $this, 'strip_version' ), 9999 );
        }

        // Disable user enumeration
        if ( ! empty( $opts['sec_disable_user_enum'] ) ) {
            add_action( 'init', array( $this, 'block_user_enum' ) );
            add_filter( 'rest_endpoints', array( $this, 'restrict_users_endpoint' ) );
        }

        // Login attempts limiter
        if ( ! empty( $opts['sec_login_attempts'] ) ) {
            add_action( 'wp_login_failed', array( $this, 'on_login_failed' ) );
            add_filter( 'authenticate', array( $this, 'check_lockout' ), 30, 3 );
            add_filter( 'wp_login', array( $this, 'on_login_success' ), 10, 2 );
        }

        // Force strong passwords
        if ( ! empty( $opts['sec_force_strong_pass'] ) ) {
            add_action( 'user_profile_update_errors', array( $this, 'enforce_strong_password' ), 10, 3 );
            add_action( 'validate_password_reset', array( $this, 'enforce_strong_password' ), 10, 2 );
        }

        // Block bad bots
        if ( ! empty( $opts['sec_block_bad_bots'] ) ) {
            add_action( 'init', array( $this, 'block_bad_bots' ), 1 );
        }

        // Security headers
        if ( ! empty( $opts['sec_security_headers'] ) ) {
            add_action( 'send_headers', array( $this, 'security_headers' ) );
        }

        // Hide login errors
        if ( ! empty( $opts['sec_hide_login_errors'] ) ) {
            add_filter( 'login_errors', array( $this, 'mask_login_errors' ) );
        }

        // Disable directory listing (htaccess)
        // (handled via .htaccess writing on activation in admin)

        // Block PHP in uploads
        if ( ! empty( $opts['sec_block_php_in_uploads'] ) ) {
            add_filter( 'upload_mimes', array( $this, 'limit_upload_mimes' ) );
            add_filter( 'wp_check_filetype_and_ext', array( $this, 'check_filetype' ), 10, 4 );
        }

        // Disable file edit
        if ( ! empty( $opts['sec_disable_file_edit'] ) && ! defined( 'DISALLOW_FILE_EDIT' ) ) {
            define( 'DISALLOW_FILE_EDIT', true );
        }
    }

    public function remove_xpingback( $headers ) {
        if ( isset( $headers['X-Pingback'] ) ) unset( $headers['X-Pingback'] );
        return $headers;
    }

    public function restrict_rest_anonymous( $result ) {
        if ( true === $result || is_wp_error( $result ) ) return $result;
        if ( ! is_user_logged_in() ) {
            return new WP_Error(
                'rest_not_logged_in',
                __( 'You must be logged in to access the REST API.', 'infobd-mega' ),
                array( 'status' => 401 )
            );
        }
        return $result;
    }

    public function strip_version( $src ) {
        if ( strpos( $src, 'ver=' ) ) {
            $src = remove_query_arg( 'ver', $src );
        }
        return $src;
    }

    public function block_user_enum() {
        if ( ! is_admin() && isset( $_GET['author'] ) && is_numeric( $_GET['author'] ) ) {
            wp_safe_redirect( home_url( '/' ), 301 );
            exit;
        }
    }

    public function restrict_users_endpoint( $endpoints ) {
        if ( ! is_user_logged_in() ) {
            if ( isset( $endpoints['/wp/v2/users'] ) ) unset( $endpoints['/wp/v2/users'] );
            if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
        }
        return $endpoints;
    }

    private function lockout_key( $ip ) {
        return 'infobd_lockout_' . md5( $ip );
    }

    private function attempts_key( $ip ) {
        return 'infobd_attempts_' . md5( $ip );
    }

    private function client_ip() {
        $ip = '0.0.0.0';
        foreach ( array( 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ) as $key ) {
            if ( ! empty( $_SERVER[ $key ] ) ) {
                $ip = explode( ',', sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) ) )[0];
                break;
            }
        }
        return $ip;
    }

    public function on_login_failed( $username ) {
        $ip = $this->client_ip();
        $opts = Infobd_Mega_Options::all();
        $max = (int) ( $opts['sec_max_attempts'] ?? 5 );
        $minutes = (int) ( $opts['sec_lockout_minutes'] ?? 30 );
        $attempts = (int) get_transient( $this->attempts_key( $ip ) );
        $attempts++;
        set_transient( $this->attempts_key( $ip ), $attempts, $minutes * MINUTE_IN_SECONDS );
        if ( $attempts >= $max ) {
            set_transient( $this->lockout_key( $ip ), 1, $minutes * MINUTE_IN_SECONDS );
        }
    }

    public function on_login_success( $user_login, $user ) {
        $ip = $this->client_ip();
        delete_transient( $this->attempts_key( $ip ) );
    }

    public function check_lockout( $user, $username, $password ) {
        $ip = $this->client_ip();
        if ( get_transient( $this->lockout_key( $ip ) ) ) {
            return new WP_Error( 'too_many_attempts', __( 'Too many failed login attempts. Try again later.', 'infobd-mega' ) );
        }
        return $user;
    }

    public function enforce_strong_password( $errors, $update = null, $user = null ) {
        $password = isset( $_POST['pass1'] ) ? trim( wp_unslash( $_POST['pass1'] ) ) : '';
        if ( empty( $password ) || ( is_wp_error( $errors ) && $errors->get_error_data( 'pass' ) ) ) return $errors;
        if ( strlen( $password ) < 10 || ! preg_match( '/[A-Z]/', $password ) || ! preg_match( '/[a-z]/', $password ) || ! preg_match( '/[0-9]/', $password ) || ! preg_match( '/[\W]/', $password ) ) {
            $errors->add( 'pass', __( '<strong>ERROR</strong>: Password must be at least 10 chars with upper, lower, number, and symbol.', 'infobd-mega' ) );
        }
        return $errors;
    }

    public function block_bad_bots() {
        $bad = array( 'AhrefsBot', 'SemrushBot', 'MJ12bot', 'DotBot', 'PetalBot', 'sqlmap', 'nikto', 'masscan', 'nmap', 'WPScan' );
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        foreach ( $bad as $b ) {
            if ( stripos( $ua, $b ) !== false ) {
                status_header( 403 );
                exit( 'Forbidden' );
            }
        }
    }

    public function security_headers() {
        if ( is_admin() ) return;
        header( 'X-Frame-Options: SAMEORIGIN' );
        header( 'X-Content-Type-Options: nosniff' );
        header( 'Referrer-Policy: strict-origin-when-cross-origin' );
        header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
        header( 'X-XSS-Protection: 1; mode=block' );
        if ( is_ssl() ) {
            header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains' );
        }
    }

    public function mask_login_errors() {
        return __( 'Login failed. Please try again.', 'infobd-mega' );
    }

    public function limit_upload_mimes( $mimes ) {
        // Remove dangerous types
        unset( $mimes['exe'], $mimes['php'], $mimes['phtml'], $mimes['php3'], $mimes['php4'], $mimes['php5'], $mimes['php7'], $mimes['phar'], $mimes['htaccess'] );
        return $mimes;
    }

    public function check_filetype( $info, $file, $filename, $mimes ) {
        if ( preg_match( '/\.(php|phtml|phar|php3|php4|php5|php7|exe|sh|cgi|pl|asp|aspx|js|html|htaccess)$/i', $filename ) ) {
            return array( 'ext' => false, 'type' => false, 'proper_filename' => false );
        }
        return $info;
    }
}
