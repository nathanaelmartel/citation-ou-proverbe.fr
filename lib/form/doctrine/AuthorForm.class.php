<?php

/**
 * Author form.
 *
 * @package    citations-vi
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class AuthorForm extends BaseAuthorForm
{
  public function configure()
  {
  	$this->setWidget('short_description', new sfWidgetFormPlain(array('value'=>$this->getObject()->getShortDescription())));
  	$this->setWidget('description', new sfWidgetFormPlain(array('value'=>$this->getObject()->getDescription())));
  	$this->setWidget('wikipedia', new sfWidgetFormPlain(array('value'=>'<a href="'.$this->getObject()->getWikipediaUrl().'">'.$this->getObject()->getWikipediaUrl().'</a>')));
  	$this->setWidget('dates', new sfWidgetFormPlain(array('value'=>$this->getObject()->getDates())));
  	$this->setWidget('nb_citations', new sfWidgetFormPlain(array('value'=>$this->getObject()->getNbCitations())));
  	$citations = '<ul>';
  	foreach ($this->getObject()->Citations as $citation) {
  		$citations .= '<li><a href="/citation/'.$citation->id.'/edit">'.$citation->id.'</a> «&nbsp;'.$citation->quote.'&nbsp;»</li>'."\n";
  	}
  	$citations .= '</ul>';
  	$this->setWidget('liste_de_citations', new sfWidgetFormPlain(array('value'=>$citations)));
  }
}
