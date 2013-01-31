<?php

setlocale(LC_ALL, 'fr_FR.UTF8');

function get_contents_utf8($content) {
	return mb_convert_encoding($content, 'UTF-8',
			mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
}

require_once(dirname(__FILE__).'/../Zend/Dom/Query.php');
/**
 * scraper
 *
 * @package    simplement-web.com
 * @subpackage scraper
 * @author     Nathanaël Martel <nathanael@fam-martel.eu>
 */
class scraper
{
	private $curl_info = '';
	private $output = '';
	private $filename = '';
	private $url = '';
	
	public function __construct($url, $id = '', $cache = true) {
		$this->url = $url;
		$this->setFilename($id);
		if (!$cache) {
			if (file_exists($this->filename)) {
				unlink($this->filename);
			}
		}
	}
	
	public function setFilename($id = '') {
    $base_path = dirname(__FILE__).'/../../../data/scraper_cache/';
    
    $url_info = parse_url($this->url);
    
    if (!file_exists($base_path.$url_info['host']))
      mkdir($base_path.$url_info['host']);
    
		if ($id == '')
			$filename = scraper::slugify($url_info['path']);
		else
			$filename = $id;
		
		$this->filename = $base_path.$url_info['host'].'/'.$filename.'.html';
	}
	
	public function getFilename() {
		return $this->filename;
	}
  
  static function slugify($path) {
    
    $filename = scraper::toAscii($path).'-';
    
    return substr($filename, 0, 32);
  }
  
  public function getPage() {
    
    if (file_exists($this->filename) && filesize($this->filename)) {
      
      $fp = fopen($this->filename, 'r');
      $output = fread($fp, filesize($this->filename));
      fclose($fp);
      
    } else {
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:15.0) Gecko/20100101 Firefox/15.0.1'); 
      curl_setopt($ch, CURLOPT_URL, $this->url );
    	curl_setopt( $ch, CURLOPT_ENCODING, "" );
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $output = curl_exec($ch);
      $this->curl_info = curl_getinfo($ch);
      curl_close($ch);
      
      //$output = file_get_contents($this->url);
      
      //$output = get_contents_utf8($output);
  
      $fp = fopen($this->filename, 'w');
      fwrite($fp, $output);
      fclose($fp);
      
    }
    
    return $output;
  }
  
  public function getPageHeader() {
  	if ($this->curl_info == '')
  		$this->getPage();
  	
  	return $this->curl_info;
  }
  
  public function queryPage($css_selector, $fetch = 'nodeValue') {
    $html = $this->getPage();

    $dom = new Zend_Dom_Query($html);
    $query_results = $dom->query($css_selector);
    
    $results = array();
    foreach ($query_results as $result) {
      if ($fetch == 'nodeValue')
        $results[] = $result->nodeValue;
      else if (($fetch == 'href-absolute') && (($result->tagName == 'a') || ($result->tagName == 'link')))
        $results[] = scraper::absoluteUrl($result->getAttribute('href'), $this->url);
      else if (($fetch == 'src-absolute') && ($result->tagName == 'img'))
        $results[] = scraper::absoluteUrl($result->getAttribute('src'), $this->url);
      else 
        $results[] = $result->getAttribute($fetch);
    }
    
    return $results;
  }
  
  public static function absoluteUrl($url, $base_url) {
    $url_info = parse_url($base_url);
    
    if (substr($url, 0, 4) == 'http')
      return $url;
    else if (substr($url, 0, 1) == '/')
      return $url_info['scheme'].'://'.$url_info['host'].$url;
    else if (substr($url, 0, 1) == '#')
      return $base_url;
    else if (substr($url, 0, 10) == 'javascript')
      return $base_url;
    else
      return substr($base_url, 0, strrpos($base_url, '/')).$url;
    
    return $url;
  }
  
  public static function toAscii($str) {
    
    $replace = array("'", '’', '&#039;');
    if( !empty($replace) ) {
      $str = str_replace((array)$replace, ' ', $str);
    }

    $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $clean = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $clean);
    $clean = strtolower(trim($clean, '-'));
    $clean = preg_replace("/[\/_| -]+/", '-', $clean);
  
    return $clean;
  }

  /*
   * $methode: 'alpha', 'none'
   */
  public static function encodingCorrection($text, $methode = 'none') {
    
    switch ($methode) {
      case 'none':
        return $text;
      case 'alpha':
        return scraper::encodingCorrectionAlpha($text);
      case 'beta':
        return scraper::encodingCorrectionBeta($text);
      case 'gamma':
        return scraper::encodingCorrectionGamma($text);
      case 'epsilon':
        return scraper::encodingCorrectionEpsilon($text);
    }
    
    return $text;
  }
  
  static function utf8($content) {
    if(!mb_check_encoding($content, 'UTF-8')
        OR !($content === mb_convert_encoding(mb_convert_encoding($content, 'UTF-32', 'UTF-8' ), 'UTF-8', 'UTF-32'))) {

        $content = mb_convert_encoding($content, 'UTF-8');

        if (mb_check_encoding($content, 'UTF-8')) {
            // log('Converted to UTF-8');
        } else {
            // log('Could not converted to UTF-8');
        }
    }
    return utf8_decode($content);
} 

  static function encodingCorrectionAlpha($text) {
        
    return utf8_decode($text);
  }

  static function encodingCorrectionBeta($text) {
        
    return utf8_encode($text);
  }

  static function encodingCorrectionGamma($text) {
  	
    return mb_convert_encoding($text, 'UTF8', mb_detect_encoding($text));
  }

  static function encodingCorrectionEpsilon($text) {
  	
    return iconv(mb_detect_encoding($text), 'UTF8', $text);
  }

  public static function cleanTag($tag) {
  	
  		$tag = strtolower($tag);
  		
  		$replace = array("l'", 'l’', 'l&#039;', "d'", 'd’', 'd&#039;', "s'", 's’', 's&#039;');
  		if( !empty($replace) ) {
  			$tag = str_replace((array)$replace, ' ', $tag);
  		}
  		
  		$tag = trim($tag, '.,;:');
  		$tag = trim($tag);
  		
  		return $tag;
  }

  public static function cleanAuthor($author_name) {
  	
  	$author_name = trim($author_name, '-.,;:');
  	$author_name = trim($author_name);
  	$author_name = trim($author_name);
  	$author_name = strtolower($author_name);
  	$author_name = ucwords($author_name);
  	
  	return $author_name;
  }

  public static function cleanString($text) {
    
    $text = strip_tags($text);
    $text = htmlentities($text, ENT_COMPAT, 'ISO-8859-1');
    
    $text = str_replace(
      array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
      array("'", "'", '"', '"', '-', '--', '...'),
      $text);
    $text = str_replace(
      array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
      array("'", "'", '"', '"', '-', '--', '...'),
      $text);
    
    $text = html_entity_decode($text);
    
    $text = str_replace(
      array('&#13;', '&#amp;', '&#039;', '’', '&#160;', '&#171;', '&#187;', '&laquo;', '&raquo;'), 
      array(" ", '&', "'", "'", ' ', '', '', '', ''),
      $text);
    
    return $text;
  }
}

