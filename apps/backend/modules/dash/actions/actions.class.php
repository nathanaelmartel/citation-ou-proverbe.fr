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
}
