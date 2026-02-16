<?php
/**
 * Plugin Name: AI Search Readiness Checker
 * Description: Reviews your WordPress site's readiness for AI-powered search engines based on 9 key steps.
 * Version: 1.0.0
 * Author: VictorStackAI
 * License: GPL-2.0+
 */

namespace AISearchReadiness;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

new Checker();
