<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 */
class Publitio_Offloading_Deactivator {

    public static function deactivate() {
        flush_rewrite_rules();
    }

}
