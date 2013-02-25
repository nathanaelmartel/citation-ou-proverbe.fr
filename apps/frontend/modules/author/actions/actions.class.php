<?php

/**
 * author actions.
 *
 * @package    citations
 * @subpackage author
 * @author     Nathanaël Martel <nathanael@fam-martel.eu>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class authorActions extends sfActions
{
  
  public function executeShort(sfWebRequest $request)
  {
  	$this->forward404Unless($author = Doctrine_Core::getTable('Author')->findOneById(array($request->getParameter('id'))), sprintf('Object citation does not exist (%s).', $request->getParameter('id')));
  	
  	$this->redirect('@author?slug='.$author->slug, 301);
  }
  
  public function executeShow(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->forward404Unless($author = Doctrine_Core::getTable('Author')->findOneBySlug(array($slug)), sprintf('Object citation does not exist (%s).', $slug));
    $this->forward404Unless($author->is_active);
    
    $response = $this->getResponse();
    $response->addMeta('description', 'Citation ou Proverbe de : '.$author->name.'. Retrouvez d\'autre citations et proverbe sur notre site.');
    $response->setTitle('Citation ou Proverbe de : '.$author->name );
    
    $this->citations = new sfDoctrinePager('Citation', sfConfig::get('app_pager'));
		$this->citations->setQuery(Doctrine_Query::create()
	    ->select('*')
	    ->from('Citation')
	    ->where('author_id = ?', $author->id));
		$this->citations->setPage($request->getParameter('page', 1));
		$this->citations->init();
    
    $this->author = $author;
  }
  
  public function executeIndex(sfWebRequest $request)
  {
    $response = $this->getResponse();
    $response->setTitle('Auteurs de Citations' );
    
    $this->authors = new sfDoctrinePager('Author', sfConfig::get('app_pager'));
		$this->authors->setQuery(Doctrine_Query::create()
	    ->select('*')
	    ->from('Author'));
		$this->authors->setPage($request->getParameter('page', 1));
		$this->authors->init();
  }
}
