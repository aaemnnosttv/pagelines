<?php
/**
 * Class PageLinesData
 *
 * Handles user-submitted data formatting (input and output)
 *
 * @since 2.0.b2
 * @var $allowed — HTML tags that are allowed in selected text input fields
 */
class PageLinesData {
	
	var $allowed = '<span><em><strong><i><b><u><code><br><strike><sub><sup>';

	function input_encode($text, $allowed = false) {
		return urlencode( trim( strip_tags( stripslashes($text), ($allowed) ? $this->allowed : false ) ) );
	}

	function input_strip($text, $allowed = true) {
		return trim( strip_tags($text, ($allowed) ? $this->allowed : false) );
	}

	function out_htmlentities($text) {
		return trim( htmlentities( stripslashes( $text ) ) );
	}

	function out_texturize($text, $stripslashes = false, $decode = false) {
		return trim( wptexturize(($decode) ? urldecode($text) : (($stripslashes) ? stripslashes($text) : $text ) ) );
	}

	function out_htmlspecialchars($text, $stripslashes = false, $decode = false) {
		return trim( htmlspecialchars(($decode) ? urldecode($text) : (($stripslashes) ? stripslashes($text) : $text ) ) );
	}

	function out_noscripts($text) {
		return trim( $this->strip_only(stripslashes($text), '<script>', true ) );
	}

	function strip_js($text) {
		return trim( $this->strip_only($text, '<script>', true) );
	}

	function strip_only($str, $tags, $stripContent = false) {
		
		$content = '';
		if (!is_array($tags)) {
			$tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));
			if (end($tags) == '') array_pop($tags);
		}
		foreach ($tags as $tag) {
			if ($stripContent) $content = '(.+</'.$tag.'[^>]*>|)';
			$str = preg_replace('#</?'.$tag.'[^>]*>'.$content.'#is', '', $str);
		}
		return $str;
		
	}
}