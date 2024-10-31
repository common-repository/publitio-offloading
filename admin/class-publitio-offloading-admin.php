<?php
/**
 * @package Publitio
 */

require_once PUBLITIO_OFFLOADING_PLUGIN_PATH . '/includes/publitio_api_service.php';

/**
 * Class Admin - handle all plugin changes on admin side
 */
class PWPO_Admin
{
    /**
     * Instance of PublitioApiService class
     */
    private $publitioApi;

    public function __construct()
    {
        $this->publitioApi = new PublitioApiService();
        $this->pwpo_register();
    }

    /**
     * Register all admin actions and filters
     */
    public function pwpo_register()
    {
        add_action('admin_enqueue_scripts', array($this, 'pwpo_enqueue'));
        add_action('admin_menu', array($this, 'pwpo_add_admin_pages'));
        add_filter("plugin_action_links_" . PUBLITIO_OFFLOADING_PLUGIN, array($this, 'pwpo_settings_link'));
        add_action('wp_ajax_pwpo_update_offloading_settings', array($this, 'pwpo_update_offloading_settings'));
        add_action('wp_ajax_pwpo_get_offloading_account_settings', array($this, 'pwpo_get_offloading_account_settings'));
        add_action('wp_ajax_pwpo_update_default_offloading_folder', array($this, 'pwpo_update_default_offloading_folder'));
        add_action('wp_ajax_pwpo_update_default_offloading_cname', array($this, 'pwpo_update_default_offloading_cname'));
        add_action('wp_ajax_pwpo_update_allow_download', array($this, 'pwpo_update_allow_download'));
        add_action('wp_ajax_pwpo_update_offload_templates', array($this, 'pwpo_update_offload_templates'));
        add_action('wp_ajax_pwpo_update_image_offloading_quality', array($this, 'pwpo_update_image_offloading_quality'));
        add_action('wp_ajax_pwpo_update_video_offloading_quality', array($this, 'pwpo_update_video_offloading_quality'));
        add_action('wp_ajax_pwpo_update_files_checkbox', array($this, 'pwpo_update_files_checkbox'));
        add_action('wp_ajax_pwpo_update_delete_checkbox', array($this, 'pwpo_update_delete_checkbox'));
        add_action('wp_ajax_pwpo_get_media_list', array($this, 'pwpo_get_media_list'));
        add_action('wp_ajax_pwpo_sync_media_file', array($this, 'pwpo_sync_media_file'));
        add_action('wp_ajax_pwpo_update_replace_media', array($this, 'pwpo_update_replace_media'));
        add_action('wp_ajax_pwpo_get_media_list_for_delete', array($this, 'pwpo_get_media_list_for_delete'));
        add_action('wp_ajax_pwpo_delete_media_file', array($this, 'pwpo_delete_media_file'));
    }

    /**
     * Add Publitio Offloading option in Dashboard menu
     */
    public function pwpo_add_admin_pages()
    {
        add_menu_page(
            'Publitio Offloading',
            'Publitio Offloading',
            'manage_options',
            'publitio_offloading',
            array($this, 'pwpo_admin_index'),
            PUBLITIO_OFFLOADING_PLUGIN_URL . 'admin/images/cloud-icon.png',
            110
        );
    }

    /**
     * Get page for plugin settings
     */
    public function pwpo_admin_index()
    {
        require_once PUBLITIO_OFFLOADING_PLUGIN_PATH . 'admin/partials/admin_offloading_settings.php';
    }

    /**
     * Register all styles and scripts
     */
    public function pwpo_enqueue()
    {
        if (isset( $_GET['page'] ) && $_GET['page'] == 'publitio_offloading' ) {
            wp_enqueue_style('offloadingstyle', PUBLITIO_OFFLOADING_PLUGIN_URL . 'admin/css/offloading-style.css');
            wp_enqueue_script('offloadingscripts', PUBLITIO_OFFLOADING_PLUGIN_URL . 'admin/js/offloading-script.js', array('jquery'));
        }
    }

    /**
     * Add settings link
     */
    public function pwpo_settings_link($links)
    {
        $settings_link = '<a href="admin.php?page=publitio_offloading">Settings</a>';
        array_push($links, $settings_link);
        return $links;
    }

    /**
     * Update plugin settings
     */
    public function pwpo_update_offloading_settings()
    {
        if (isset($_POST['api_key']) && isset($_POST['api_secret'])) {
            $api_key = sanitize_text_field($_POST['api_key']);
            $api_secret = sanitize_text_field($_POST['api_secret']);
            $this->publitioApi->init($api_key, $api_secret);
        }

    }

