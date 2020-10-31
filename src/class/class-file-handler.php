<?php

/**
 * Class File_Handler
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
class File_Handler {
	/**
	 * @var bool|string
	 */
	protected $src = false;

	/**
	 * @var bool
	 */
	protected $dest = false;

	/**
	 * File_Handler constructor.
	 *
	 * @param      $src
	 * @param bool $dest
	 */
	public function __construct( $src, $dest = false ) {
		#$this->src  = trim( $src, './' );
		$this->src = $src;
		if ( false !== strpos( $this->src, './' ) ) {
			$this->src = ltrim( $this->src, './' );
		}
		$this->dest = $dest;
	}

	/**
	 * Retrives Contents.
	 *
	 * @return false|string
	 */
	public function get_contents() {
		if ( file_exists( WORK_DIR . $this->src ) ) {
			return @file_get_contents( WORK_DIR . $this->src );
		}
		gh_log_error( sprintf( '%s File Not Found In Location', WORK_DIR . $this->src ) );
		return false;
	}

	/**
	 * @param $content
	 */
	public function save( $content ) {
		@file_put_contents( WORK_DIR . $this->dest, $content );
	}

	/**
	 * Returns SRC
	 *
	 * @return bool|string
	 */
	public function get_basedir() {
		return WORK_DIR . $this->src;
	}
}