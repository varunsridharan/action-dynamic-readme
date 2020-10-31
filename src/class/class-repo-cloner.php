<?php

/**
 * Class Repository_Cloner
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
class Repository_Cloner {

	/**
	 * @var bool|string
	 */
	protected $username = false;

	/**
	 * @var bool|string
	 */
	protected $repository = false;

	/**
	 * @var bool|string
	 */
	protected $branch = false;

	/**
	 * Repository_Cloner constructor.
	 *
	 * @param string $username Github Username
	 * @param string $repository Github Repository Name
	 * @param string $branch Github Repository Branch Name
	 */
	public function __construct( $username = '', $repository = '', $branch = 'default' ) {
		$this->username   = $username;
		$this->repository = $repository;
		$this->branch     = ( empty( $branch ) ) ? 'default' : $branch;
		$this->branch     = str_replace( '@', '', $this->branch );
		$basepath         = TEMPLATE_REPO_PATH . $username . '/' . $repository;

		if ( ! is_dir( $basepath . '/' . $this->branch ) ) {
			if ( 'default' !== $this->branch ) {
				gh_log( "Cloning {$this->username}/{$this->repository}@{$this->branch}" );
			} else {
				gh_log( "Cloning {$this->username}/{$this->repository}" );
			}

			@mkdir( $basepath, 777, true );
			$url = "https://github.com/{$this->username}/{$this->repository}";
			$cmd = 'git clone --quiet --no-hardlinks --no-tags --depth 1';
			$cmd .= ( 'default' !== $this->branch ) ? " --branch \"{$this->branch}\"" : '';
			shell_exec( "$cmd $url $basepath/{$this->branch}" );
			if ( ! is_dir( "$basepath/{$this->branch}" ) ) {
				gh_log_error( '	Failed To Clone. Unknown Error Occured' );
			} else {
				gh_log( '✔️  Success' );
			}
		} else {
			if ( 'default' !== $this->branch ) {
				gh_log( "Cache Exists For : {$this->username}/{$this->repository}@{$this->branch}" );
			} else {
				gh_log( "Cache Exists For : {$this->username}/{$this->repository}" );
			}
		}
	}

	/**
	 * Returns A Full Path of the given template repository
	 *
	 * @return string
	 */
	public function get_path() {
		if ( ! empty( $this->branch ) ) {
			return TEMPLATE_REPO_PATH . $this->username . '/' . $this->repository . '/' . $this->branch . '/';
		}
		return TEMPLATE_REPO_PATH . $this->username . '/' . $this->repository . '/';
	}
}