<?php

/**
 * Project filter form base class.
 *
 * @package    citations-vi
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterBaseTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class BaseFormFilterDoctrine extends sfFormFilterDoctrine
{
  public function setup()
  {
  	unset(  
			$this['created_at'],  
			$this['updated_at'],
			$this['citations_list'],
			$this['tags_list'],
			$this['author_id']
		); 
  }
}
