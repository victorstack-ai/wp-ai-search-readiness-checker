<?php
use PHPUnit\Framework\TestCase;
use AISearchReadiness\Checker;

// Mock WordPress functions if not defined
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/../');
}
if (!function_exists('add_action')) {
    function add_action($tag, $callback) {}
}
if (!function_exists('register_setting')) {
    function register_setting($option_group, $option_name) {}
}

class CheckerTest extends TestCase {
    public function test_get_checks_structure() {
        // We need to mock the internal methods that call WP functions
        $checker = $this->getMockBuilder(Checker::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['check_content_structure', 'check_answer_early', 'check_schema_markup', 'check_eeat_signals', 'check_performance', 'check_robots_txt', 'check_llms_txt', 'check_internal_links', 'check_sitemaps'])
            ->getMock();

        $checker->method('check_content_structure')->willReturn(['status' => 'pass', 'label' => '1', 'message' => 'ok']);
        $checker->method('check_answer_early')->willReturn(['status' => 'pass', 'label' => '2', 'message' => 'ok']);
        $checker->method('check_schema_markup')->willReturn(['status' => 'pass', 'label' => '4', 'message' => 'ok']);
        $checker->method('check_eeat_signals')->willReturn(['status' => 'pass', 'label' => '5', 'message' => 'ok']);
        $checker->method('check_performance')->willReturn(['status' => 'pass', 'label' => '6', 'message' => 'ok']);
        $checker->method('check_robots_txt')->willReturn(['status' => 'pass', 'label' => '7', 'message' => 'ok']);
        $checker->method('check_llms_txt')->willReturn(['status' => 'pass', 'label' => '7b', 'message' => 'ok']);
        $checker->method('check_internal_links')->willReturn(['status' => 'pass', 'label' => '8', 'message' => 'ok']);
        $checker->method('check_sitemaps')->willReturn(['status' => 'pass', 'label' => '9', 'message' => 'ok']);

        $checks = $checker->get_checks();
        $this->assertIsArray($checks);
        $this->assertArrayHasKey('content_structure', $checks);
        $this->assertEquals('pass', $checks['content_structure']['status']);
    }
}
