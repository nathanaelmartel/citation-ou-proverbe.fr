<?php

/**
 * Project form base class.
 *
 * @package    citations-vi
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormBaseTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class BaseFormDoctrine extends sfFormDoctrine
{
  public function setup()
  {
  	unset(  
			$this['created_at'],  
			$this['updated_at'],  
			$this['author_id'],  
			$this['tags_list'],  
			$this['citations_list']
		); 
  }
}
