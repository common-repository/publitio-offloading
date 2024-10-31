<?php
/**
 * Trigger file on Plugin uninstall
 *
 * @package Publitio
 */

if( ! defined('WP_UNINSTALL_PLUGIN') ) {
    exit;
}

delete_option('publitio_offloading_key');
delete_option('publitio_offloading_secret');
delete_option('publitio_offloading_default_folder');
delete_option('publitio_offloading_default_cname');
delete_option('publitio_offloading_allow_download');
delete_option('publitio_offloading_image_quality');
delete_option('publitio_offloading_video_quality');
delete_option('publitio_offloading_image_checkbox');
delete_option('publitio_offloading_video_checkbox');
delete_option('publitio_offloading_audio_checkbox');
delete_option('publitio_offloading_document_checkbox');
delete_option('publitio_offloading_replace_checkbox');
delete_option('publitio_offloading_offload_templates');
delete_option('publitio_offloading_delete_checkbox');
