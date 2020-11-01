<?php

/**
 * Fix Delimeters
 */
$empty_char       = '|';
$variable_search  = '';
$variable_replace = '';
$file_search      = '<!--';
$file_replace     = '<' . $empty_char . '!--';
if ( preg_match( '/^\s*(\S+)\s+(\S+)\s*$/', TEMPLATE_DELIMITER, $matches ) ) {
	$variable_search = ( isset( $matches[1] ) ) ? $matches[1] : false;
	if ( false !== $variable_search ) {
		$variable_replace = str_replace( $variable_search[0], $variable_search[0] . $empty_char, $variable_search );
	}
}

/**
 * @param $src
 *
 * @return mixed
 * @since {NEWVERSION}
 */
function extract_src_informaton( $src ) {
	#$regex = '/(?P<login>[A-z0-9_-]+)\/(?P<repo>[A-z0-9_-]+)(?P<branch>@[A-z0-9_-]+|)\/(?P<path>\w.+)/mi';
	$regex = '/(?P<login>[A-z0-9_-]+)\/(?P<repo>[A-z0-9_-]+)(?P<branch>@[A-z0-9_-]+|)(?:\/(?P<path>\w.+)|)/mi';
	preg_match_all( $regex, $src, $matches, PREG_SET_ORDER, 0 );
	return $matches;
}

/**
 * @param string $content
 * @param bool   $rever_back
 *
 * @return string
 */
function escape_content_to_raw( $content, $rever_back = false ) {
	global $variable_replace, $variable_search, $file_replace, $file_search;
	if ( $rever_back ) {
		$content = str_replace( $variable_replace, $variable_search, $content );
		$content = str_replace( $file_replace, $file_search, $content );
	} else {
		$content = str_replace( $variable_search, $variable_replace, $content );
		$content = str_replace( $file_search, $file_replace, $content );
	}
	return $content;
}