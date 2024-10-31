<?php
/**
 * @package Publitio
 */
require_once PUBLITIO_OFFLOADING_PLUGIN_PATH . '/includes/class-publitio-offloading-auth-service.php';


/**
 * Class PublitioApiService for handling Publitio Api calls and responses
 */
class PublitioApiService
{
    /**
     * Instance of PublitioApiService class
     */
    private $publitio_api;

    public function __construct()
    {
        if (PWPO_AuthService::is_user_authenticated()) {
            $key = PWPO_AuthService::get_key();
            $secret = PWPO_AuthService::get_secret();
            $this->publitio_api = new \Publitio\API($key, $secret);
        } else {
            $this->publitio_api = NULL;
        }
    }

    /**
     * Init Publitio Api
     * @param $api_key
     * @param $api_secret
     */
    public function init($api_key, $api_secret)
    {
        $this->publitio_api = new \Publitio\API($api_key, $api_secret);
        PWPO_AuthService::add_credentials($api_key, $api_secret);
        $this->check_credentials();
    }

    /**
     * Get list of account settings for Publitio account
     * @return object
     */
    public function get_publitio_account_settins()
    {
        if (PWPO_AuthService::is_user_authenticated()) {
            $resp = $this->publitio_api->call('/folders/tree', 'GET');
            $cnames = $this->publitio_api->call('/cnames/list', 'GET', array(
                        'wpo' => true
                    ));
            $resp->cnames = $cnames->cnames;

            //if no default cname set, make it no 1
            $default_cname = get_option('publitio_offloading_default_cname');
            if(!$default_cname) {
                //test
                $cname_url = $cnames->cnames[0]->url;
                update_option('publitio_offloading_default_cname', esc_url_raw($cname_url));
            }            

            return $resp;
        } else {
            return false;
        }
    }

    /**
     * Set default folder in settings page
     * @param $folder_id
     */
    public function set_default_offloading_folder($folder_id)
    {
        if (PWPO_AuthService::is_user_authenticated()) {
            update_option('publitio_offloading_default_folder', sanitize_text_field($folder_id));
            wp_send_json([
                'status' => 200,
                'folder_id' => esc_html($folder_id)
            ]);
        }
    }

    /**
     * Set default cname in settings page
     * @param $cname_url
     */
    public function set_default_offloading_cname($cname_url)
    {
        if (PWPO_AuthService::is_user_authenticated()) {
            update_option('publitio_offloading_default_cname', esc_url_raw($cname_url));
            wp_send_json([
                'status' => 200,
                'default_cname_url' => esc_url($cname_url)
            ]);
        }
    }

    /**
     * Set value for allow download option on setting page
     * @param $allow
     */
    public function set_allow_download_offloading($allow)
    {
        if (PWPO_AuthService::is_user_authenticated()) {
            if ($allow === "true") {
                $option = 'yes';
            } else {
                $option = 'no';
            }
            update_option('publitio_offloading_allow_download', sanitize_text_field($option));
            wp_send_json([
                'status' => 200,
                'allow_download' => esc_html($option)
            ]);
        }
    }

    /**
     * Set value for allow download option on setting page
     * @param $allow
     */
    public function set_offload_templates($allow)
    {
        if (PWPO_AuthService::is_user_authenticated()) {
            if ($allow === "true") {
                $option = 'yes';
            } else {
                $option = 'no';
            }
            update_option('publitio_offloading_offload_templates', sanitize_text_field($option));
            wp_send_json([
                'status' => 200,
                'offload_templates' => esc_html($option)
            ]);
        }
    }

    /**
     * Set value image quality (50,60,70,80(default),90,100)
     * @param $image_quality
     */
    public function set_offloading_image_quality($image_quality)
    {
        if (PWPO_AuthService::is_user_authenticated()) {
            update_option('publitio_offloading_image_quality', sanitize_text_field($image_quality));
            wp_send_json([
                'status' => 200,
                'image_quality' => esc_html($image_quality)
            ]);
        }
    }

    /**
     * Set value video quality (360p,480p,720p,1080p)
     * @param $video_quality
     */
    public function set_offloading_video_quality($video_quality)
    {
        if (PWPO_AuthService::is_user_authenticated()) {
            update_option('publitio_offloading_video_quality', sanitize_text_field($video_quality));
            wp_send_json([
                'status' => 200,
                'video_quality' => esc_html($video_quality)
            ]);
        }
    }

