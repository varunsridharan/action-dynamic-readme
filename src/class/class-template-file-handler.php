<?php

/**
 * Class Template_File_Handler
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
class Template_File_Handler extends File_Handler {
	/**
	 * @var bool|string
	 */
	protected $parent_file = false;

	/**
	 * Template_File_Handler constructor.
	 *
	 * @param $src
	 */
	public function __construct( $src, $parent_file = false ) {
		$src = trim( $src, ' ' );
		parent::__construct( $src, false );
		$this->parent_file = ( ! empty( $parent_file ) ) ? dirname( $parent_file ) . '/' : false;
		$this->src         = $this->extract_src_details();
	}

	/**
	 * Retrives Contents.
	 *
	 * @return false|string
	 * @since {NEWVERSION}
	 */
	public function get_contents() {
		return ( ! empty( $this->src ) && file_exists( $this->src ) ) ? file_get_contents( $this->src ) : false;
	}

	/**
	 * Extracts Repository Info.
	 *
	 * @since {NEWVERSION}
	 */
	protected function extract_src_details() {
		$matches = extract_src_informaton( $this->src );
		$matches = ( isset( $matches[0] ) ) ? $matches[0] : array();

		if ( empty( $matches ) || ( isset( $matches['branch'] ) && empty( $matches['branch'] ) ) || ! isset( $matches['branch'] ) ) {
			/**
			 * Checks for file inside the parent file's directory
			 */

			if ( ! empty( $this->parent_file ) && file_exists( $this->parent_file . $this->src ) ) {
				gh_log( 'File Found : ' . $this->parent_file . $this->src );
				return $this->parent_file . $this->src;
			}

			/**
			 * Checks for file inside Current Repository
			 */
			if ( file_exists( WORK_DIR . $this->src ) ) {
				gh_log( 'File Found : ' . WORK_DIR . $this->src );
				return WORK_DIR . $this->src;
			}

			/**
			 * Checks for file in global template repository
			 */
			if ( ! empty( GLOBAL_REPO_PATH ) && file_exists( GLOBAL_REPO_PATH . $this->src ) ) {
				gh_log( 'File Found : ' . GLOBAL_REPO_PATH . $this->src );
				return GLOBAL_REPO_PATH . $this->src;
			}
		}

		if ( isset( $matches['branch'] ) && ! empty( $matches['branch'] ) ) {
			$repo_instance = new Repository_Cloner( $matches['login'], $matches['repo'], $matches['branch'] );
			if ( file_exists( $repo_instance->get_path() . $matches['path'] ) ) {
				return $repo_instance->get_path() . $matches['path'];
			} else {
				gh_log_error( ' File Not Found ! class-template-file-handler.php#' . __LINE__ );
				return false;
			}
		}

		gh_log_error( 'Unable To Find File Any Where !! Please Check The File Location ' );
		gh_log_error( print_r( array_filter( array(
			'Parent File : ' => $this->parent_file,
			'Lookup File : ' => $this->src,
		) ), true ) );

		return $this->src;
	}

	/**
	 * Returns SRC
	 *
	 * @return bool|string
	 */
	public function get_src() {
		return $this->src;
	}
}