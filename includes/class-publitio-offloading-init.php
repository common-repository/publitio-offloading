<?php


/**
 * @package Publitio
 */

require_once PUBLITIO_OFFLOADING_PLUGIN_PATH.'/admin/class-publitio-offloading-admin.php';
require_once PUBLITIO_OFFLOADING_PLUGIN_PATH . '/includes/class-publitio-offloading.php';

class PWPO_Init
{
    /**
     * Get all classes and store them inside array
     * @return array
     */
    public static function pwpo_get_services()
    {
        return [
            new PWPO_Admin(),
            new PWPO_Offload()
        ];
    }
}