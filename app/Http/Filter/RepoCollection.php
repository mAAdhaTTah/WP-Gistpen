<?php
namespace Intraxia\Gistpen\Http\Filter;

use WP_Error;

/**
 * Class Filter\RepoCollection
 *
 * @package Intraxia\Gistpen\Http
 * @subpackage Filters
 */
class RepoCollection extends BaseFilter {
	/**
	 * Generates argument rules.
	 *
	 * Returns an array matching the WP-API format for argument rules,
	 * including sanitization, validation, required, or defaults.
	 *
	 * @return array
	 */
	public function rules() {
		return array(
			'page' => array(
				'required'          => false,
				'default'           => 1,
				'type'              => 'integer',
			),
		);
	}
}
