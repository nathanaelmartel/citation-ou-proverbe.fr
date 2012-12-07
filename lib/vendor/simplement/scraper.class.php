<?php

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
  
  public function getPage($url) {
    $file = scraper::slugify($url);
    
    if (file_exists($file)) {
      
      $fp = fopen($file, 'r');
      $output = fread($fp, filesize($file));
      fclose($fp);
      
    } else {
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:15.0) Gecko/20100101 Firefox/15.0.1'); 
      curl_setopt($ch, CURLOPT_URL, $url );
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $output = curl_exec($ch);
      $this->curl_info = curl_getinfo($ch);
      curl_close($ch);
  
      $fp = fopen($file, 'w');
      fwrite($fp, $output);
      fclose($fp);
      
    }
    
    return $output;
  }
  
  public function getPageHeader($url) {
  	if ($this->curl_info == '')
  		$this->getPage($url);
  	
  	return $this->curl_info;
  }
  
  public function queryPage($url, $css_selector, $fetch = 'nodeValue') {
    $html = $this->getPage($url);

    $dom = new Zend_Dom_Query($html);
    $query_results = $dom->query($css_selector);
    
    $results = array();
    foreach ($query_results as $result) {
      if ($fetch == 'nodeValue')
        $results[] = $result->nodeValue;
      else if (($fetch == 'href-absolute') && (($result->tagName == 'a') || ($result->tagName == 'link')))
        $results[] = scraper::absoluteUrl($result->getAttribute('href'), $url);
      else if (($fetch == 'src-absolute') && ($result->tagName == 'img'))
        $results[] = scraper::absoluteUrl($result->getAttribute('src'), $url);
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
    else
      return substr($base_url, 0, strrpos($base_url, '/')).$url;
    
    return $url;
  }
  
  static function slugify($url) {
    $base_path = dirname(__FILE__).'/../../../data/scraper_cache/';
    
    $url_info = parse_url($url);
    
    if (!file_exists($base_path.$url_info['host']))
      mkdir($base_path.$url_info['host']);
    
    $filename = '';
    if (array_key_exists('path', $url_info))
      $filename = scraper::toAscii($url_info['path']).'-';
    
    $file = $url_info['host'].'/'.$filename.base64_encode($url);
    
    return $base_path.$file.'.html';
  }
  
  public static function toAscii($str) {
    setlocale(LC_ALL, 'fr_FR.UTF8');
    
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
    }
    
    return $text;
  }

  static function encodingCorrectionAlpha($text) {
    

    //return mb_convert_encoding($text, 'UTF8', mb_detect_encoding($text));
    //return iconv(mb_detect_encoding($text), 'UTF8', $text);
 
    $text = utf8_decode($text);
        
    
    return $text;
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
      array('&#13;', '&#amp;', '&#039;', '’'), 
      array(" ", '&', "'", "'"),
      $text);
    
    return $text;
  }
}

