<?php

namespace PolylangTcoPro;

class Integration {

	// ------------------------------------------------------------------
	// Data
	// ------------------------------------------------------------------

	public function getLanguages(): array {
		$languages = pll_the_languages( [ 'echo' => 0, 'raw' => 1 ] );
		$default   = $this->defaultLanguage();
		$list      = [];

		foreach ( $languages as $language ) {
			$language['default_lang'] = ( $default === $language['slug'] );
			$list[]                   = $language;
		}

		return $list;
	}

	public function defaultLanguage(): string {
		return pll_default_language();
	}

	public function getWidgets(): array {
		return [
			(object) [ 'title' => __( 'Headers', 'polylang-tcopro' ),         'slug' => 'headers',         'items' => $this->getHeaders() ],
			(object) [ 'title' => __( 'Footers', 'polylang-tcopro' ),         'slug' => 'footers',         'items' => $this->getFooters() ],
			(object) [ 'title' => __( 'Archive layouts', 'polylang-tcopro' ), 'slug' => 'archive_layouts', 'items' => $this->getLayouts( 'archive' ) ],
			(object) [ 'title' => __( 'Single layouts', 'polylang-tcopro' ),  'slug' => 'single_layouts',  'items' => $this->getLayouts( 'single' ) ],
		];
	}

	public function getHeaders(): array {
		return $this->getItems( 'cs_header' );
	}

	public function getFooters(): array {
		return $this->getItems( 'cs_footer' );
	}

	public function getLayouts( string $type = 'single' ): array {
		return $this->getItems( "cs_layout_{$type}" );
	}

	private function getItems( string $type ): array {
		global $wpdb;

		$rows  = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM $wpdb->posts WHERE post_type = %s AND post_status = 'tco-data'",
				$type
			)
		);

		$items = [];
		foreach ( $rows as $row ) {
			$data    = json_decode( $row->post_content );
			$items[] = (object) [
				'ID'          => $row->ID,
				'title'       => $row->post_title,
				'assignments' => $data->settings->assignments,
				'priority'    => $data->settings->assignment_priority,
				'settings'    => $data->settings,
			];
		}

		return $items;
	}

	public function getItemLanguages( int $item_id ): object|false {
		$value = get_post_meta( $item_id, 'polylang_tcopro_language_assignments', true );

		if ( ! $value ) {
			return false;
		}

		return (object) [
			'list'  => explode( '|', $value ),
			'value' => $value,
		];
	}

	// ------------------------------------------------------------------
	// Assignment matching
	// ------------------------------------------------------------------

	private function sortByPriority( object $a, object $b ): int {
		if ( $a->priority === $b->priority ) {
			return $a->ID - $b->ID;
		}
		return $a->priority - $b->priority;
	}

	public function getMatchedItem( array $items ): ?int {
		$matcher      = cornerstone( 'RuleMatching' );
		$current_lang = pll_current_language();
		$matches      = [];

		if ( POLYLANG_TCOPRO_ENV === 'dev' ) {
			error_log( '[plytco] getMatchedItem — current_lang: ' . var_export( $current_lang, true ) );
		}

		foreach ( $items as $item ) {
			$assignments    = array_map( fn( $a ) => (array) $a, $item->assignments );
			$item_languages = $this->getItemLanguages( $item->ID );
			$cs_match       = $matcher->match( $assignments );

			if ( POLYLANG_TCOPRO_ENV === 'dev' ) {
				error_log( sprintf(
					'[plytco] ID %d — cs_match: %s | item_languages: %s',
					$item->ID,
					var_export( $cs_match, true ),
					var_export( $item_languages, true )
				) );
			}

			if ( $cs_match && $item_languages && in_array( $current_lang, $item_languages->list, true ) ) {
				$matches[] = $item;
			}
		}

		if ( count( $matches ) > 1 ) {
			usort( $matches, [ $this, 'sortByPriority' ] );
		}

		$result = ! empty( $matches ) ? $matches[0]->ID : null;

		if ( POLYLANG_TCOPRO_ENV === 'dev' ) {
			error_log( '[plytco] getMatchedItem result: ' . var_export( $result, true ) );
		}

		return $result;
	}

	// ------------------------------------------------------------------
	// Cornerstone filter callbacks
	// ------------------------------------------------------------------

	public function headerAssignment( $match ) {
		if ( is_admin() ) return $match;
		return $this->getMatchedItem( $this->getHeaders() );
	}

	public function footerAssignment( $match ) {
		if ( is_admin() ) return $match;
		return $this->getMatchedItem( $this->getFooters() );
	}

	public function layoutArchiveAssignment( $match ) {
		if ( is_admin() ) return $match;
		return $this->getMatchedItem( $this->getLayouts( 'archive' ) );
	}

	public function layoutSingleAssignment( $match ) {
		if ( is_admin() ) return $match;
		return $this->getMatchedItem( $this->getLayouts( 'single' ) );
	}

	public function languagesProvider( $results ): array {
		return [
			'current'   => pll_current_language( 'slug' ),
			'languages' => $this->getLanguages(),
		];
	}

	// ------------------------------------------------------------------
	// Settings update
	// ------------------------------------------------------------------

	public function update(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$nonce_key = POLYLANG_TCOPRO_NAME . '_update_nonce';
		$nonce_action = POLYLANG_TCOPRO_NAME . '_update';

		if (
			! isset( $_POST[ $nonce_key ] ) ||
			! wp_verify_nonce( sanitize_key( $_POST[ $nonce_key ] ), $nonce_action )
		) {
			return;
		}

		foreach ( $this->getWidgets() as $widget ) {
			foreach ( $widget->items as $item ) {
				$field = "item_{$item->ID}_languages";
				if ( isset( $_POST[ $field ] ) ) {
					update_post_meta(
						$item->ID,
						'polylang_tcopro_language_assignments',
						sanitize_text_field( wp_unslash( $_POST[ $field ] ) )
					);
				}
			}
		}
	}
}
