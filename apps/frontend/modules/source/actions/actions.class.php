<?php

/**
 * source actions.
 *
 * @package    citations
 * @subpackage source
 * @author     Nathanaël Martel <nathanael@fam-martel.eu>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sourceActions extends sfActions
{
  
  public function executeShort(sfWebRequest $request)
  {
  	$this->forward404Unless($source = Doctrine_Core::getTable('Source')->findOneById(array($request->getParameter('id'))), sprintf('Object source does not exist (%s).', $request->getParameter('id')));
  	
  	$this->redirect('@source?slug='.$source->slug.'&author='.$source->Author->slug, 301);
  }
  
  public function executeShow(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->forward404Unless($source = Doctrine_Core::getTable('Source')->findOneBySlug(array($slug)), sprintf('Object source does not exist (%s).', $slug));
    $this->forward404Unless($source->is_active);
    
    $response = $this->getResponse();
    $response->addMeta('description', 'Citation ou Proverbe de : '.$source->title.' - '.$source->Author->name.'. Retrouvez d\'autre citations et proverbe sur notre site.');
    $response->setTitle('Citation ou Proverbe de : '.$source->title.' - '.$source->Author->name );
    
    $source->view = $source->view + 1;
    $source->save();
    
    $this->citations = new sfDoctrinePager('Citation', sfConfig::get('app_pager'));
		$this->citations->setQuery(Doctrine_Query::create()
	    ->select('*')
	    ->from('Citation')
	    ->where('source_id = ?', $source->id)
			->andWhere('is_active = ?', true));
		$this->citations->setPage($request->getParameter('page', 1));
		$this->citations->init();
		
		$this->source = $source;
    $this->author = $source->Author;
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
    $response->addMeta('description', 'Auteurs de Citations ');
    $response->setTitle('Auteurs de Citations ' );
  }
}
