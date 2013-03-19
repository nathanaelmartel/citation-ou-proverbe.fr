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
  
  public function executeRandom(sfWebRequest $request)
  {
    $citations = Doctrine::getTable('Citation')
      ->createQuery('a')
      ->where('is_active = ?', 1)
      ->limit(50)
      ->offset(rand(0, 1000))
      ->orderBy('last_published_at desc')
      ->execute(); 
    
    $citation = $citations[rand(0, 49)];
    
    $this->getResponse()->setContentType('application/json');
    return $this->renderText(json_encode($citation->getApiData()));
  }
  
  public function executeShow(sfWebRequest $request)
  {
    $id = $request->getParameter('id');
    $this->forward404Unless($citation = Doctrine_Core::getTable('Citation')->findOneById(array($id)), sprintf('Object citation does not exist (%s).', $id));
    $this->forward404Unless($citation->is_active);
    
    $citation->view = $citation->view + 1;
    $citation->save();
    
    $this->getResponse()->setContentType('application/json');
    return $this->renderText(json_encode($citation->getApiData()));
  }
  
  public function executeLast(sfWebRequest $request)
  {
  	$citations = Doctrine_Core::getTable('Citation')->retrieveLast();
  	
  	$response = array();
  	foreach ($citations as $citation) {
  		$response[] = $citation->getApiData();
  	}
  	
    $this->getResponse()->setContentType('application/json');
    return $this->renderText(json_encode($response));
  }
}
