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
  
  public function executeRecherche(sfWebRequest $request)
  {
  	$term = $request->getParameter('author_search');
  	
  	$authors = Doctrine::getTable('Author')->retrieveForSelect($term, false);
  	
  	if (count($authors) == 1)
  		$this->redirect('@author?slug='.$authors[0]->slug, 301);
  	
  	$this->authors = $authors;
  	$this->term = $term;
  	
		$response = $this->getResponse();
		$response->addJavascript(sfConfig::get('sf_js_dir'). 'jquery-jqueryui.min.js');
		$response->addJavascript(sfConfig::get('sf_js_dir'). 'script.js');
    $response->addMeta('description', 'Recherche par Auteurs de Citations ');
    $response->setTitle('Recherche par Auteurs de Citations ' );
  }
  
  public function executeSearch(sfWebRequest $request)
  {
  	$term = $request->getParameter('term');
  	
  	$auteurs = Doctrine::getTable('Author')->retrieveForSelect($term);
  	 
  	$this->getResponse()->setContentType('application/json');
  	$this->setLayout(false);
  	return $this->renderText(json_encode($auteurs));
  }
  
  public function executeShow(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->forward404Unless($author = Doctrine_Core::getTable('Author')->findOneBySlug(array($slug)), sprintf('Object citation does not exist (%s).', $slug));
    $this->forward404Unless($author->is_active);
    
    $response = $this->getResponse();
    $response->addMeta('description', 'Citation ou Proverbe de : '.$author->name.'. Retrouvez d\'autre citations et proverbe sur notre site.');
    $response->setTitle('Citation ou Proverbe de : '.$author->name );
    
    $author->view = $author->view + 1;
    $author->save();
    
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
  	$dbh = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
  	
		$top_query = 'SELECT a.name, a.slug, count(c.id) as nb 
			FROM author a left join citation c ON a.id = c.author_id 
			WHERE a.is_active = 1 and c.is_active = 1 and a.has_thumbnail = 1 
			GROUP BY a.name HAVING nb > 100 LIMIT 10';
		
		$this->top_authors = $dbh->query($top_query); 
  	
		$query = 'SELECT a.name, a.slug, count(c.id) as nb 
			FROM author a left join citation c ON a.id = c.author_id 
			WHERE a.is_active = 1 and c.is_active = 1 
			GROUP BY a.name HAVING nb > 70 LIMIT 10, 100';
		
		$this->authors = $dbh->query($query); 
		
		$response = $this->getResponse();
		$response->addJavascript(sfConfig::get('sf_js_dir'). 'jquery-jqueryui.min.js');
		$response->addJavascript(sfConfig::get('sf_js_dir'). 'script.js');
    $response->addMeta('description', 'Auteurs de Citations ');
    $response->setTitle('Auteurs de Citations ' );
  }
}
