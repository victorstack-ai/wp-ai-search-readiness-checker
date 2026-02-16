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
			'AI Search Readiness',
			'AI Search Readiness',
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
			<h1>AI Search Readiness Checker</h1>
			<p>Check how well your site is prepared for AI search engines like Perplexity, Gemini, and ChatGPT Search.</p>
			
			<table class="widefat striped">
				<thead>
					<tr>
						<th>Step</th>
						<th>Status</th>
						<th>Details</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $this->get_checks() as $id => $check ) : ?>
						<tr>
							<td><strong><?php echo esc_html( $check['label'] ); ?></strong></td>
							<td>
								<?php if ( $check['status'] === 'pass' ) : ?>
									<span style="color: green;">✅ Pass</span>
								<?php elseif ( $check['status'] === 'warn' ) : ?>
									<span style="color: orange;">⚠️ Warning</span>
								<?php else : ?>
									<span style="color: red;">❌ Fail</span>
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
			return [ 'label' => '1. Content Structure', 'status' => 'warn', 'message' => 'No posts found to analyze structure.' ];
		}
		
		foreach ( $posts as $post ) {
			if ( strpos( $post->post_content, '<h2' ) === false && strpos( $post->post_content, '<h3' ) === false ) {
				return [ 'label' => '1. Content Structure', 'status' => 'warn', 'message' => 'Recent posts are missing H2/H3 subheadings.' ];
			}
		}

		return [ 'label' => '1. Content Structure', 'status' => 'pass', 'message' => 'Recent posts use a logical heading structure.' ];
	}

	public function check_answer_early() {
		return [ 'label' => '2. Answer Early', 'status' => 'warn', 'message' => 'Ensure your first 100 words provide a direct answer to the user\'s likely question.' ];
	}

	public function check_schema_markup() {
		if ( class_exists( 'WPSEO_Options' ) || class_exists( 'RankMath' ) ) {
			return [ 'label' => '4. Schema Markup', 'status' => 'pass', 'message' => 'SEO plugin detected; likely handling Schema markup.' ];
		}
		return [ 'label' => '4. Schema Markup', 'status' => 'warn', 'message' => 'No dedicated SEO plugin detected for structured data.' ];
	}

	public function check_eeat_signals() {
		$users = get_users( [ 'capability' => 'publish_posts', 'number' => 1 ] );
		if ( ! empty( $users ) && ! empty( get_the_author_meta( 'description', $users[0]->ID ) ) ) {
			return [ 'label' => '5. E-E-A-T Signals', 'status' => 'pass', 'message' => 'Author bios are configured.' ];
		}
		return [ 'label' => '5. E-E-A-T Signals', 'status' => 'warn', 'message' => 'Author bios are missing; critical for demonstrating expertise.' ];
	}

	public function check_performance() {
		if ( function_exists( 'wp_cache_get' ) ) {
			return [ 'label' => '6. Performance', 'status' => 'pass', 'message' => 'Caching is active.' ];
		}
		return [ 'label' => '6. Performance', 'status' => 'warn', 'message' => 'Performance optimizations (caching) not detected.' ];
	}

	public function check_robots_txt() {
		$robots_path = ABSPATH . 'robots.txt';
		if ( file_exists( $robots_path ) ) {
			$content = file_get_contents( $robots_path );
			if ( stripos( $content, 'GPTBot' ) !== false || stripos( $content, 'CCBot' ) !== false ) {
				return [ 'label' => '7. Robots.txt', 'status' => 'pass', 'message' => 'AI crawlers are explicitly mentioned.' ];
			}
		}
		return [ 'label' => '7. Robots.txt', 'status' => 'warn', 'message' => 'Robots.txt doesn\'t explicitly handle AI crawlers.' ];
	}

	public function check_llms_txt() {
		$llms_path = ABSPATH . 'llms.txt';
		if ( file_exists( $llms_path ) ) {
			return [ 'label' => '7b. LLMS.txt', 'status' => 'pass', 'message' => 'llms.txt found.' ];
		}
		return [ 'label' => '7b. LLMS.txt', 'status' => 'warn', 'message' => 'llms.txt not found. Recommended for modern AI discovery.' ];
	}

	public function check_internal_links() {
		return [ 'label' => '8. Internal Linking', 'status' => 'warn', 'message' => 'Manually verify that your content uses semantic internal links.' ];
	}

	public function check_sitemaps() {
		if ( function_exists( 'wp_get_sitemap_index_url' ) && wp_get_sitemap_index_url() ) {
			return [ 'label' => '9. Sitemaps', 'status' => 'pass', 'message' => 'XML Sitemaps are enabled.' ];
		}
		return [ 'label' => '9. Sitemaps', 'status' => 'warn', 'message' => 'Ensure XML sitemaps are submitted to search consoles.' ];
	}
}

new Checker();
