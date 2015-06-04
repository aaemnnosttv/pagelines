<?php

/**
 * PL2X (PageLines 2.x) Theme Management
 */
class PL2X_WP_CLI_Command extends WP_CLI_Command
{
	/**
	 * Purge caches
	 *
	 * @subcommand purge
	 */
	function purge()
	{
		delete_transient( 'pagelines_extend_themes' );
		delete_transient( 'pagelines_extend_sections' );
		delete_transient( 'pagelines_extend_plugins' );
		delete_transient( 'pagelines_extend_integrations' );

		pl_purge_section_cache();

		do_action('extend_flush');

		WP_CLI::success('Caches purged!');
	}
}
WP_CLI::add_command( 'pl2x', 'PL2X_WP_CLI_Command' );
