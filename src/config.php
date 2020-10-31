<?php
require_once '/gh-toolkit/php.php';

$delimeter   = gh_input( 'DELIMITER', '${{ }}' );
$delimeter   = ( ! empty( $delimeter ) ) ? $delimeter : '${{ }}';
$global_path = false;

define( 'APP_PATH', __DIR__ . '/' );
define( 'WORK_DIR', gh_env( 'GITHUB_WORKSPACE', '/github/workspace' ) . '/' );
define( 'TEMPLATE_REPO_PATH', '/dynamic-readme-tmp/repos/' );
define( 'TEMPLATE_ENGINE', gh_input( 'TEMPLATE_ENGINE', 'mustache' ) );
define( 'TEMPLATE_DELIMITER', $delimeter );

if ( file_exists( APP_PATH . 'global-repo' ) ) {
	$global_path = file_get_contents( APP_PATH . 'global-repo' );
}

define( 'GLOBAL_REPO_PATH', $global_path );

require_once APP_PATH . 'functions.php';
