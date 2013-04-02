<?php

/**
 * Tag form.
 *
 * @package    citations-vi
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class TagForm extends BaseTagForm
{
  public function configure()
  {
  	$citations = '<ul>';
  	foreach ($this->getObject()->Citations as $citation) {
  		$citations .= '<li><a href="/citation/'.$citation->id.'/edit">'.$citation->id.'</a> «&nbsp;'.$citation->quote.'&nbsp;»</li>'."\n";
  	}
  	$citations .= '</ul>';
  	$this->setWidget('liste_de_citations', new sfWidgetFormPlain(array('value'=>$citations)));
  }
}
