<?php

/**
 * dash actions.
 *
 * @package    citations-vi
 * @subpackage dash
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class dashActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executePage(sfWebRequest $request)
  {
  	$this->q = Doctrine_Manager::getInstance()->getCurrentConnection();
  }
  
  public function executeCitation(sfWebRequest $request)
  {
  	$this->q = Doctrine_Manager::getInstance()->getCurrentConnection();
  }
  
  public function executeAuthor(sfWebRequest $request)
  {
  	$this->q = Doctrine_Manager::getInstance()->getCurrentConnection();
  }
  
  public function executeAuthorMerge(sfWebRequest $request)
  {
  	$old_author_id = $request->getParameter('old_author_id');
  	$new_author_id = $request->getParameter('new_author_id');
  	
  	if ($old_author_id && $new_author_id) {
    	$old_author = Doctrine_Core::getTable('Author')->findOneById($old_author_id);
    	$new_author = Doctrine_Core::getTable('Author')->findOneById($new_author_id);
    	$do_modif = true;
	    	
	    	// quel nom conserver ?
	    	$final_name = $old_author->name;
	    	foreach ($old_author->DBPedia as $dbpedia) {
	    		$final_name = $dbpedia->name;
	    	}
	    	foreach ($old_author->Wikipedia as $wikipedia) {
	    		$final_name = $wikipedia->name;
	    	}
	    	$final_name = $new_author->name;
	    	foreach ($new_author->DBPedia as $dbpedia) {
	    		$final_name = $dbpedia->name;
	    	}
	    	foreach ($new_author->Wikipedia as $wikipedia) {
	    		$final_name = $wikipedia->name;
	    	}
	    	
	    	
	    	// merge citations
	    	foreach ($old_author->Citations as $citation) {
		    	if ($do_modif) {
			    	$citation->author_id = $new_author->id;
			    	$citation->save();
		    	}
	    	}
	    	
	    	// merge dbpedia
	    	foreach ($old_author->DBPedia as $dbpedia) {
		    	if ($do_modif) {
			    	$dbpedia->author_id = $new_author->id;
			    	$dbpedia->save();
		    	}
	    	}
	    	
	    	// merge wikipedia
	    	foreach ($old_author->Wikipedia as $wikipedia) {
		    	if ($do_modif) {
			    	$wikipedia->author_id = $new_author->id;
			    	$wikipedia->save();
		    	}
	    	}
	    	
	    	// remove old
	    	if ($do_modif) {
	    		$old_author->delete();
		    	$new_author->name = $final_name;
		    	$new_author->wikipedia_at = null;
		    	$new_author->dbpedia_at = null;
		    	$new_author->save();
	    	}
	  	
	  	$notice = 'Merge : '.$old_author_id.' => '.$new_author_id.' '.$final_name.' [citations: '.count($new_author->Citations).']'."\n";
	    
	    if ($notice != '')
	    	$this->getUser()->setFlash('notice', $notice);
	    
    	$this->redirect('/author/'.$new_author->id.'/edit');
  	}
  	
  }
  
  
  public function executeSearch(sfWebRequest $request)
  {
  	$term = $request->getParameter('term');
  	
  	$auteurs = Doctrine::getTable('Author')->retrieveForSelect($term);
  	 
  	$this->getResponse()->setContentType('application/json');
  	$this->setLayout(false);
  	return $this->renderText(json_encode($auteurs));
  }
}
