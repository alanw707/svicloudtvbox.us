<?php
/**
 * Allow administrators to upload Android APK installers to the Media Library.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('upload_mimes', function (array $mimes): array {
    if (!current_user_can('manage_options')) {
        return $mimes;
    }

    $mimes['apk'] = 'application/vnd.android.package-archive';

    return $mimes;
});
