<?php

/**
 * Plugin Name:    Transform Image Plugin
 * Description:    Take a image and difficault level and transform it into pixel art
 * Version:        1.0
 * Author:         Liki Crus
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

require 'pixelart_lib.php';

if (!class_exists('TransferImagePlugin')) :

    class TransferImagePlugin
    {

        public static $_instance;

        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function init()
        {
            add_action('admin_menu', array($this, 'plugin_admin_page'));
            add_shortcode('pixelart', array($this, "add_transform_img_shortcode"));

            add_action('wp_enqueue_scripts', array($this, 'pa_load_scripts'));

            add_action('wp_ajax_image_submission', array($this, 'pa_image_submission_cb'));
            add_action('wp_ajax_nopriv_image_submission', array($this, 'pa_image_submission_cb'));

            add_filter('admin_init', array($this, 'check_upload_dir_change'), 999);
        }

        public function pa_load_scripts()
        {
            if (!wp_script_is('jquery', 'enqueued')) {
                wp_enqueue_script('jquery');
            }

            wp_enqueue_script('pa_script', plugin_dir_url(__FILE__) . 'assets/js/pa_script.js');

            $data = array(
                'upload_url' => admin_url('async-upload.php'),
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('image-submission')
            );

            wp_localize_script('pa_script', 'pa_config', $data);
        }


        function check_upload_dir_change()
        {
            global $pagenow;

            if (!empty($_POST['action'] && $_POST['action'] == 'image_submission') && ('admin-ajax.php' == $pagenow)) {
                add_filter('upload_dir', array($this, 'set_pa_upload_dir'));
            }
        }

        function set_pa_upload_dir($upload)
        {
            $upload['subdir'] = '/pixelarts' . $upload['subdir'];
            $upload['path'] = $upload['basedir'] . $upload['subdir'];
            $upload['url'] = $upload['baseurl'] . $upload['subdir'];
            return $upload;
        }

        function pa_image_submission_cb()
        {

            check_ajax_referer('image-submission');

            $file_id = "async-upload";
            $upload_overrides = array('test_form' => false);
            $upload_result = wp_handle_upload($_FILES[$file_id], $upload_overrides);

            if (isset($upload_result['error'])) {
                wp_send_json_error(array('msg' => 'Validation failed. Please try again later.'));
            }

            # Creating the pixel art now. 
            $file = $upload_result['file'];
            $path_info = pathinfo($file);

            $output_filename = $path_info['filename'] . "_pa";
            $output_file = $path_info['dirname'] . "/" . $output_filename;

            $box_count = intval($_REQUEST['box_count']);
            $box_size = floatval($_REQUEST['box_size']);

            $output_file_gen = create_fixed_pixel_art($file, $output_file, $box_count, $box_size);

            $url = str_replace($path_info['basename'], $output_file_gen, $upload_result['url']);

            remove_filter('upload_dir', array($this, 'set_pa_upload_dir'));

            wp_send_json_success(array(
                'msg' => 'Your image uploaded successfully.',
                'url' => $url,
                //'path' => $upload_result['file'],   // NOTE: Should be removed on server.
            ));

        }

        public function plugin_admin_page()
        {
            // Admin functions
        }

        // Shortcut function
        public static function add_transform_img_shortcode($atts, $content = '')
        {
            ob_start();
            include_once '_form.php';
            $html = ob_get_contents();
            ob_end_clean();

            return $html;
        }
    }

    TransferImagePlugin::instance()->init();

endif;