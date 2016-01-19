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

  public function executeWallpaper(sfWebRequest $request) {
    $slug = $request->getParameter('slug');
    $this->forward404Unless($citation = Doctrine_Core::getTable('Citation')->findOneBySlug(array($slug)), sprintf('Object citation does not exist (%s).', $slug));
    $this->forward404Unless($citation->is_active);

    $this->citation = $citation;

		$response = $this->getResponse();
		$response->addJavascript(sfConfig::get('sf_js_dir'). 'jquery.uniform.min.js');
		$response->addJavascript(sfConfig::get('sf_js_dir'). 'spectrum.js');
		$response->addJavascript(sfConfig::get('sf_js_dir'). 'dropzone.js');
    $response->addMeta('description', 'Fond d\'écran personalisé de citation');
    $response->setTitle('Fond d\'écran personalisé pour la citation : '.$citation->getShortQuote().' - '.$citation->getAuthor() );
  }

  public function executeUpload(sfWebRequest $request) {

  	if (!empty($_FILES)) {
  		$tempFile = $_FILES['file']['tmp_name'];
  		//$pathinfo = pathinfo($_FILES['file']['name']);
  		$fileName = md5($_FILES['file']['name']);//.'.'.$pathinfo['extension'];
  		move_uploaded_file($tempFile, sfConfig::get('sf_upload_dir').'/'.$fileName);
  	}

  	$this->setLayout(false);
  	return $this->renderText($fileName);
  }

  public function executePortrait(sfWebRequest $request)
  {
    $slug = $request->getParameter('author');
  	$this->forward404Unless($author = Doctrine_Core::getTable('Author')->findOneBySlug(array($slug)), sprintf('Object citation does not exist (%s).', $slug));
    $this->forward404Unless($author->is_active);
    $this->forward404Unless($author->has_thumbnail);

    $format = $request->getParameter('sf_format');
    if (($format != 'jpg') && ($format != 'gif') && ($format != 'png'))
    	$format = 'jpg';

    $effect = $request->getParameter('effect');
    if (($effect != 'original') && ($effect != 'noir-et-blanc') && ($effect != 'contour') && ($effect != 'relief') && ($effect != 'dessin'))
    	$effect = 'original';

    $original_file = sfConfig::get('sf_web_dir').'/portrait/'.$author->slug.'/';
    $handle = opendir($original_file);
    while (false !== ($entry = readdir($handle))) {
      if (substr_count($entry, 'original') > 0) {
        $original_file .= $entry;
      }
    }
    closedir($handle);


    $filename = sfConfig::get('sf_web_dir').'/medias/'.$author->slug;
    if (!file_exists($filename))
    	mkdir($filename);

    $filename = sfConfig::get('sf_web_dir').'/medias/'.$author->slug.'/portrait.'.$author->slug.'.'.$effect.'.'.$format;

    $overlay_file = sfConfig::get('sf_web_dir').'/images/portrait-overlay.png';

    $img = new sfImage($original_file);
    $img->transparency('#000000');

    if ($effect == 'contour') {
      $img->edgeDetect();
    }
    if ($effect == 'relief') {
      $img->emboss();
    }
    if ($effect == 'dessin') {
      $img->sketchy();
    }

    $img->thumbnail(200, 200, 'scale', '#FFFFFF');

    if ($effect != 'original') {
      $img->greyscale();
	    $img->colorize(120, 120, 120, 1);
	    $overlay_img = new sfImage($overlay_file);
	    $overlay_img->resize($img->getWidth(), $img->getHeight(), true, false);
	    $img->overlay($overlay_img, 'center');
    }

    $img->setMIMEType('image/'.$format);
    $img->saveAs($filename, 'image/'.$format);

    $response = $this->getResponse();
    $response->setContentType($img->getMIMEType());
    $response->setContent($img);

    return sfView::NONE;
  }

  public function executeShow(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->forward404Unless($citation = Doctrine_Core::getTable('Citation')->findOneBySlug(array($slug)), sprintf('Object citation does not exist (%s).', $slug));
    $this->forward404Unless($citation->is_active);

    $format = $request->getParameter('sf_format');
    if (($format != 'jpg') && ($format != 'gif') && ($format != 'png'))
      $format = 'jpg';

    $filename = sfConfig::get('sf_web_dir').'/medias/'.$citation->Author->slug;
    if (!file_exists($filename))
    	mkdir($filename);

    $filename .= '/'.$citation->Author->slug.'.'.$citation->slug.'.'.$format;

    if (file_exists($filename)) {
      $img = new sfImage($filename);
    } else {

      $img = $this->build($citation, $citation->getRGBColorHex(), $citation->getTextRGBColorHex(), 1200, 768);

      $img->setMIMEType('image/'.$format);
      $img->saveAs($filename, 'image/'.$format);
    }

    $response = $this->getResponse();
    $response->setContentType($img->getMIMEType());
    $response->setContent($img);

    return sfView::NONE;
  }

  public function executeCustom(sfWebRequest $request)
  {
  	/// wallpaper/:author.:id.:width.:height.:bgcolor.:textcolor.:authorname.:portrait.:sf_format
  	/// wallpaper/:author.:id.:width.:height.:bgcolor.:textcolor.:authorname.:portrait.:background:sf_format
    $id = $request->getParameter('id');
    $width = $request->getParameter('width');
    $height = $request->getParameter('height');
    $bgcolor = $request->getParameter('bgcolor');
    $textcolor = $request->getParameter('textcolor');
    $show_author = $request->getParameter('authorname');
    $show_portrait = $request->getParameter('portrait');
    $background = $request->getParameter('background', false);
    $this->forward404Unless($citation = Doctrine_Core::getTable('Citation')->findOneById(array($id)), sprintf('Object citation does not exist (%s).', $id));
    $this->forward404Unless($citation->is_active);

    $format = $request->getParameter('sf_format');
    if (($format != 'jpg') && ($format != 'gif') && ($format != 'png'))
      $format = 'jpg';

    $filename = sfConfig::get('sf_web_dir').'/wallpaper/'.$citation->Author->slug.'.'.$citation->id.'.'.$width.'.'.$height.'.'.$bgcolor.'.'.$textcolor.'.'.$show_author.'.'.$show_portrait;
    if ($background)
    	$filename .= $background;
    $filename .= '.'.$format;

    if (file_exists($filename)) {
      $img = new sfImage($filename);
    } else {

      $img = $this->build($citation, '#'.$bgcolor, '#'.$textcolor, $width, $height, true, true, $show_author, $show_portrait, $background);

      $img->setMIMEType('image/'.$format);
      $img->saveAs($filename, 'image/'.$format);
    }

    $response = $this->getResponse();
    $response->setContentType($img->getMIMEType());
    $response->setContent($img);

    return sfView::NONE;
  }

  public function executeTwitter(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $this->forward404Unless($citation = Doctrine_Core::getTable('Citation')->findOneById(array($id)), sprintf('Object citation does not exist (%s).', $id));
    $this->forward404Unless($citation->is_active);

    $format = $request->getParameter('sf_format');
    if (($format != 'jpg') && ($format != 'gif') && ($format != 'png'))
      $format = 'jpg';

    $filename = sfConfig::get('sf_web_dir').'/twitter/'.$citation->Author->slug;
    if (!file_exists($filename))
    	mkdir($filename);

    $filename .= '/'.$citation->id.'.'.$format;

    if (file_exists($filename)) {
      $img = new sfImage($filename);
    } else {

      $img = $this->build($citation, $citation->getRGBColorHex(), $citation->getTextRGBColorHex(), 560, 350, false, false, false);

      $img->setMIMEType('image/'.$format);
      $img->saveAs($filename, 'image/'.$format);
    }

    $response = $this->getResponse();
    $response->setContentType($img->getMIMEType());
    $response->setContent($img);

    return sfView::NONE;
  }

  public function build($citation, $bgcolor, $textcolor, $width = 1200, $height = 768, $show_overlay = true, $show_url = true, $show_author = true, $show_portrait = true, $background = false)
  {
      $overlay_file = sfConfig::get('sf_web_dir').'/images/overlay.png';
			$rgb = sscanf($bgcolor, '#%2x%2x%2x');

      $img = new sfImage();
      $img->transparency('#000000');
      $img->thumbnail($width, $height, 'center');

      if ($background) {
      	$background_img = new sfImage(sfConfig::get('sf_upload_dir').'/'.$background);
      	$background_img->thumbnail($width, $height, 'center', $bgcolor);
		    $img->overlay($background_img);
      } else {
      	$img->colorize($rgb[0], $rgb[1], $rgb[2], 0);
      }

      if ($show_overlay) {
	      $overlay_img = new sfImage($overlay_file);
	      $img->overlay($overlay_img, array(20, $height-80));
      }

      if ($show_portrait && $citation->Author->has_thumbnail) {
    		$overlay_portrait_file = sfConfig::get('sf_web_dir').'/medias/'.$citation->Author->slug.'/portrait.'.$citation->Author->slug.'.contour.jpg';
    		if (!file_exists($overlay_portrait_file)) {
    			file_get_contents(str_replace(sfConfig::get('sf_web_dir'), 'https://www.citation-ou-proverbe.fr', $overlay_portrait_file));
    		}
    		if (file_exists($overlay_portrait_file)) {
		      $overlay_portrait_img = new sfImage($overlay_portrait_file);
		      $opposite = array(255 - $rgb[0], 255 - $rgb[1], 255 - $rgb[2]);
      		$overlay_portrait_img->colorize(-$opposite[0], -$opposite[1], -$opposite[2], 0);
		      $img->overlay($overlay_portrait_img, array($width*0.75, $height*0.7));
    		}
      }

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
      $left = $width*.1;
      $top = floor(($height-$textheight)*.4)-$lineHeight*(count($lines)-1);
      if ($background) {
      	$img->text($text, $left+1, $top+1, $text_font_size, $text_font_name, $bgcolor);
      }
      $img->text($text, $left, $top, $text_font_size, $text_font_name, $textcolor);

      if ($show_author) {
	      $author_font_name = 'Quicksand/Quicksand_Book_Oblique';
	      $author_font_size = 20;
	      $author_font_dir = sfConfig::get('app_sfImageTransformPlugin_font_dir').DIRECTORY_SEPARATOR.$author_font_name.'.ttf';
	      $box = imagettfbbox($author_font_size, 0, $author_font_dir, $citation->Author->name);
	      $textwidth = abs($box[4] - $box[0]) - 4;
	      $left = $width-$textwidth-100;
	      $top = floor(($height-$textheight)*.4)+$lineHeight*count($lines)*2;
	      if ($background) {
	      	$img->text($citation->Author->name, $left+1, $top+1, $author_font_size, $author_font_name, $bgcolor);
	      }
	      $img->text($citation->Author->name, $left, $top, $author_font_size, $author_font_name, $textcolor);
      }

      if ($show_url) {
	      $url_font_name = 'Quicksand/Quicksand_Light';
	      $url_font_size = 10;
	      $url_font_dir = sfConfig::get('app_sfImageTransformPlugin_font_dir').DIRECTORY_SEPARATOR.$url_font_name.'.ttf';
	      $url = 'https://citation-ou-proverbe.fr/c/'.$citation->id;
	      $box = imagettfbbox($url_font_size, 0, $url_font_dir, $url);
	      $textwidth = abs($box[4] - $box[0]) - 4;

	      $img->text($url, 10, $height-20, $url_font_size, $url_font_name, '#000000');
      }

      return $img;
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
