<?php
/**
 * @package atelier-noir
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ServicesTest extends TestCase {

	public function test_parse_services_json_returns_normalized_rows(): void {
		$json = wp_json_encode(
			array(
				array(
					'id'    => 'fashion-consulting',
					'title' => 'Fashion Consulting',
					'text'  => 'Strategy and image.',
					'video' => 'https://cdn.example.com/svc.mp4',
					'image' => 'https://cdn.example.com/poster.jpg',
				),
			)
		);

		$list = atelier_noir_parse_services_json( $json );

		$this->assertCount( 1, $list );
		$this->assertSame( 'fashion-consulting', $list[0]['id'] );
		$this->assertSame( '01', $list[0]['number'] );
		$this->assertSame( 'video', $list[0]['type'] );
	}

	public function test_sanitize_services_json_strips_invalid_rows(): void {
		$raw = wp_json_encode(
			array(
				array( 'title' => '', 'id' => 'empty' ),
				array( 'title' => 'Events', 'id' => 'events', 'text' => 'Launches.' ),
			)
		);

		$clean = atelier_noir_sanitize_services_json( $raw );
		$data  = json_decode( $clean, true );

		$this->assertIsArray( $data );
		$this->assertCount( 1, $data );
		$this->assertSame( 'events', $data[0]['id'] );
	}

	public function test_defaults_provide_three_services(): void {
		$this->assertCount( 3, atelier_noir_services_defaults() );
	}
}