    /**
     * Set checkbox option for each type of media (image,video,audio,document)
     * @param $id
     * @param $value
     */
    public function set_files_checkbox($id, $value)
    {
        if (PWPO_AuthService::is_user_authenticated()) {
            if ($value === "true") {
                $option = 'yes';
            } else {
                $option = 'no';
            }
            update_option('publitio_offloading_' . $id, $option);
            wp_send_json([
                'status' => 200,
                $id => esc_html($option)
            ]);
        }
    }

    /**
     * Set checkbox option for each type of media (image,video,audio,document)
     * @param $value
     */
    public function set_delete_checkbox($value)
    {
        if (PWPO_AuthService::is_user_authenticated()) {
            if ($value === "true") {
                $option = 'yes';
            } else {
                $option = 'no';
            }
            update_option('publitio_offloading_delete_checkbox', $option);
            wp_send_json([
                'status' => 200,
                'delete_checkbox' => esc_html($option)
            ]);
        }
    }

    /**
     * Set checkbox option for automatically replacing media url with Publitio URL
     * @param $value
     */
    public function set_replace_checkbox($value)
    {
        if (PWPO_AuthService::is_user_authenticated()) {
            if ($value === "true") {
                $option = 'yes';
            } else {
                $option = 'no';
            }
            update_option('publitio_offloading_replace_checkbox', $option);
            wp_send_json([
                'status' => 200,
                'replace_checkbox' => esc_html($option)
            ]);
        }
    }

    /**
     * Check if Publitio file exists, if not upload it again and update meta data for attachment
     * @param $attcID
     */
    public function syncMedia($attcID)
    {
        $attachment = get_post($attcID);
        $publitioMeta = $this->getPublitioMeta($attachment);
        if (!is_null($publitioMeta)) {
            $responseShow = $this->showFile($publitioMeta['id']);
            if ($responseShow->success !== true) {
                if (delete_post_meta($attachment->ID, 'publitioMeta')) {
                    $publitioMetaData = $this->getPublitioMeta($attachment);
                    if (!is_null($publitioMetaData)) {
                        wp_send_json([
                            'sync' => true
                        ]);
                    } else {
                        wp_send_json([
                            'sync' => false,
                            'guid' => $attachment->guid
                        ]);
                    }
                }
                wp_send_json([
                    'sync' => false,
                    'guid' => $attachment->guid
                ]);
            }
            else {
                if($responseShow->url_preview !== $publitioMeta['publitio_url']) {
                    $publitioMeta['publitio_url'] = $responseShow->url_preview;
                    if($responseShow->folder && !is_null($responseShow->folder)) {
                        $publitioMeta['folder_name'] = $responseShow->folder;
                    } else {
                        unset($publitioMeta['folder_name']);
                    }
                    update_post_meta($attachment->ID, 'publitioMeta', $publitioMeta);
                }

                wp_send_json([
                    'sync' => true
                ]);
            }
        }
    }

    /**
     * Function for upload image to Publitio Dashboard
     * @param $attachment
     * @return array|null
     */
    public function uploadFile($attachment)
    {
        $args = array(
            'public_id' => $attachment->post_name,
            'wp_url' => $attachment->guid
        );
        $folder = get_option('publitio_offloading_default_folder');
        if ($folder && !empty($folder)) {
            $args['folder'] = $folder;
        }
        $attach = get_attached_file($attachment->ID);
        if (file_exists($attach)) {
            $responseUpload = $this->publitio_api->uploadFile(fopen($attach, 'r'), 'file', $args);
            if ($responseUpload->success === true) {
                if($this->isVideoType($responseUpload->extension)) {
                    $ext = 'mp4';
                } else if ($this->isAudioType($responseUpload->extension)) {
                    $ext = 'mp3';
                } else {
                    $ext = $responseUpload->extension;
                }
                $publitioMeta = array(
                    'id' => $responseUpload->id,
                    'publitio_url' => $responseUpload->url_preview,
                    'public_id' => $responseUpload->public_id,
                    'extension' => $ext
                );
                if ($folder) {
                    $publitioMeta['folder_name'] = $responseUpload->folder;
                }
                update_post_meta($attachment->ID, 'publitioMeta', $publitioMeta);
                return $publitioMeta;
            }
        }
        return null;
    }

