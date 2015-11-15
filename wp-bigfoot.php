<?php
/*
Plugin Name: WP-Bigfoot
Plugin URI: https://github.com/freekrai/wp-bigfoot
Description: Easier footnotes for your site, and jQuery Bigfoot for cooler effects
Author: Roger Stringer
Version: 1.0.2
Author URI: http://rogerstringer.com
*/ 

class WP_Bigfoot	{

	private $footnotes = array();
	private $option_name = 'wp_bigfoot';
	private $db_version = 1;
	private $placement = 'content';

	public $shared_post;

	function __construct(){
		add_action('init', array($this, 'init'));
		add_action('wp_footer', array($this, 'footer'));
	}

	function init() {
		global $current_user;
		add_shortcode('footnote', array($this,'shortcode_footnote') );
		add_filter('the_content', array($this, 'the_content' ), 12 );
		$this->admin_page_init();
	}
	
	function footer() {
		// call_user_func_array() compliance
	}
	
	function admin_page_init() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('bigfoot', plugin_dir_url( __FILE__ ) . 'js/bigfoot.min.js', 'jquery', '1.4.0', true );
		wp_enqueue_script('wp-bigfoot', plugin_dir_url( __FILE__ ) . 'js/wp-bigfoot.js', 'jquery', '1.4.0', true );
		if( is_admin() ){
			wp_enqueue_style('wp-bigfoot-default', plugin_dir_url( __FILE__ ) . 'css/bigfoot-default.css');
			wp_enqueue_style('wp-bigfoot-admin', plugin_dir_url( __FILE__ ) . 'css/wp-bigfoot.css');
		}else{
			/*
				add some switching logic here based on admin options for styles: default, bottom, or number
			*/
			wp_enqueue_style( 'wp-bigfoot', plugin_dir_url( __FILE__ ) . 'css/bigfoot-default.css');
		}
	}

	function shortcode_footnote( $atts, $content=NULL ){
		global $id;
		if ( null === $content )	return;
		$content = $this->remove_crappy_markup( $content );
		if ( ! isset( $this->footnotes[$id] ) ) $this->footnotes[$id] = array();
		$this->footnotes[$id][] = $content;
		$count = count( $this->footnotes[$id] );
		return '<a href="#footnote-' . $count . '-' . $id . '" ' . 'id="note-' . $count . '-' . $id . '" ' . 'rel="footnote">' . $count . '</a>';
	}
	
	function remove_crappy_markup( $string ){
		$patterns = array(
			'#^\s*</p>#',
			'#<p>\s*$#'
		);
		return preg_replace($patterns, '', $string);
	}

	function the_content($content) {
		return $this->get_footnotes( $content );
	}

	function get_footnotes( $content ) {
		global $id;
		if ( empty( $this->footnotes[$id] ) )	return $content;
		
		$footnotes = $this->footnotes[$id];
		if( count($footnotes) ){
			$content .= '<div class="footnotes">';
			$content .= '<hr />';
			$content .= '<ol>';
			foreach ( $footnotes as $number => $footnote ): 
				$number++;
				$content .= '<li id="footnote-'.$number.'-'.$id.'" class="footnote">';
				$content .= '<p>';
				$content .= $footnote;
				$content .= '<a href="#note-'.$number.'-'.$id.'" class="footnote-return">&#8617;</a>';
				$content .= '</p>';
				$content .= '</li><!--/#footnote-'.$number.'.footnote-->';
			endforeach;
			$content .= '</ol>';
			$content .= '</div><!--/#footnotes-->';
		}
		return $content;
			
	}


}
new WP_Bigfoot();
include 'wp-bigfoot-options.php';