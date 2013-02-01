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
  public function executeShow(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->forward404Unless($citation = Doctrine_Core::getTable('Citation')->findOneBySlug(array($slug)), sprintf('Object citation does not exist (%s).', $slug));
    $this->forward404Unless($citation->is_active);
    
  
    $format = $request->getParameter('sf_format');
    if (($format != 'jpg') && ($format != 'gif') && ($format != 'png'))
      $format = 'jpg';
    
    $width = '1024';
    $height = '768';
    
    $filename = sfConfig::get('sf_web_dir').'/medias/'.$citation->Author->slug; 
    if (!file_exists($filename))
    	mkdir($filename);
    
    $filename .= '/'.$citation->slug.'.'.$format;
    
    if (file_exists($filename)) {
      $img = new sfImage($filename);
    } else {
      $img = new sfImage();
			include('../../../../../lib/vendor/simplement/couleur.class.php') ;
      
      $img->thumbnail($width, $height, 'center');
      $img->negate();
      if ($citation->color) {
      	$rgb = json_decode($citation->color);
      } else {
      	$rgb = couleur::hsl_rgb(rand(0, 100)/100, .8, .8);
      	$citation->color = json_encode($rgb);
      	$citation->save();
      }
      $img->colorize($rgb[0], $rgb[1], $rgb[2], 1);
      
      
      
      $img->setMIMEType('image/'.$format);
      //$img->saveAs($filename, 'image/'.$format);
    }
    
    
    $response = $this->getResponse();
    $response->setContentType($img->getMIMEType());    
    $response->setContent($img); 
    
    return sfView::NONE;
  }
  
  public function executeShort(sfWebRequest $request)
  {
  	$this->forward404Unless($citation = Doctrine_Core::getTable('Citation')->findOneById(array($request->getParameter('id'))), sprintf('Object citation does not exist (%s).', $request->getParameter('id')));
  	
  	$this->redirect('@citation_image?sf_format=jpg&slug='.$citation->slug.'&author='.$citation->Author->slug, 301);
  }
}
