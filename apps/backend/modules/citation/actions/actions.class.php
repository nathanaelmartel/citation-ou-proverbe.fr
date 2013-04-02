<?php

require_once dirname(__FILE__).'/../lib/citationGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/citationGeneratorHelper.class.php';

/**
 * citation actions.
 *
 * @package    citations-vi
 * @subpackage citation
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class citationActions extends autoCitationActions
{
	public function executeList_removeMedia(sfWebRequest $request) {
    $citation = $this->getRoute()->getObject();
    $notice = '';
    
    $twitter = sfConfig::get('sf_web_dir').'/twitter/'.$citation->Author->slug.'/'.$citation->id.'.png'; 
    if (file_exists($twitter)) {
    	unlink($twitter);
    	$notice .= 'Remove twitter card : '.$twitter."\n";
    }
    
    $wallpaper = sfConfig::get('sf_web_dir').'/medias/'.$citation->Author->slug.'/'.$citation->Author->slug.'.'.$citation->slug.'.png'; 
    if (file_exists($wallpaper)) {
    	unlink($wallpaper);
    	$notice .= 'Remove wallpaper : '.$wallpaper."\n";
    }
    
    if ($notice != '')
    	$this->getUser()->setFlash('notice', $notice);
    
    $this->redirect('/citation/'.$citation->id.'/edit');
	}
}
