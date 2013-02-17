<?php

/**
 * media actions.
 *
 * @package    citations
 * @subpackage media
 * @author     Nathanaël Martel <nathanael@fam-martel.eu>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class mediaActions extends sfActions
{
  
  public function executeShort(sfWebRequest $request)
  {
  	$this->forward404Unless($citation = Doctrine_Core::getTable('Citation')->findOneById(array($request->getParameter('id'))), sprintf('Object citation does not exist (%s).', $request->getParameter('id')));
  	
  	$this->redirect('@citation_image?sf_format=png&slug='.$citation->slug.'&author='.$citation->Author->slug, 301);
  }
  
  public function executeShow(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->forward404Unless($citation = Doctrine_Core::getTable('Citation')->findOneBySlug(array($slug)), sprintf('Object citation does not exist (%s).', $slug));
    $this->forward404Unless($citation->is_active);
    
  
    $format = $request->getParameter('sf_format');
    if (($format != 'jpg') && ($format != 'gif') && ($format != 'png'))
      $format = 'jpg';
    
    $width = '1200';
    $height = '768';
    
    $filename = sfConfig::get('sf_web_dir').'/medias/'.$citation->Author->slug; 
    if (!file_exists($filename))
    	mkdir($filename);
    
    $filename .= '/'.$citation->slug.'.'.$format;
    
    if (file_exists($filename)) {
      $img = new sfImage($filename);
    } else {
      $img = new sfImage();
      
      $img->thumbnail($width, $height, 'center');
      $img->negate();
      $rgb = $citation->getRGBColor();
      $img->colorize($rgb[0], $rgb[1], $rgb[2], 1);
      $citation->getRGBColorHex();
      
      $text_font_name = 'Quicksand/Quicksand_Bold';
      $text_font_size = 50;
      $text_font_dir = sfConfig::get('app_sfImageTransformPlugin_font_dir').DIRECTORY_SEPARATOR.$text_font_name.'.ttf';
      $textheight = $height;
     
      while ($textheight > $height/2) {
      	$text_font_size = $text_font_size - 10;
	      $text = $this->wrap($text_font_size, 0, $text_font_dir, $citation->quote, $width*.8);
	      $box = imagettfbbox($text_font_size, 0, $text_font_dir, $text);
	      $textheight = abs($box[5] - $box[1]);
      }
      
      $box = imagettfbbox($text_font_size, 0, $text_font_dir, 'Test');
      $lineHeight = abs($box[5] - $box[1]);
      $lines = explode("\n", $text);
      $img->text($text, $width*.1, floor(($height-$textheight)*.4)-$lineHeight*(count($lines)-1), $text_font_size, $text_font_name, $citation->getTextRGBColorHex());
      
      
      $author_font_name = 'Quicksand/Quicksand_Book_Oblique';
      $author_font_size = 20;
      $author_font_dir = sfConfig::get('app_sfImageTransformPlugin_font_dir').DIRECTORY_SEPARATOR.$author_font_name.'.ttf';
      $box = imagettfbbox($author_font_size, 0, $author_font_dir, $citation->Author->name);
      $textwidth = abs($box[4] - $box[0]) - 4;
      
      $img->text($citation->Author->name, $width-$textwidth-100, floor(($height-$textheight)*.4)+$lineHeight*count($lines)*2, $author_font_size, $author_font_name, $citation->getTextRGBColorHex());
      
      
      
      $url_font_name = 'Quicksand/Quicksand_Light';
      $url_font_size = 10;
      $url_font_dir = sfConfig::get('app_sfImageTransformPlugin_font_dir').DIRECTORY_SEPARATOR.$url_font_name.'.ttf';
      $url = 'http://citation-ou-proverbe.fr/c/'.$citation->id;
      $box = imagettfbbox($url_font_size, 0, $url_font_dir, $url);
      $textwidth = abs($box[4] - $box[0]) - 4;
      
      $img->text($url, $width-$textwidth-10, $height-20, $url_font_size, $url_font_name, '#000000');
      
      
      $img->setMIMEType('image/'.$format);
      //$img->saveAs($filename, 'image/'.$format);
    }
    
    
    $response = $this->getResponse();
    $response->setContentType($img->getMIMEType());    
    $response->setContent($img); 
    
    return sfView::NONE;
  }
  
  private function wrap($fontSize, $angle, $fontFace, $string, $width)
  {
  	$ret = "";
  	$arr = explode(' ', $string);
  	foreach ( $arr as $word )
  	{
  		$teststring = $ret.' '.$word;
  		$testbox = imagettfbbox($fontSize, $angle, $fontFace, $teststring);
  		if ( $testbox[2] > $width ){
  			$ret.=($ret==""?"":"\n").$word;
  		} else {
  			$ret.=($ret==""?"":' ').$word;
  		}
  	}
  	return $ret;
  }
}
