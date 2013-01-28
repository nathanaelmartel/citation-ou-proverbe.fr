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
  	$citations = '<ul>';
  	foreach ($this->getObject()->Citations as $citation) {
  		$citations .= '<li>'.$citation->id.' «'.$citation->quote.'»</li>'."\n";
  	}
  	$citations .= '</ul>';
  	$this->setWidget('citations_list', new sfWidgetFormPlain(array('value'=>$citations)));
  }
}
