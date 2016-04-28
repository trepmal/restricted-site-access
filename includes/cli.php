<?php

if ( !defined( 'WP_CLI' ) ) return;

/**
 * Restricted Site Access CLI
 */
class Restricted_Site_Access_CLI extends WP_CLI_Command {

	/**
	 * Add IP to whitelist
	 *
	 * ## OPTIONS
	 *
	 * [<ip>...]
	 * : IP address
	 *
	 * ## EXAMPLES
	 *
	 *     wp rsa add-ip 127.0.0.1
	 *
	 * @subcommand add-ip
	 */
	function add_ip( $args, $assoc_args ) {

		$new_input = $old_input = get_option( 'rsa_options', array() );

		$ips = $args;
		$added = array();
		if ( !empty( $ips ) && is_array( $ips ) ) {
			foreach( $ips as $ip_address ) {
				if ( Restricted_Site_Access::is_ip( $ip_address ) ) {
					if ( false === array_search( $ip_address, $new_input['allowed'] ) ) {
						$new_input['allowed'][] = $ip_address;
						$added[] = $ip_address;
					}
				}
			}
		}

		// var_dump( $new_input, $old_input );
		if ( $new_input == $old_input ) {
			WP_CLI::log( 'No changes.' );
		} else {
			WP_CLI::log( 'Added: ' . implode(', ', $added ) );
			update_option( 'rsa_options', $new_input );
		}

	}

	/**
	 * Remove IP from whitelist
	 *
	 * ## OPTIONS
	 *
	 * [<ip>...]
	 * : IP address
	 *
	 * ## EXAMPLES
	 *
	 *     wp rsa remove-ip 127.0.0.1
	 *
	 * @subcommand remove-ip
	 */
	function remove_ip( $args, $assoc_args ) {

		$new_input = $old_input = get_option( 'rsa_options', array() );

		$ips = $args;
		$removed = array();
		if ( !empty( $ips ) && is_array( $ips ) ) {
			foreach( $ips as $ip_address ) {
				if ( false !== ( $key = array_search( $ip_address, $new_input['allowed'] ) ) ) {
					unset( $new_input['allowed'][ $key ] );
					$removed[] = $ip_address;
				}
			}
		}

		// var_dump( $new_input, $old_input );
		if ( $new_input == $old_input ) {
			WP_CLI::log( 'No changes.' );
		} else {
			WP_CLI::log( 'Removed: ' . implode(', ', $removed ) );
			update_option( 'rsa_options', $new_input );
		}

	}

	/**
	 * List whitelisted IPs
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 *     wp rsa list-ips
	 *
	 * @subcommand list-ips
	 */
	function list_ips( $args, $assoc_args ) {

		$rsa_options = get_option( 'rsa_options', array() );

		if ( isset( $rsa_options['allowed'] ) && count( $rsa_options['allowed']) > 0 ) {
			foreach ( $rsa_options['allowed'] as $ip ) {
				WP_CLI::log( $ip );
			}
		} else {
			WP_CLI::log( 'No IPs currently allowed.' );
		}
	}

}

WP_CLI::add_command( 'rsa', 'Restricted_Site_Access_CLI' );