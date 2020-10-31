<?php

use utilphp\util;

function deep_arrayhandler( $value ) {
	if ( util::is_serialized( $value ) ) {
		$value = util::maybe_unserialize( $value );
	} else {
		$data  = json_decode( $value, true );
		$value = ( JSON_ERROR_NONE === json_last_error() && is_array( $data ) ) ? $data : $value;
	}
	return ( is_array( $value ) ) ? util::array_map_deep( $value, 'deep_arrayhandler' ) : $value;
}

function get_template_vars() {
	$data = array(
		'GITHUB_API_URL'          => isset( $_SERVER['GITHUB_API_URL'] ) ? $_SERVER['GITHUB_API_URL'] : false,
		'GITHUB_REPOSITORY_OWNER' => isset( $_SERVER['GITHUB_REPOSITORY_OWNER'] ) ? $_SERVER['GITHUB_REPOSITORY_OWNER'] : false,
		'GITHUB_ACTIONS'          => isset( $_SERVER['GITHUB_ACTIONS'] ) ? $_SERVER['GITHUB_ACTIONS'] : false,
		'GITHUB_HEAD_REF'         => isset( $_SERVER['GITHUB_HEAD_REF'] ) ? $_SERVER['GITHUB_HEAD_REF'] : false,
		'GITHUB_ACTOR'            => isset( $_SERVER['GITHUB_ACTOR'] ) ? $_SERVER['GITHUB_ACTOR'] : false,
		'GITHUB_REF'              => isset( $_SERVER['GITHUB_REF'] ) ? $_SERVER['GITHUB_REF'] : false,
		'GITHUB_SHA'              => isset( $_SERVER['GITHUB_SHA'] ) ? $_SERVER['GITHUB_SHA'] : false,
		'GITHUB_RUN_ID'           => isset( $_SERVER['GITHUB_RUN_ID'] ) ? $_SERVER['GITHUB_RUN_ID'] : false,
		'GITHUB_SERVER_URL'       => isset( $_SERVER['GITHUB_SERVER_URL'] ) ? $_SERVER['GITHUB_SERVER_URL'] : false,
		'GITHUB_JOB'              => isset( $_SERVER['GITHUB_JOB'] ) ? $_SERVER['GITHUB_JOB'] : false,
		'GITHUB_REPOSITORY'       => isset( $_SERVER['GITHUB_REPOSITORY'] ) ? $_SERVER['GITHUB_REPOSITORY'] : false,
		'GITHUB_EVENT_NAME'       => isset( $_SERVER['GITHUB_EVENT_NAME'] ) ? $_SERVER['GITHUB_EVENT_NAME'] : false,
		'GITHUB_WORKFLOW'         => isset( $_SERVER['GITHUB_WORKFLOW'] ) ? $_SERVER['GITHUB_WORKFLOW'] : false,
		'ENV'                     => $_ENV,
	);

	$data = util::array_map_deep( $data, 'deep_arrayhandler' );

	$data['env'] = $data['ENV'];
	return $data;
}

