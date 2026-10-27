<?php
/** Template Name: Retired Downloads */
defined('ABSPATH') || exit;
status_header(410);
nocache_headers();
header('X-Robots-Tag: noindex, nofollow');
get_header();
echo '<main id="main-content" class="page-shell"><h1>This resource is no longer available.</h1></main>';
get_footer();
