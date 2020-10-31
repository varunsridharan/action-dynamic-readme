<?php
require_once __DIR__ . '/config.php';

$global_template_repository = gh_input( 'GLOBAL_TEMPLATE_REPOSITORY', false );

require APP_PATH . 'vars.php';
require APP_PATH . 'engine/mustache.php';
require APP_PATH . 'class/class-markdown-handler.php';
require APP_PATH . 'class/class-file-handler.php';
require APP_PATH . 'class/class-repo-cloner.php';
require APP_PATH . 'class/class-template-file-handler.php';
require APP_PATH . 'class/class-update-template.php';

$src  = ( isset( $argv[1] ) ) ? $argv[1] : false;
$dest = ( isset( $argv[2] ) ) ? $argv[2] : false;

$instance = new File_Handler( $src, $dest );
$template = new Update_Template( $instance->get_contents(), $instance );
$instance->save( $template->update() );
