<?php

/**
 * Class Update_Template
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 */
class Update_Template {
	/**
	 * @var bool|string
	 */
	protected $content = false;

	/**
	 * @var bool|\Template_File_Handler
	 */
	protected $parent_template = false;

	/**
	 * Update_Template constructor.
	 *
	 * @param string                     $content
	 * @param Template_File_Handler|bool $parent_template
	 */
	public function __construct( $content, $parent_template = false ) {
		$this->content         = $content;
		$this->parent_template = $parent_template;
	}

	/**
	 * @return array
	 * @example
	 * <!-- mypath/filename.md -->
	 *
	 * <!-- mypath/filename.md -->
	 * @since {NEWVERSION}
	 */
	public function extract_included_templates() {
		$matches = array();
		$re      = '/(?P<inline><!--\s(?:INCLUDE|include)(\s\[(?P<markdown>.+)\]\s|\s)(?P<file>[\w\W].+)\s-->)|((?P<sec_start><!--\s(?:START|start)(\s\[(?P<markdown>.+)\]\s|\s)(?P<file>[\w\W].+)\s-->)(?P<sec_content_start_linebr>\n|)(?P<sec_content>[\s\S]*?)(?P<sec_content_end_linebr>\n|)(?P<sec_end><!--\s(?:END|end)\7\9\s-->))/mJ';
		preg_match_all( $re, $this->content, $matches, PREG_SET_ORDER, 0 );

		/**
		 * [0] -- > Full Content
		 * [1] -- > Comment Key
		 * [2] -- > Content Inside Comment
		 */
		return $matches;
	}

	public function update() {
		$this->include_templates();

		$function = 'dynamic_readme_' . TEMPLATE_ENGINE . '_engine';
		$default  = 'dynamic_readme_mustache_engine';

		if ( function_exists( $function ) ) {
			#gh_log( 'Template Engine ' . TEMPLATE_ENGINE . ' Found' );
			$this->content = call_user_func( $function, $this->content );
		} elseif ( function_exists( $default ) ) {
			gh_log_error( 'Template Engine ' . TEMPLATE_ENGINE . ' Not Found' );
			gh_log_error( 'Using Default Template Engine {mustache}' );
			$this->content = call_user_func( $function, $this->content );
		} else {
			gh_log_error( 'No Template Engine Found' );
		}

		return $this->content;
	}


	/**
	 * Process Template Files.
	 *
	 * @since {NEWVERSION}
	 */
	protected function include_templates() {
		$templates       = $this->extract_included_templates();
		$parent_template = ( method_exists( $this->parent_template, 'get_basedir' ) ) ? $this->parent_template->get_basedir() : false;
		$parent_template = ( method_exists( $this->parent_template, 'get_src' ) ) ? $this->parent_template->get_src() : $parent_template;

		foreach ( $templates as $template ) {
			if ( in_array( strtolower( $template['file'] ), array( 'raw_contents', 'raw_content', 'raw' ) ) ) {
				$template_content = escape_content_to_raw( Markdown_Handler::handle( $template['sec_content'], $template ), false );
			} else {
				$template_file = new Template_File_Handler( $template['file'], $parent_template );
				$contents      = $template_file->get_contents();
				if ( false === $contents ) {
					continue;
				}

				$template_content = new Update_Template( Markdown_Handler::handle( $contents, $template ), $template_file );
				$template_content = $template_content->update();
			}
			if ( false !== $template_content ) {
				if ( isset( $template['inline'] ) && ! empty( $template['inline'] ) ) {
					$str_find    = $template['inline'];
					$str_replace = $template_content;
					$content     = $this->content;
				} else {
					$regex       = '/';
					$regex       .= '(' . preg_quote( $template['sec_start'], '/' ) . ')';
					$regex       .= '(' . $template['sec_content_start_linebr'] . ')';
					$regex       .= '(' . preg_quote( $template['sec_content'], '/' ) . ')';
					$regex       .= '(' . $template['sec_content_end_linebr'] . ')';
					$regex       .= '(' . preg_quote( $template['sec_end'], '/' ) . ')';
					$regex       .= '/';
					$str_find    = 'PLACEHOLDER_REPLACE:' . rand( 1, 1000 );
					$str_replace = $template['sec_start'] . $template['sec_content_start_linebr'] . $template_content . $template['sec_content_end_linebr'] . $template['sec_end'];
					$content     = preg_replace( $regex, $str_find, $this->content );
				}

				if ( ! empty( $content ) ) {
					$this->content = str_replace( $str_find, $str_replace, $content );
				}
			}
		}
	}
}
