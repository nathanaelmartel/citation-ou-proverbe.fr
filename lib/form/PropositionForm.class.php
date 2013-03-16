<?php

/**
 * Proposition form.
 *
 * @package    citations-vi
 * @subpackage form
 * @author     Nathanaël Martel <nathanael@fam-martel.eu>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PropositionForm extends BaseCitationForm
{
  public function configure()
  {
  	unset(
  			$this['last_published_at'],
  			$this['is_active'],
  			$this['hash'],
  			$this['note'],
  			$this['view'],
  			$this['slug'],
  			$this['color'],
  			$this['text_color'],
  			$this['created_at'],
  			$this['updated_at']
  	);
  	
  	$this->setWidget('author_id', new sfWidgetFormInputHidden());
  }
}
