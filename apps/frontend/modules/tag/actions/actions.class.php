<?php

/**
 * tag actions.
 *
 * @package    citations
 * @subpackage tag
 * @author     Nathanaël Martel <nathanael@fam-martel.eu>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class tagActions extends sfActions
{
  
  public function executeShort(sfWebRequest $request)
  {
  	$this->forward404Unless($tag = Doctrine_Core::getTable('Tag')->findOneById(array($request->getParameter('id'))), sprintf('Object citation does not exist (%s).', $request->getParameter('id')));
  	
  	$this->redirect('@tag?slug='.$tag->slug, 301);
  }
  
  public function executeShow(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->forward404Unless($tag = Doctrine_Core::getTable('Tag')->findOneBySlug(array($slug)), sprintf('Object citation does not exist (%s).', $slug));
    $this->forward404Unless($tag->is_active);
    
    $response = $this->getResponse();
    $response->addMeta('description', 'Citation ou Proverbe sur le thème : '.$tag->name.'. Retrouvez d\'autre citations et proverbe sur notre site.');
    $response->setTitle('Citation ou Proverbe sur le thème : '.$tag->name );
    
    $this->citations = new sfDoctrinePager('Citation', sfConfig::get('app_pager'));
		$this->citations->setQuery(Doctrine_Query::create()
	    ->select('*')
	    ->from('Citation c')
			->leftJoin('TagCitation t')
	    ->where('t.tag_id = ?', $tag->id)
	    ->andWhere('t.citation_id = c.id'));
		$this->citations->setPage($request->getParameter('page', 1));
		$this->citations->init();
    
    $this->tag = $tag;
  }
  
  public function executeIndex(sfWebRequest $request)
  {
    $response = $this->getResponse();
    $response->setTitle('Thèmes des Citations' );
    
    $this->tags = new sfDoctrinePager('Tag', sfConfig::get('app_pager'));
		$this->tags->setQuery(Doctrine_Query::create()
	    ->select('*')
	    ->from('Tag'));
		$this->tags->setPage($request->getParameter('page', 1));
		$this->tags->init();
  }
}
