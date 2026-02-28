<?php
/**
 * Plugin Name: AI Search Readiness Checker
 * Description: Reviews your WordPress site's readiness for AI-powered search engines based on 9 key steps.
 * Version: 1.0.0
 * Author: VictorStackAI
 * License: GPL-2.0-or-later
 * Text Domain: ai-search-readiness-checker
 */

namespace AISearchReadiness;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AI_SEARCH_READINESS_VERSION', '1.0.0' );

add_action( 'init', function () {
	load_plugin_textdomain( 'ai-search-readiness-checker', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
} );

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

new Checker();
