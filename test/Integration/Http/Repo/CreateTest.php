<?php
namespace Intraxia\Gistpen\Test\Integration\Http\Repo;

use WP_REST_Request;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Test\Integration\TestCase;

class CreateTest extends TestCase {
	public function test_requires_admin() {
		$this->set_role( 'subscriber' );
		$repo    = $this->fm->instance( Repo::class );
		$request = new WP_REST_Request( 'POST', '/intraxia/v1/gistpen/repos' );
		$request->set_body_params( [ 'description' => $repo->description ] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 401 );
	}

	public function test_returns_error_with_invalid_blobs() {
		$this->set_role( 'administrator' );
		$repo    = $this->fm->instance( Repo::class );
		$request = new WP_REST_Request( 'POST', '/intraxia/v1/gistpen/repos' );
		$request->set_body_params( [
			'blobs'       => [ 123 ],
			'description' => $repo->description,
			'status'      => $repo->status,
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 400 );
		$this->assertResponseData( $response, [
			'code'    => 'rest_invalid_param',
			'message' => 'Invalid parameter(s): blobs',
			'data'    => [
				'status' => 400,
				'params' => [
					'blobs' => 'blobs[0] is not of type object.',
				],
			],
		] );
	}

	public function test_returns_error_with_blobs_with_extra_props() {
		$this->set_role( 'administrator' );
		$repo    = $this->fm->instance( Repo::class );
		$blob    = $this->fm->instance( Blob::class );
		$request = new WP_REST_Request( 'POST', '/intraxia/v1/gistpen/repos' );
		$request->set_body_params( [
			'blobs'       => [
				[
					'filename' => $blob->filename,
					'extra'    => 'invalid',
				],
			],
			'description' => $repo->description,
			'status'      => $repo->status,
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 400 );
		$this->assertResponseData( $response, [
			'code'    => 'rest_invalid_param',
			'message' => 'Invalid parameter(s): blobs',
			'data'    => [
				'status' => 400,
				'params' => [
					'blobs' => 'extra is not a valid property of Object.',
				],
			],
		] );
	}

	public function test_returns_error_with_missing_description() {
		$this->set_role( 'administrator' );
		$repo    = $this->fm->instance( Repo::class );
		$request = new WP_REST_Request( 'POST', '/intraxia/v1/gistpen/repos' );
		$request->set_body_params( [
			'status' => $repo->status,
			'blobs'  => [],
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 400 );
		$this->assertResponseData( $response, [
			'code'    => 'rest_missing_callback_param',
			'message' => 'Missing parameter(s): description',
			'data'    => [
				'status' => 400,
				'params' => [ 'description' ],
			],
		] );
	}

	public function test_returns_error_with_invalid_description() {
		$this->set_role( 'administrator' );
		$repo    = $this->fm->instance( Repo::class );
		$request = new WP_REST_Request( 'POST', '/intraxia/v1/gistpen/repos' );
		$request->set_body_params( [
			'description' => 473,
			'status'      => $repo->status,
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 400 );
		$this->assertResponseData( $response, [
			'code'    => 'rest_invalid_param',
			'message' => 'Invalid parameter(s): description',
			'data'    => [
				'status' => 400,
				'params' => [
					'description' => 'description is not of type string.',
				],
			],
		] );
	}

	public function test_returns_error_with_extra_params() {
		$this->set_role( 'administrator' );
		$repo    = $this->fm->instance( Repo::class );
		$request = new WP_REST_Request( 'POST', '/intraxia/v1/gistpen/repos' );
		$request->set_body_params( [
			'description' => $repo->description,
			'status'      => $repo->status,
			'extra'       => 'value',
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 400 );
		$this->assertResponseData( $response, [
			'code'    => 'rest_invalid_param',
			'message' => 'Invalid parameter(s): extra',
			'data'    => [
				'status' => 400,
				'params' => [
					'extra' => 'extra is not a valid request param.',
				],
			],
		] );
	}

	public function test_returns_combines_errors_of_extra_and_invalid_params() {
		$this->set_role( 'administrator' );
		$repo    = $this->fm->instance( Repo::class );
		$request = new WP_REST_Request( 'POST', '/intraxia/v1/gistpen/repos' );
		$request->set_body_params( [
			'description' => 123,
			'status'      => $repo->status,
			'extra'       => 'value',
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 400 );
		$this->assertResponseData( $response, [
			'code'    => 'rest_invalid_param',
			'message' => 'Invalid parameter(s): description, extra',
			'data'    => [
				'status' => 400,
				'params' => [
					'description' => 'description is not of type string.',
					'extra'       => 'extra is not a valid request param.',
				],
			],
		] );
	}

	public function test_creates_repo_with_no_blobs() {
		$this->set_role( 'administrator' );
		$repo    = $this->fm->instance( Repo::class );
		$request = new WP_REST_Request( 'POST', '/intraxia/v1/gistpen/repos' );
		$request->set_body_params( [
			'description' => $repo->description,
			'status'      => $repo->status,
		] );

		$response = $this->server->dispatch( $request );
		$this->assertResponseStatus( $response, 201 );

		$repo = $this->app->make( 'database' )
			->find( Repo::class, $response->get_data()['ID'] );
		$this->assertResponseData( $response, [
			'ID'          => $repo->ID,
			'description' => $repo->description,
			'slug'        => $repo->slug,
			'status'      => $repo->status,
			'password'    => $repo->password,
			'gist_id'     => $repo->gist_id,
			'gist_url'    => $repo->gist_url,
			'sync'        => $repo->sync,
			'blobs'       => [],
			'rest_url'    => $repo->rest_url,
			'commits_url' => $repo->commits_url,
			'html_url'    => $repo->html_url,
			'created_at'  => $repo->created_at,
			'updated_at'  => $repo->updated_at,
		] );
	}

	public function test_creates_repo_with_one_blob() {
		$this->set_role( 'administrator' );
		$repo    = $this->fm->instance( Repo::class );
		$blob    = $this->fm->instance( Blob::class );
		$request = new WP_REST_Request( 'POST', '/intraxia/v1/gistpen/repos' );
		$request->set_body_params( [
			'description' => $repo->description,
			'status'      => $repo->status,
			'blobs'       => [
				[
					'code'     => $blob->code,
					'filename' => $blob->filename,
					'language' => 'js',
				],
			],
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 201 );

		$repo = $this->app->make( 'database' )
			->find( Repo::class, $response->get_data()['ID'], [
				'with' => [
					'blobs' => [
						'with' => 'language',
					],
				],
			] );
		$blob = $repo->blobs->first();
		$this->assertResponseHeader( $response, 'Location', $repo->rest_url );
		$this->assertResponseData( $response, [
			'ID'          => $repo->ID,
			'description' => $repo->description,
			'slug'        => $repo->slug,
			'status'      => $repo->status,
			'password'    => $repo->password,
			'gist_id'     => $repo->gist_id,
			'gist_url'    => $repo->gist_url,
			'sync'        => $repo->sync,
			'blobs'       => [
				[
					'ID'       => $blob->ID,
					'size'     => $blob->size,
					'raw_url'  => $blob->raw_url,
					'edit_url' => $blob->edit_url,
					'filename' => $blob->filename,
					'code'     => $blob->code,
					'language' => [
						'ID'           => $blob->language->ID,
						'display_name' => $blob->language->display_name,
						'slug'         => $blob->language->slug,
					],
				],
			],
			'rest_url'    => $repo->rest_url,
			'commits_url' => $repo->commits_url,
			'html_url'    => $repo->html_url,
			'created_at'  => $repo->created_at,
			'updated_at'  => $repo->updated_at,
		] );
		$this->assertEquals( $blob->language->slug, 'js' );
	}

	public function test_creates_repo_with_one_blob_with_only_filename() {
		$this->set_role( 'administrator' );
		$repo    = $this->fm->instance( Repo::class );
		$blob    = $this->fm->instance( Blob::class );
		$request = new WP_REST_Request( 'POST', '/intraxia/v1/gistpen/repos' );
		$request->set_body_params( [
			'description' => $repo->description,
			'status'      => $repo->status,
			'blobs'       => [
				[
					'filename' => $blob->filename,
				],
			],
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 201 );

		$repo = $this->app->make( 'database' )
			->find( Repo::class, $response->get_data()['ID'], [
				'with' => [
					'blobs' => [
						'with' => 'language',
					],
				],
			] );
		$blob = $repo->blobs->first();
		$this->assertResponseHeader( $response, 'Location', $repo->rest_url );
		$this->assertResponseData( $response, [
			'ID'          => $repo->ID,
			'description' => $repo->description,
			'slug'        => $repo->slug,
			'status'      => $repo->status,
			'password'    => $repo->password,
			'gist_id'     => $repo->gist_id,
			'gist_url'    => $repo->gist_url,
			'sync'        => $repo->sync,
			'blobs'       => [
				[
					'ID'       => $blob->ID,
					'size'     => $blob->size,
					'raw_url'  => $blob->raw_url,
					'edit_url' => $blob->edit_url,
					'filename' => $blob->filename,
					'code'     => '',
					'language' => [
						'ID'           => $blob->language->ID,
						'display_name' => $blob->language->display_name,
						'slug'         => $blob->language->slug,
					],
				],
			],
			'rest_url'    => $repo->rest_url,
			'commits_url' => $repo->commits_url,
			'html_url'    => $repo->html_url,
			'created_at'  => $repo->created_at,
			'updated_at'  => $repo->updated_at,
		] );
		$this->assertEquals( $blob->language->slug, 'plaintext' );
	}
}
