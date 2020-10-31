<?php

function extract_src_informaton( $src ) {
	#$regex = '/(?P<login>[A-z0-9_-]+)\/(?P<repo>[A-z0-9_-]+)(?P<branch>@[A-z0-9_-]+|)\/(?P<path>\w.+)/mi';
	$regex = '/(?P<login>[A-z0-9_-]+)\/(?P<repo>[A-z0-9_-]+)(?P<branch>@[A-z0-9_-]+|)(?:\/(?P<path>\w.+)|)/mi';
	preg_match_all( $regex, $src, $matches, PREG_SET_ORDER, 0 );
	return $matches;
}