<?php
class wpi_caption{
	public function __construct(){
		add_action('init', array($this, "init"));
	}
	public function init(){
		add_shortcode('wpi_caption', array($this, 'wpi_img_caption_shortcode'));
	}
	public function wpi_img_caption_shortcode( $attr, $content = null ) {
		
		if ( ! isset( $attr['caption'] ) ) {
			if ( preg_match( '#((?:<a [^>]+>\s*)?<img [^>]+>(?:\s*</a>)?)(.*)#is', $content, $matches ) ) {
				$content = $matches[1];
				$attr['caption'] = trim( $matches[2] );
			}
		} elseif ( strpos( $attr['caption'], '<' ) !== false ) {
			$attr['caption'] = wp_kses( $attr['caption'], 'post' );
		}
		$attr['caption'] = do_shortcode($attr['caption'] );
		$output = apply_filters( 'img_caption_shortcode', '', $attr, $content );
		if ( $output != '' )
			return $output;

		$atts = shortcode_atts( array(
			'id'	  => '',
			'align'	  => 'alignnone',
			'width'	  => '',
			'caption' => '',
			'class'   => '',
		), $attr, 'caption' );

		$atts['width'] = (int) $atts['width'];
		if ( $atts['width'] < 1 || empty( $atts['caption'] ) )
			return $content;

		if ( ! empty( $atts['id'] ) )
			$atts['id'] = 'id="' . esc_attr( sanitize_html_class( $atts['id'] ) ) . '" ';

		$class = trim( 'wp-caption ' . $atts['align'] . ' ' . $atts['class'] );

		$html5 = current_theme_supports( 'html5', 'caption' );
		
		$width = $html5 ? $atts['width'] : ( 10 + $atts['width'] );

		$caption_width = apply_filters( 'img_caption_shortcode_width', $width, $atts, $content );

		$style = '';
		if ( $caption_width )
			$style = 'style="width: ' . (int) $caption_width . 'px" ';

		$html = '';
		if ( $html5 ) {
			$html = '<figure ' . $atts['id'] . $style . 'class="' . esc_attr( $class ) . '">'
			. do_shortcode( $content ) . '<figcaption class="wp-caption-text">' . $atts['caption'] . '</figcaption></figure>';
		} else {
			$html = '<div ' . $atts['id'] . $style . 'class="' . esc_attr( $class ) . '">'
			. do_shortcode( $content ) . '<p class="wp-caption-text">' . $atts['caption'] . '</p></div>';
		}
		
		//$html=do_shortcode( $content ) ;

		return $html;
	}
}
new wpi_caption();