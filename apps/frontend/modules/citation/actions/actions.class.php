<?php

/**
 * citation actions.
 *
 * @package    citations
 * @subpackage citation
 * @author     Nathanaël Martel <nathanael@fam-martel.eu>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class citationActions extends sfActions
{
  
  public function executeShort(sfWebRequest $request)
  {
  	$this->forward404Unless($citation = Doctrine_Core::getTable('Citation')->findOneById(array($request->getParameter('id'))), sprintf('Object citation does not exist (%s).', $request->getParameter('id')));
  	
  	if ($citation->slug == '')  {
  		$citation->generateSlug();
    	$citation->save();
  	}
  	
  	$this->redirect('@citation?slug='.$citation->slug.'&author='.$citation->Author->slug, 301);
  }
  
  public function executeShow(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->forward404Unless($citation = Doctrine_Core::getTable('Citation')->findOneBySlug(array($slug)), sprintf('Object citation does not exist (%s).', $slug));
    $this->forward404Unless($citation->is_active);
    
    $response = $this->getResponse();
    $response->addMeta('description', substr($citation->getQuote(), 0, stripos($citation->quote, ' ', 50)+1 ).'... - '.$citation->getAuthor().'. Retrouvez d\'autre citations et proverbe sur notre site.');
    $response->setTitle($citation->Author->name.' : '.$citation->quote );
    
    $this->citation = $citation;
  }
}
