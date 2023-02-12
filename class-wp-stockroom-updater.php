<?php

if ( class_exists( 'WP_Stockroom_Updater' ) ) {
	return; //Prevent multiple includes.
}

/**
 * Class WP_Stockroom_Updater.
 *
 * phpcs:disable WordPress.NamingConventions
 */
class WP_Stockroom_Updater {

	/**
	 * Version of this updater script.
	 */
	const VERSION = '1.0.0';

	/**
	 * The endpoint where to look for
	 */
	const REST_ROUTE = 'wp-stockroom/v1/package';

	/**
	 * Get update data for the plugin. This only preps the data, updating itself is done later.
	 *
	 * @param false|array $update       The plugin/theme update data with the latest details. Default false.
	 * @param array       $current_data Plugin/Theme headers.
	 * @param string      $package_file Plugin/Theme filename.
	 * @param array       $locales      Installed locales to look translations for. Currently not implemented.
	 *
	 * @return false|array
	 */
	public static function check_update( $update, $current_data, $package_file, $locales ) {
		if ( false !== $update ) {
			return $update; // Update instructions are already set.
		}

		// The name of the directory of the plugin/theme currently being checked.
		$package_slug = explode( '/', $package_file )[0];
		// The external repository could have a different plugin slug, here is a way to interject.

		/**
		 * Change the slug for the external stockroom url that is being checked.
		 *
		 * @param string $package_slug The current slug of the theme/plugin that is being checked.
		 * @param array  $current_data Details of the plugin/theme being checked.
		 */
		$remote_package_slug = apply_filters( 'wp_stockroom_updater_slug', $package_slug, $current_data );
		$endpoint            = 'https://' . $current_data['UpdateURI'] . '/wp-json/' . self::REST_ROUTE . '/' . $remote_package_slug . '/';
		$external_data       = self::call_endpoint( $endpoint );
		if ( is_wp_error( $external_data ) ) {
			// translators: The plugin/theme name.
			$message = sprintf( __( 'An error occurred while checking for updates for %s' ), "<i>{$current_data['Name']}</i>" );
			$message .= "<br/>\n" . __( 'If this error persists, contact support.' ) . "<br/>\n<strong>" . __( 'Error details are:' ) . '</strong>';
			$error   = new \WP_Error( "update_error_{$package_slug}", $message );
			$error->merge_from( $external_data );
			wp_die( $error );
		}

		if ( version_compare( $current_data['Version'], $external_data->version, '>=' ) ) {
			return false; // remote version is the same, or even smaller.
		}

		$update_data = array(
			'id'           => $endpoint,
			'slug'         => $package_slug,
			'version'      => $external_data->version,
			'package'      => $external_data->package_link,
			'tested'       => $external_data->wp_tested_version,
			'requires_php' => $external_data->php_version,
		);

		// Last change to change some update details.

		/**
		 * Give a chance to change the update details of the plugin/theme.
		 *
		 * @param string $package_slug The current slug of the theme/plugin that is being checked.
		 * @param array  $current_data Details of the plugin/theme being checked.
		 */
		return apply_filters( 'wp_stockroom_updater_data', $update_data, $current_data, $external_data, $package_slug );
	}

	/**
	 * The package endpoint where the update details are kept.
	 *
	 * @param string $url The full url to the package rest endpoint.
	 *
	 * @return stdClass|WP_Error The package details, or an error.
	 */
	protected static function call_endpoint( $url ) {
		// Get the readme file.
		$r = wp_remote_get( $url );

		// Was the call successfully?
		if ( is_wp_error( $r ) ) {
			return $r;
		}

		$response_code = wp_remote_retrieve_response_code( $r );
		// Only accept 200 range.
		if ( $response_code < 200 | $response_code >= 300 ) {
			$body = json_decode( wp_remote_retrieve_body( $r ) );
			if ( ! empty( $body->message ) ) {
				return new \WP_Error( $response_code, $body->message );
			}

			return new \WP_Error( $response_code, 'Invalid HTTP status code: ' . $response_code );

		}

		$body = wp_remote_retrieve_body( $r );

		if ( empty( $body ) ) {
			return new \WP_Error( $response_code, 'Empty response.' );
		}

		return json_decode( $body );
	}
}