    /**
     * Get account settings
     */
    public function pwpo_get_offloading_account_settings()
    {
        $response = $this->publitioApi->get_publitio_account_settins();
        if ($response) {
            wp_send_json([
                'status' => 200,
                'folders' => $response->folders,
                'cnames' => $response->cnames,
                'default_folder_id' => get_option('publitio_offloading_default_folder'),
                'default_cname_url' => get_option('publitio_offloading_default_cname'),
                'allow_download' => get_option('publitio_offloading_allow_download'),
                'image_quality' => get_option('publitio_offloading_image_quality'),
                'video_quality' => get_option('publitio_offloading_video_quality'),
                'image_checkbox' => get_option('publitio_offloading_image_checkbox'),
                'video_checkbox' => get_option('publitio_offloading_video_checkbox'),
                'audio_checkbox' => get_option('publitio_offloading_audio_checkbox'),
                'document_checkbox' => get_option('publitio_offloading_document_checkbox'),
                'delete_checkbox' => get_option('publitio_offloading_delete_checkbox'),
                'replace_checkbox' => get_option('publitio_offloading_replace_checkbox')
            ]);
        } else {
            wp_send_json([
                'folders' => null,
                'cnames' => null,
                'default_folder_id' => '',
                'default_cname_url' => '',
                'allow_download' => '',
                'image_quality' => '',
                'video_quality' => '',
                'image_checkbox' => '',
                'video_checkbox' => '',
                'audio_checkbox' => '',
                'document_checkbox' => '',
                'delete_checkbox' => '',
                'replace_checkbox' => ''
            ]);
        }
    }

    /**
     * Update default folder
     */
    public function pwpo_update_default_offloading_folder()
    {
        if (isset($_POST['folder_id'])) {
            $this->publitioApi->set_default_offloading_folder(sanitize_text_field($_POST['folder_id']));
        }
    }

    /**
     * Update default cname
     */
    public function pwpo_update_default_offloading_cname()
    {
        if (isset($_POST['cname_url'])) {
            $this->publitioApi->set_default_offloading_cname(sanitize_text_field($_POST['cname_url']));
        }
    }

    /**
     * Update allow download option
     */
    public function pwpo_update_allow_download()
    {
        if (isset($_POST['allow'])) {
            $this->publitioApi->set_allow_download_offloading(sanitize_text_field($_POST['allow']));
        }
    }

    /**
     * Update offload templates option
     */
    public function pwpo_update_offload_templates()
    {
        if (isset($_POST['allow'])) {
            $this->publitioApi->set_offload_templates(sanitize_text_field($_POST['allow']));
        }
    }

    /**
     * Update image quality
     */
    public function pwpo_update_image_offloading_quality()
    {
        if (isset($_POST['image_quality'])) {
            $this->publitioApi->set_offloading_image_quality(sanitize_text_field($_POST['image_quality']));
        }
    }

    /**
     * Update video quality
     */
    public function pwpo_update_video_offloading_quality()
    {
        if (isset($_POST['video_quality'])) {
            $this->publitioApi->set_offloading_video_quality(sanitize_text_field($_POST['video_quality']));
        }
    }

    /**
     * Update checkbox to define which files should be offloaded
     */
    public function pwpo_update_files_checkbox()
    {
        if (isset($_POST['id']) && isset($_POST['value'])) {
            $this->publitioApi->set_files_checkbox(sanitize_text_field($_POST['id']), sanitize_text_field($_POST['value']));
        }
    }

    /**
     * Update checkbox to define which is delete from publitio is allowed
     */
    public function pwpo_update_delete_checkbox()
    {
        if (isset($_POST['delete_checkbox'])) {
            $this->publitioApi->set_delete_checkbox(sanitize_text_field($_POST['delete_checkbox']));
        }
    }

    /**
     * Return list of media objects
     */
    public function pwpo_get_media_list()
    {
        $attachments = $this->publitioApi->get_media_for_sync();
        wp_send_json([
            'media' => $attachments
        ]);
    }

    /**
     * Synchronize media file with Publitio
     */
    public function pwpo_sync_media_file()
    {
        if (isset($_POST['attach_id'])) {
            $this->publitioApi->syncMedia(sanitize_text_field($_POST['attach_id']));
        }
    }

    /**
     * Set up flag for replace all media with Publitio media
     */
    public function pwpo_update_replace_media()
    {
        if (isset($_POST['replace_checkbox'])) {
            $this->publitioApi->set_replace_checkbox(sanitize_text_field($_POST['replace_checkbox']));
        }
    }

    /**
     * Return list of media objects that needs to be deleted
     */
    public function pwpo_get_media_list_for_delete() {
        $attachments = $this->publitioApi->get_undeleted_attachments();
        wp_send_json([
            'media' => $attachments
        ]);
    }

    /**
     * Remove media file from uploads folder
     */
    public function pwpo_delete_media_file()
    {
        if (isset($_POST['attach_id'])) {
            $this->publitioApi->deleteAtachment(sanitize_text_field($_POST['attach_id']));
        }
    }

}