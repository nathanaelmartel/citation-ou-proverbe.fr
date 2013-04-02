<?php

require_once dirname(__FILE__).'/../lib/authorGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/authorGeneratorHelper.class.php';

/**
 * author actions.
 *
 * @package    citations-vi
 * @subpackage author
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class authorActions extends autoAuthorActions
{
	public function executeList_removeMedia(sfWebRequest $request) {
    $author = $this->getRoute()->getObject();
    $notice = '';
    
   	$original_file = sfConfig::get('sf_web_dir').'/portrait/'.$author->slug.'/';
    $handle = opendir($original_file);
    while (false !== ($entry = readdir($handle))) {
      if (substr_count($entry, 'original') > 0) {
	      unlink($original_file.$entry);
	      $notice .= 'Remove original : '.$original_file.$entry."\n";
      }
    }
    closedir($handle);
    $author->has_thumbnail = false;
    $author->save();
    
   	$filename = sfConfig::get('sf_web_dir').'/medias/'.$author->slug;
    $handle = opendir($original_file);
    while (false !== ($entry = readdir($handle))) {
      if (substr_count($entry, 'portrait.'.$author->slug) > 0) {
	      unlink($original_file.$entry);
	      $notice .= 'Remove portrait : '.$original_file.$entry."\n";
      }
    }
    closedir($handle);
    
    if ($notice != '')
    	$this->getUser()->setFlash('notice', $notice);
    
    $this->redirect('/author/'.$author->id.'/edit');
	}
}