    /**
     * Delete file from Publitio when it is permanently deleted from Media folder in Wordpress
     * @param $id
     */
    public function deleteFileFromPublitio($id)
    {
        $this->publitio_api->call('/files/delete/' . $id, 'DELETE');
    }

    /**
     * Check if file exists on Publitio Dashboard
     * @param $id
     * @return mixed
     */
    public function showFile($id) {
        return $responseShow = $this->publitio_api->call("/files/show/" . $id, "GET");
    }

    /**
     * Remove file from upload folder, but leave media object in database
     * @param $id
     * @param bool $response
     */
    public function deleteAtachment($id, $response = true)
    {
        $meta = wp_get_attachment_metadata($id);
        $attach = get_attached_file($id);
        $pi = pathinfo($attach);

        $img_dirname = $pi['dirname'];
        if(file_exists($attach)) {
            $sizes = $meta['sizes'];
            if ($sizes) {
                foreach ($sizes as $size) {
                    $filename = $size['file'];
                    $img_size_url = $img_dirname . "/" . $filename;
                    wp_delete_file($img_size_url);
                }
            }
        }

        wp_delete_file($attach);
        if ($response === true) {
            if (!file_exists($attach)) {
                wp_send_json([
                    'deleted' => true
                ]);
            } else {
                wp_send_json([
                    'deleted' => false
                ]);
            }
        }
    }

    /**
     * Return Publitio URL with url transformations
     * @param $dimensions - size of image
     * @param $publitioMeta
     * @return string
     */
    public function getTransformedUrl($dimensions, $publitioMeta)
    {
        if (is_null($publitioMeta)) {
            return null;
        }
        $media_url = PUBLITIO_OFFLOADING_PUBLITIO_MEDIA;
        if (get_option('publitio_offloading_default_cname')) {
            $media_url = get_option('publitio_offloading_default_cname') . '/file/';
        }
        if (is_null($dimensions)) {
            if ($this->isImageType($publitioMeta['extension'])) {
                $qualityImage = get_option('publitio_offloading_image_quality') ? get_option('publitio_offloading_image_quality') : '80';
                $publitio_url = $media_url . ($qualityImage ? 'q_' . $qualityImage . '/' : '') . (isset($publitioMeta['folder_name']) ? $publitioMeta['folder_name'] : '') . $publitioMeta['public_id'] . '.' . $publitioMeta['extension'];
            } elseif ($this->isVideoType($publitioMeta['extension'])) {
                $qualityVideo = get_option('publitio_offloading_video_quality') ? get_option('publitio_offloading_video_quality') : '480';
                $publitio_url = $media_url . ($qualityVideo ? 'h_' . $qualityVideo . '/' : '') . (isset($publitioMeta['folder_name']) ? $publitioMeta['folder_name'] : '') . $publitioMeta['public_id'] . '.' . $publitioMeta['extension'];
            } else {
                $publitio_url = $media_url . (isset($publitioMeta['folder_name']) ? $publitioMeta['folder_name'] : '') . $publitioMeta['public_id'] . '.' . $publitioMeta['extension'];
            }
        } else {
            if(isset($dimensions['width'])) {
                $dimensions['width'] = round($dimensions['width']);
            }
            if(isset($dimensions['height'])) {
                $dimensions['height'] = round($dimensions['height']);
            }
            if ($this->isImageType($publitioMeta['extension'])) {
                $qualityImage = get_option('publitio_offloading_image_quality') ? get_option('publitio_offloading_image_quality') : '80';
                $publitio_url = $media_url . 'w_' . $dimensions['width'] . ',' . (isset($dimensions['height']) ? 'h_' . $dimensions['height'] . ',' : '') . $dimensions['crop'] . ($qualityImage ? ',q_' . $qualityImage : '') . '/' . (isset($publitioMeta['folder_name']) ? $publitioMeta['folder_name'] : '') . $publitioMeta['public_id'] . '.' . $publitioMeta['extension'];
            } else {
                $publitio_url = $media_url . 'w_' . $dimensions['width'] . ',' . (isset($dimensions['height']) ? 'h_' . $dimensions['height'] . ',' : '') . $dimensions['crop'] . '/' . (isset($publitioMeta['folder_name']) ? $publitioMeta['folder_name'] : '') . $publitioMeta['public_id'] . '.' . $publitioMeta['extension'];
            }
        }
        $publitio_url = preg_replace('/\s/', '', $publitio_url);
        return $publitio_url;
    }

