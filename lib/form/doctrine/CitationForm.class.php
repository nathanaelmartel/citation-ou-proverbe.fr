<?php

/**
 * Citation form.
 *
 * @package    citations-vi
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class CitationForm extends BaseCitationForm
{
  public function configure()
  {
  	unset(   
			$this['author_id']
		); 
  	$this->setWidget('auteur', new sfWidgetFormPlain(array('value' => '<a href="/author/'.$this->getObject()->Author->id.'/edit">'.$this->getObject()->Author->name.'</a>')));
  	$tags = '';
  	foreach ($this->getObject()->Tags as $tag) {
  		$tags .= '<a href="/tag/'.$tag->id.'/edit">'.$tag.'</a>, ';
  	}
  	$this->setWidget('liste_de_tag', new sfWidgetFormPlain(array('value'=>$tags)));
  }
}
