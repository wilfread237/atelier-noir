<?php
/**
 * @package atelier-noir
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class MediaTest extends TestCase {

	public function test_accepts_direct_mp4_url(): void {
		$url = 'https://cdn.example.com/hero.mp4';
		$this->assertTrue( atelier_noir_is_allowed_video_url( $url ) );
		$this->assertSame( $url, atelier_noir_resolve_video_url( $url ) );
	}

	public function test_accepts_youtube_url(): void {
		$url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
		$this->assertTrue( atelier_noir_is_allowed_video_url( $url ) );
		$this->assertSame( $url, atelier_noir_resolve_video_url( $url ) );
	}

	public function test_rejects_pexels_html_page(): void {
		$url = 'https://www.pexels.com/video/woman-in-fashion-3740510/';
		$this->assertFalse( atelier_noir_is_allowed_video_url( $url ) );
		$this->assertSame( '', atelier_noir_resolve_video_url( $url ) );
	}

	public function test_rejects_empty_url(): void {
		$this->assertFalse( atelier_noir_is_allowed_video_url( '' ) );
		$this->assertSame( '', atelier_noir_resolve_video_url( '' ) );
	}
}