    /**
     * Get all media that need synchronization with Publitio
     * @return array
     */
    public function get_media_for_sync() {
        $args = array('post_type' => 'attachment',
            'post_status' => 'null',
            'posts_per_page' => -1);
        $attachments = get_posts($args);
        $mediaList = array();
        foreach ($attachments as $attachment) {
            $add_to_array = true;
            if (get_option('publitio_offloading_image_checkbox') && get_option('publitio_offloading_image_checkbox') === 'no') {
                if (strpos($attachment->post_mime_type, 'image') !== false ) {
                    $add_to_array = false;
                }
            }

            if (get_option('publitio_offloading_video_checkbox') && get_option('publitio_offloading_video_checkbox') === 'no') {
                if (strpos($attachment->post_mime_type, 'video') !== false ) {
                    $add_to_array = false;
                }
            }

            if (get_option('publitio_offloading_audio_checkbox') && get_option('publitio_offloading_audio_checkbox') === 'no') {
                if (strpos($attachment->post_mime_type, 'audio') !== false ) {
                    $add_to_array = false;
                }
            }

            if (get_option('publitio_offloading_document_checkbox') && get_option('publitio_offloading_document_checkbox') === 'no') {
                if (strpos($attachment->post_mime_type, 'pdf') !== false ) {
                    $add_to_array = false;
                }
            }
            if ($add_to_array === true) {
                array_push($mediaList, $attachment);
            }
        }

        return array_chunk($mediaList,1);
    }

    /**
     * Get all media that are in upload folder and have Publitio URL
     * @return array
     */
    public function get_undeleted_attachments()
    {
        $args = array('post_type' => 'attachment',
            'post_status' => 'inherit',
            'posts_per_page' => -1);
        $attachments = get_posts($args);
        $undelete = array();
        foreach ($attachments as $attachment) {
            $attach = get_attached_file($attachment->ID);
            $publitioMeta = get_post_meta($attachment->ID, 'publitioMeta', true);
            if (file_exists($attach) && $publitioMeta && !is_null($publitioMeta)) {
                $filetype = wp_check_filetype($attachment->guid);
                $add_to_array = true;
                if (get_option('publitio_offloading_image_checkbox') && get_option('publitio_offloading_image_checkbox') === 'no') {
                    if ($this->isImageType($filetype['ext'])) {
                        $add_to_array = false;
                    }
                }

                if (get_option('publitio_offloading_video_checkbox') && get_option('publitio_offloading_video_checkbox') === 'no') {
                    if ($this->isVideoType($filetype['ext'])) {
                        $add_to_array = false;
                    }
                }

                if (get_option('publitio_offloading_audio_checkbox') && get_option('publitio_offloading_audio_checkbox') === 'no') {
                    if ($this->isAudioType($filetype['ext'])) {
                        $add_to_array = false;
                    }
                }

                if (get_option('publitio_offloading_document_checkbox') && get_option('publitio_offloading_document_checkbox') === 'no') {
                    if ($this->isDocumentType($filetype['ext'])) {
                        $add_to_array = false;
                    }
                }
                if ($add_to_array === true) {
                    array_push($undelete, $attachment);
                }
            }
        }

        return $undelete;
    }

