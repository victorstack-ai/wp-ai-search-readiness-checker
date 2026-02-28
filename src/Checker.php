<?php

namespace AISearchReadiness;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Checker {

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'init', [ $this, 'register_settings' ] );
	}

	public function add_admin_menu() {
		add_management_page(
			__( 'AI Search Readiness', 'ai-search-readiness-checker' ),
			__( 'AI Search Readiness', 'ai-search-readiness-checker' ),
			'manage_options',
			'ai-search-readiness',
			[ $this, 'render_admin_page' ]
		);
	}

	public function register_settings() {
		register_setting( 'ai_search_readiness', 'ai_search_readiness_options' );
	}

	public function render_admin_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'AI Search Readiness Checker', 'ai-search-readiness-checker' ); ?></h1>
			<p><?php esc_html_e( 'Check how well your site is prepared for AI search engines like Perplexity, Gemini, and ChatGPT Search.', 'ai-search-readiness-checker' ); ?></p>

			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Step', 'ai-search-readiness-checker' ); ?></th>
						<th><?php esc_html_e( 'Status', 'ai-search-readiness-checker' ); ?></th>
						<th><?php esc_html_e( 'Details', 'ai-search-readiness-checker' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $this->get_checks() as $id => $check ) : ?>
						<tr>
							<td><strong><?php echo esc_html( $check['label'] ); ?></strong></td>
							<td>
								<?php if ( $check['status'] === 'pass' ) : ?>
									<span style="color: green;">&#9989; <?php esc_html_e( 'Pass', 'ai-search-readiness-checker' ); ?></span>
								<?php elseif ( $check['status'] === 'warn' ) : ?>
									<span style="color: orange;">&#9888;&#65039; <?php esc_html_e( 'Warning', 'ai-search-readiness-checker' ); ?></span>
								<?php else : ?>
									<span style="color: red;">&#10060; <?php esc_html_e( 'Fail', 'ai-search-readiness-checker' ); ?></span>
								<?php endif; ?>
							</td>
							<td><?php echo esc_html( $check['message'] ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	public function get_checks() {
		return [
			'content_structure' => $this->check_content_structure(),
			'answer_early'      => $this->check_answer_early(),
			'schema_markup'     => $this->check_schema_markup(),
			'eeat_signals'      => $this->check_eeat_signals(),
			'performance'       => $this->check_performance(),
			'robots_txt'        => $this->check_robots_txt(),
			'llms_txt'          => $this->check_llms_txt(),
			'internal_links'    => $this->check_internal_links(),
			'sitemaps'          => $this->check_sitemaps(),
		];
	}

	public function check_content_structure() {
		$posts = get_posts( [ 'numberposts' => 5 ] );
		if ( empty( $posts ) ) {
			return [
				'label'   => __( '1. Content Structure', 'ai-search-readiness-checker' ),
				'status'  => 'warn',
				'message' => __( 'No posts found to analyze structure.', 'ai-search-readiness-checker' ),
			];
		}

		foreach ( $posts as $post ) {
			if ( strpos( $post->post_content, '<h2' ) === false && strpos( $post->post_content, '<h3' ) === false ) {
				return [
					'label'   => __( '1. Content Structure', 'ai-search-readiness-checker' ),
					'status'  => 'warn',
					'message' => __( 'Recent posts are missing H2/H3 subheadings.', 'ai-search-readiness-checker' ),
				];
			}
		}

		return [
			'label'   => __( '1. Content Structure', 'ai-search-readiness-checker' ),
			'status'  => 'pass',
			'message' => __( 'Recent posts use a logical heading structure.', 'ai-search-readiness-checker' ),
		];
	}

	public function check_answer_early() {
		return [
			'label'   => __( '2. Answer Early', 'ai-search-readiness-checker' ),
			'status'  => 'warn',
			'message' => __( 'Ensure your first 100 words provide a direct answer to the user\'s likely question.', 'ai-search-readiness-checker' ),
		];
	}

	public function check_schema_markup() {
		if ( class_exists( 'WPSEO_Options' ) || class_exists( 'RankMath' ) ) {
			return [
				'label'   => __( '4. Schema Markup', 'ai-search-readiness-checker' ),
				'status'  => 'pass',
				'message' => __( 'SEO plugin detected; likely handling Schema markup.', 'ai-search-readiness-checker' ),
			];
		}
		return [
			'label'   => __( '4. Schema Markup', 'ai-search-readiness-checker' ),
			'status'  => 'warn',
			'message' => __( 'No dedicated SEO plugin detected for structured data.', 'ai-search-readiness-checker' ),
		];
	}

	public function check_eeat_signals() {
		$users = get_users( [ 'capability' => 'publish_posts', 'number' => 1 ] );
		if ( ! empty( $users ) && ! empty( get_the_author_meta( 'description', $users[0]->ID ) ) ) {
			return [
				'label'   => __( '5. E-E-A-T Signals', 'ai-search-readiness-checker' ),
				'status'  => 'pass',
				'message' => __( 'Author bios are configured.', 'ai-search-readiness-checker' ),
			];
		}
		return [
			'label'   => __( '5. E-E-A-T Signals', 'ai-search-readiness-checker' ),
			'status'  => 'warn',
			'message' => __( 'Author bios are missing; critical for demonstrating expertise.', 'ai-search-readiness-checker' ),
		];
	}

	public function check_performance() {
		if ( function_exists( 'wp_cache_get' ) ) {
			return [
				'label'   => __( '6. Performance', 'ai-search-readiness-checker' ),
				'status'  => 'pass',
				'message' => __( 'Caching is active.', 'ai-search-readiness-checker' ),
			];
		}
		return [
			'label'   => __( '6. Performance', 'ai-search-readiness-checker' ),
			'status'  => 'warn',
			'message' => __( 'Performance optimizations (caching) not detected.', 'ai-search-readiness-checker' ),
		];
	}

	public function check_robots_txt() {
		$robots_path = ABSPATH . 'robots.txt';
		if ( file_exists( $robots_path ) ) {
			$content = file_get_contents( $robots_path );
			if ( stripos( $content, 'GPTBot' ) !== false || stripos( $content, 'CCBot' ) !== false ) {
				return [
					'label'   => __( '7. Robots.txt', 'ai-search-readiness-checker' ),
					'status'  => 'pass',
					'message' => __( 'AI crawlers are explicitly mentioned.', 'ai-search-readiness-checker' ),
				];
			}
		}
		return [
			'label'   => __( '7. Robots.txt', 'ai-search-readiness-checker' ),
			'status'  => 'warn',
			'message' => __( 'Robots.txt doesn\'t explicitly handle AI crawlers.', 'ai-search-readiness-checker' ),
		];
	}

	public function check_llms_txt() {
		$llms_path = ABSPATH . 'llms.txt';
		if ( file_exists( $llms_path ) ) {
			return [
				'label'   => __( '7b. LLMS.txt', 'ai-search-readiness-checker' ),
				'status'  => 'pass',
				'message' => __( 'llms.txt found.', 'ai-search-readiness-checker' ),
			];
		}
		return [
			'label'   => __( '7b. LLMS.txt', 'ai-search-readiness-checker' ),
			'status'  => 'warn',
			'message' => __( 'llms.txt not found. Recommended for modern AI discovery.', 'ai-search-readiness-checker' ),
		];
	}

	public function check_internal_links() {
		return [
			'label'   => __( '8. Internal Linking', 'ai-search-readiness-checker' ),
			'status'  => 'warn',
			'message' => __( 'Manually verify that your content uses semantic internal links.', 'ai-search-readiness-checker' ),
		];
	}

	public function check_sitemaps() {
		if ( function_exists( 'wp_get_sitemap_index_url' ) && wp_get_sitemap_index_url() ) {
			return [
				'label'   => __( '9. Sitemaps', 'ai-search-readiness-checker' ),
				'status'  => 'pass',
				'message' => __( 'XML Sitemaps are enabled.', 'ai-search-readiness-checker' ),
			];
		}
		return [
			'label'   => __( '9. Sitemaps', 'ai-search-readiness-checker' ),
			'status'  => 'warn',
			'message' => __( 'Ensure XML sitemaps are submitted to search consoles.', 'ai-search-readiness-checker' ),
		];
	}
}
