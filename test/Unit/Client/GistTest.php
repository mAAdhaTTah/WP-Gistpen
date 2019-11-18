<?php
namespace Intraxia\Gistpen\Test\Unit\Client;

use Intraxia\Gistpen\Test\Unit\TestCase;

class GistTest extends TestCase {

	/**
	 * @var \Intraxia\Gistpen\Client\Gist
	 */
	private $gist;

	public function setUp() {
		parent::setUp();

		$this->gist = $this->app->fetch( 'client.gist' );
	}

	public function test_all_returns_error_with_no_token() {
		$response = $this->gist->all();

		$this->assertWPError( $response );
	}
}