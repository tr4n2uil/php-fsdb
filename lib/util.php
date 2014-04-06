<?php
/**
 *	Utility Functions
 *
 *	Vibhaj Rajan <vibhaj8@gmail.com>
 *
 *	Licensed under MIT License 
 *	http://www.opensource.org/licenses/mit-license.php
 *
**/

	// http redirect
	function http_redirect( $url, $array = array() ){
		if( $array ){
			$params = array();
			foreach( $array as $k => $v )
				$params[] = $k.'='.$v;
			$url .= '?'.implode( '&', $params );
		}
		
		header( 'Location: '. $url );
	}

	// get unique object
	function unique_object( $cls, $array = array() ){
		try {
			return $cls::objects()->get( $array );
		}
		catch( Exception $e ){
			return null;
		}
	}

	// generate random string
	function random_string( $length = 10, $charset = 'qwert12yuiop34asdf56ghjkl78zxcv90bnm' ){
		$result = '';
		$charsetlen = strlen( $charset ) - 1;

		for( $i = 0; $i < $length; $i++ ){
			$result .= $charset[ mt_rand( 0, $charsetlen ) ];
		}

		return $result;
	}

	// generate random uuid
	function random_uuid(){
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', 
			// 32 bits for "time_low"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),
	 
			// 16 bits for "time_mid"
			mt_rand(0, 0xffff),
	 
			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand(0, 0x0fff) | 0x4000,
	 
			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand(0, 0x3fff) | 0x8000,
	 
			// 48 bits for "node"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}

	// slugify text
	function slugify( $text ){
		// replace non letter or digits by -
		$text = preg_replace( '~[^\\pL\d]+~u', '-', $text );

		// trim
		$text = trim( $text, '-' );

		// transliterate
		$text = iconv( 'utf-8', 'us-ascii//TRANSLIT', $text );

		// lowercase
		$text = strtolower( $text );

		// remove unwanted characters
		$text = preg_replace( '~[^-\w]+~', '', $text );

		if( empty( $text ) ){
			return '';
		}

		return $text;
	}

	// truncate html
	function html_truncate( $maxLength, $html, $isUtf8 = true ){
		return tidy_repair_string( substr( $html, 0, $maxLength ), array( 'wrap' => 0, 'show-body-only' => TRUE ), $isUtf8 ? 'utf8' : 'ascii' ); 

		$printedLength = 0;
		$position = 0;
		$tags = array();
		$result = '';

		// For UTF-8, we need to count multibyte sequences as one character.
		$re = $isUtf8 ? '{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;|[\x80-\xFF][\x80-\xBF]*}' : '{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}';

		while( $printedLength < $maxLength && preg_match( $re, $html, $match, PREG_OFFSET_CAPTURE, $position ) ){
			list( $tag, $tagPosition ) = $match[ 0 ];

			// Print text leading up to the tag.
			$str = substr( $html, $position, $tagPosition - $position );
			if( $printedLength + strlen( $str ) > $maxLength ){
				$result .= substr( $str, 0, $maxLength - $printedLength );
				$printedLength = $maxLength;
				break;
			}

			$result .= $str;
			$printedLength += strlen( $str );
			if( $printedLength >= $maxLength ) break;

			if( $tag[ 0 ] == '&' || ord( $tag ) >= 0x80 ){
				// Pass the entity or UTF-8 multibyte sequence through unchanged.
				$result .= $tag;
				$printedLength++;
			}
			else {
				// Handle the tag.
				$tagName = $match[ 1 ][ 0 ];
				if( $tag[1] == '/' ){
					// This is a closing tag.
					$openingTag = array_pop( $tags );
					assert( $openingTag == $tagName ); // check that tags are properly nested.

					$result .= $tag;
				}
				else if( $tag[ strlen( $tag ) - 2 ] == '/'){
					// Self-closing tag.
					$result .= $tag;
				}
				else {
					// Opening tag.
					$result .= $tag;
					$tags[] = $tagName;
				}
			}

			// Continue after the tag.
			$position = $tagPosition + strlen( $tag );
		}

		// Print any remaining text.
		if( $printedLength < $maxLength && $position < strlen( $html ) )
			$result .= substr( $html, $position, $maxLength - $printedLength );

		// Close any open tags.
		while ( !empty( $tags ) )
			$result .= '</'.array_pop( $tags ) .'>';

		return $result;
	}

	// clean html
	function html_clean( $html ){
		//strip_tags( $html, '' );

		require_once( PR_ROOT. '../lib/htmlawed/htmLawed.php' );
		return htmLawed( $html );
	}

	// get unique alias
	function unique_alias( $cls, $name, $array = array(), $col = 'alias' ){
		$alias = slugify( $name );
		$array[ $col ] = new Q_START( array( $col => $alias ) );
		
		$res = $cls::objects()->filter( $array )->order_by( array( 'repeat' ) )->select();
		$cnt = count( $res );

		if( $cnt == 0 ){
			return array( $alias, 1 );
		}
		else {
			$max = $res[ $cnt - 1 ];
			$cnt = $max->repeat + 1;
			return array( $alias.'-'.$cnt, $cnt );
		}
	}

	// text diff
	function text_diff( $old, $new ){
		if( $new != $old ){
			require_once( PR_ROOT. '../lib/php-finediff/finediff.php' );
			return FineDiff::getDiffOpcodes( $old, $new );	
		}
		return '';
	}

	// text patch
	function text_patch( $old, $patch ){
		if( $patch ){
			require_once( DF_ROOT. 'finediff.php' );
			return FineDiff::renderToTextFromOpcodes( $old, $patch );	
		}
		return $old;
	}


?>
