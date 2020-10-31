<?php
require_once __DIR__ . '/mustache-class.php';

function dynamic_readme_mustache_engine( $content ) {
	$m = new Mustache_Engine( array(
		'delimiters' => TEMPLATE_DELIMITER,
		'escape'     => function ( $value ) {
			#return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
			return ( ! is_array( $value ) ) ? htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ) : $value;
		},
	) );
	return $m->render( $content, get_template_vars() );
}