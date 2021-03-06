<?php
namespace Intraxia\Gistpen\Test\Unit\Model;

use Intraxia\Jaxion\Contract\Axolotl\EntityManager as EM;
use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\Test\Unit\TestCase;
use WP_Query;

class LanguageTest extends TestCase {
	/**
	 * @var EntityManager
	 */
	protected $database;

	/**
	 * @var int
	 */
	protected $language;

	public function setUp() {
		parent::setUp();

		$this->database = $this->app->make( EM::class );
		$this->language = wp_insert_term( 'js', 'wpgp_language' );
	}

	public function test_repo_should_have_correct_properties() {
		/** @var Language $language */
		$language = $this->database->find( Language::class, $this->language['term_id'] );

		$this->assertSame( $this->language['term_id'], $language->ID );
		$this->assertSame( 'js', $language->slug );
		$this->assertSame( 'JavaScript', $language->display_name );
	}
}
