<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 */
class Publitio_Offloading_Activator {

    public static function activate() {
        flush_rewrite_rules();
    }

}