    /**
     * Get size information for all currently-registered image sizes.
     * @return array $sizes Data for all currently-registered image sizes.
     */
    public function _get_all_image_sizes()
    {
        global $_wp_additional_image_sizes;
        $image_sizes = array();

        foreach ( get_intermediate_image_sizes() as $_size ) {
            if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
                $image_sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
                $image_sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
                $image_sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
            } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
                $image_sizes[ $_size ] = array(
                    'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
                    'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                    'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
                );
            }
        }
        return $image_sizes;
    }

    /**
     * Check if attachment type is image
     * @param $type
     * @return bool
     */
    public function isImageType($type)
    {
        $image_types = array('jpg', 'jpeg', 'jpe', 'png', 'gif', 'bmp', 'psd', 'webp', 'ai', 'tif', 'tiff', 'svg');
        return in_array($type, $image_types);
    }

    /**
     * Check if attachment type is video
     * @param $type
     * @return bool
     */
    public function isVideoType($type)
    {
        $video_types = array('mp4', 'webm', 'ogv', 'avi', 'mov', 'flv', '3gp', '3g2', 'wmv', 'mpeg', 'mkv');
        return in_array($type, $video_types);
    }

    /**
     * Check if attachment type is pdf
     * @param $type
     * @return bool
     */
    public function isAudioType($type)
    {
        $audio_types = array('mp3', 'wav', 'ogg', 'aac', 'aiff', 'amr', 'ac3', 'au', 'flac', 'm4a', 'aac', 'ra', 'voc', 'wma');
        return in_array($type, $audio_types);
    }

    /**
     * Check if attachment type is pdf
     * @param $type
     * @return bool
     */
    public function isDocumentType($type)
    {
        $doc_types = array('pdf');
        return in_array($type, $doc_types);
    }

    /**
     * Try to get list of folders with api key and api secret
     */
    private function check_credentials()
    {
        $response = $this->get_publitio_account_settins();
        $this->handle_response($response);
    }

    /**
     * Handle response from Publitio API
     * @param $response
     */
    private function handle_response($response)
    {
        if (!$response->success && $response->error->code === 401) {
            $this->handle_unauthorized();
        } else if (!$response->success) {
            $this->handle_error();
        }
        $this->handle_success($response);
    }

    /**
     * When user is unauthorized remove credentials
     */
    private function handle_unauthorized()
    {
        PWPO_AuthService::delete_credentials();
        wp_send_json(['status' => 401]);
    }

    /**
     * When error remove credentials
     */
    private function handle_error()
    {
        PWPO_AuthService::delete_credentials();
        wp_send_json(['status' => 500]);
    }

    /**
     * When success handle response
     * @param $response
     */
    private function handle_success($response)
    {
        delete_option('publitio_offloading_default_folder');
        update_option('publitio_offloading_allow_download', 'yes');
        delete_option('publitio_offloading_default_cname');
        delete_option('publitio_offloading_delete_checkbox');
        update_option('publitio_offloading_allow_download', 'yes');
        update_option('publitio_offloading_image_quality', '80');
        update_option('publitio_offloading_video_quality', '480');
        update_option('publitio_offloading_delete_checkbox', 'no');
        delete_option('publitio_offloading_replace_checkbox');
        update_option('publitio_offloading_image_checkbox', 'yes');
        update_option('publitio_offloading_video_checkbox', 'yes');
        update_option('publitio_offloading_audio_checkbox', 'yes');
        update_option('publitio_offloading_document_checkbox', 'yes');
        update_option('publitio_offloading_offload_templates', 'yes');


        wp_send_json([
            'status' => 200,
            'folders' => $response->folders,
            'cnames' => $response->cnames,
            'default_folder_id' => get_option('publitio_offloading_default_folder'),
            'default_cname_url' => get_option('publitio_offloading_default_cname'),
            'allow_download' => get_option('publitio_offloading_allow_download'),
            'offload_templates' => get_option('publitio_offloading_offload_templates'),
            'image_quality' => get_option('publitio_offloading_image_quality'),
            'video_quality' => get_option('publitio_offloading_video_quality'),
            'image_checkbox' => get_option('publitio_offloading_image_checkbox'),
            'video_checkbox' => get_option('publitio_offloading_video_checkbox'),
            'audio_checkbox' => get_option('publitio_offloading_audio_checkbox'),
            'document_checkbox' => get_option('publitio_offloading_document_checkbox'),
            'delete_checkbox' => get_option('publitio_offloading_delete_checkbox'),
            'replace_checkbox' => get_option('publitio_offloading_replace_checkbox')
        ]);
    }


    /**
     * Get Publitio Meta Data for attachment
     * @param $attachment
     * @return array|mixed|null
     */
    private function getPublitioMeta($attachment)
    {
        $publitioMeta = get_post_meta($attachment->ID, 'publitioMeta', true);
        if (!$publitioMeta) {
            $publitioMeta = $this->uploadFile($attachment);
            if (is_null($publitioMeta)) {
                return null;
            }
        }
        return $publitioMeta;
    }
}
