<?php

namespace Intraxia\Gistpen\View;

use Intraxia\Gistpen\Contract\Translations as TranslationsContract;
use Intraxia\Jaxion\Contract\Core\HasActions;

/**
 * Maintains & outputs all the translations used by the FE.
 *
 * @package    Intraxia\Gistpen
 * @subpackage View
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      2.0.0
 */
class Translations implements TranslationsContract, HasActions {

	/**
	 * Translations.
	 *
	 * @var array
	 */
	protected $translations = array();

	/**
	 * Translations constructor.
	 */
	public function __construct() {
		$this->translations = array(
			'editor.commits'     => __( 'View Commits', 'wp-gistpen' ),
			'editor.shortcode'   => __( 'Copy shortcode', 'wp-gistpen' ),
			'editor.delete'      => __( 'Delete', 'wp-gistpen' ),
			'editor.description' => __( 'Description...', 'wp-gistpen' ),
			'editor.file.add'    => __( 'Add File', 'wp-gistpen' ),
			'editor.gist'        => __( 'View on Gist', 'wp-gistpen' ),
			'editor.invisibles'  => __( 'Enable Invisibles?', 'wp-gistpen' ),
			'editor.return'      => __( 'Return to Editor', 'wp-gistpen' ),
			'editor.saving'      => __( 'Saving Gistpen...', 'wp-gistpen' ),
			'editor.status'      => __( 'Post Status', 'wp-gistpen' ),
			'editor.sync'        => __( 'Sync Gistpen with Gist?', 'wp-gistpen' ),
			'editor.tabs'        => __( 'Enable Tabs?', 'wp-gistpen' ),
			'editor.theme'       => __( 'Theme', 'wp-gistpen' ),
			'editor.update'      => __( 'Update Gistpen', 'wp-gistpen' ),
			'editor.width'       => __( 'Indentation width (in spaces)', 'wp-gistpen' ),
			/* translators: %s: Translation key. */
			'i18n.notfound'      => __( 'Translation for key %s not found.', 'wp-gistpen' ),
			'jobs.description'   => __( 'Job Description', 'wp-gistpen' ),
			'jobs.dispatch'      => __( 'Dispatch Job', 'wp-gistpen' ),
			'jobs.loading'       => __( 'Loading...', 'wp-gistpen' ),
			'jobs.name'          => __( 'Job Name', 'wp-gistpen' ),
			'jobs.runs'          => __( 'Job Runs', 'wp-gistpen' ),
			'jobs.runs.view'     => __( 'View Runs', 'wp-gistpen' ),
			'jobs.status'        => __( 'Job Status', 'wp-gistpen' ),
			'jobs.title'         => __( 'Background Jobs', 'wp-gistpen' ),
			/* translators: %s: Route key. */
			'route.404'          => __( 'Route %s not found', 'wp-gistpen' ),
			'run.id'             => __( 'Run ID', 'wp-gistpen' ),
			'run.status'         => __( 'Run Status', 'wp-gistpen' ),
			'run.scheduled'      => __( 'Run Scheduled At', 'wp-gistpen' ),
			'run.started'        => __( 'Run Started At', 'wp-gistpen' ),
			'run.finished'       => __( 'Run Finished At', 'wp-gistpen' ),
			'run.messages'       => __( 'Run Messages', 'wp-gistpen' ),
			'run.messages.view'  => __( 'View Messages', 'wp-gistpen' ),
			'search.invalid'     => __( 'Please enter a valid search term.', 'wp-gistpen' ),
			'search.loading'     => __( 'Loading Gistpens...', 'wp-gistpen' ),
			/* translators: %s: Search term. */
			'search.results.no'  => __( 'No results found for term %s', 'wp-gistpen' ),
			'search.term.no'     => __( 'Please enter a search term ', 'wp-gistpen' ),
			'search.title'       => __( 'Search Gistpens', 'wp-gistpen' ),
			'settings.saving'    => __( 'Saving settings...', 'wp-gistpen' ),
		);
	}

	/**
	 * Get the translation string for the key.
	 *
	 * @param  string $key Translation key.
	 *
	 * @return string     Translation string.
	 */
	public function translate( $key ) {
		if ( isset( $this->translations[ $key ] ) ) {
			return $this->translations[ $key ];
		}

		return sprintf(
			$this->translations['i18n.notfound'],
			$key
		);
	}

	/**
	 * Serializes the model's public data into an array.
	 *
	 * @return array
	 */
	public function serialize() {
		return $this->translations;
	}

	/**
	 * Output the script tag with the translations used by the FE.
	 */
	public function output_translations() {
		echo '<script type="application/javascript">';
		echo 'window.__GISTPEN_I18N__ = ' . wp_json_encode( $this->serialize() );
		echo '</script>';
	}

	/**
	 * Provides the array of actions the class wants to register with WordPress.
	 *
	 * These actions are retrieved by the Loader class and used to register the
	 * correct service methods with WordPress.
	 *
	 * @return array[]
	 */
	public function action_hooks() {
		return array(
			array(
				'hook'     => 'admin_enqueue_scripts',
				'method'   => 'output_translations',
				'priority' => 5,
			),
		);
	}
}
