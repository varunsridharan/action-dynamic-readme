<?php
require_once __DIR__ . '/config.php';

require APP_PATH . 'class/class-repo-cloner.php';

$global_template_repository = gh_input( 'GLOBAL_TEMPLATE_REPOSITORY', false );

if ( ! empty( $global_template_repository ) ) {
	gh_log_group_start( 'Setting Up Global Template Repository' );
	gh_log( 'Global Repo : ' . $global_template_repository );

	$matches = extract_src_informaton( $global_template_repository );
	$matches = ( isset( $matches[0] ) ) ? $matches[0] : array();

	if ( ! empty( $matches ) ) {
		$repo_instance = new Repository_Cloner( $matches['login'], $matches['repo'], $matches['branch'] );
		$repo_dir      = $repo_instance->get_path();
		$path          = ( isset( $matches['path'] ) && ! empty( $matches['path'] ) ) ? $matches['path'] : false;

		if ( ( ! empty( $path ) && is_dir( $repo_dir . $path ) ) || is_dir( $repo_instance->get_path() ) ) {
			gh_log( 'Success' );
			$content = ( ! empty( $path ) ) ? $repo_dir . $path : $repo_dir;
			file_put_contents( APP_PATH . 'global-repo', $content );
		} else {
			gh_log_error( 'Unable To Fetch Global Template Repository !' );
		}
	}

	gh_log_group_end();
}

