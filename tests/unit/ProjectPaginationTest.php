<?php
/**
 * @package atelier-noir
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ProjectPaginationTest extends TestCase {

	public function test_single_page_when_four_or_fewer_posts(): void {
		$this->assertSame( 1, atelier_noir_project_total_pages( 0 ) );
		$this->assertSame( 1, atelier_noir_project_total_pages( 4 ) );
	}

	public function test_second_page_when_more_than_four_posts(): void {
		$this->assertSame( 2, atelier_noir_project_total_pages( 5 ) );
		$this->assertSame( 2, atelier_noir_project_total_pages( 8 ) );
		$this->assertSame( 3, atelier_noir_project_total_pages( 9 ) );
	}
}
