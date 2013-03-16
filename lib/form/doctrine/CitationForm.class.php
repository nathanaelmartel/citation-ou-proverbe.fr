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
  	$this->setWidget('author_id', new sfWidgetFormPlain(array('value'=>$this->getObject()->Author->name)));
  	$tags = '';
  	foreach ($this->getObject()->Tags as $tag) {
  		$tags .= $tag.', ';
  	}
  	$this->setWidget('tags_list', new sfWidgetFormPlain(array('value'=>$tags)));
  }
}
