<?php

class sfWidgetFormPlain extends sfWidgetForm
{
	/**
	 * Constructor.
	 *
	 * Available options:
	 *
	 *  * type: The widget type
	 *
	 * @param array $options     An array of options
	 * @param array $attributes  An array of default HTML attributes
	 *
	 * @see sfWidgetForm
	 */
	protected function configure($options = array(), $attributes = array())
	{
		$this->addOption('value');
	}

	/**
	 * @param  string $name        The element name
	 * @param  string $value       The value displayed in this widget
	 * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
	 * @param  array  $errors      An array of errors for the field
	 *
	 * @return string An HTML tag string
	 *
	 * @see sfWidgetForm
	 */
	public function render($name, $value = null, $attributes = array(), $errors = array())
	{
		//optional - for easy css styling
		$attributes['class'] = 'frozen';

		return $this->renderContentTag('div', $this->getOption('value'), $attributes);
	}
}




/**
 * Project form base class.
 *
 * @package    citations-vi
 * @subpackage form
 * @author     Nathanaël Martel <nathanael@fam-martel.eu>
 * @version    SVN: $Id: sfDoctrineFormBaseTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class BaseFormDoctrine extends sfFormDoctrine
{
  public function setup()
  {
  	unset(  
			$this['created_at'],  
			$this['updated_at'],  
			$this['tags_list'],  
			$this['citations_list']
		); 
  	
  	$this->setWidget('created_at', new sfWidgetFormPlain(array('value'=>$this->getObject()->created_at)));
  	$this->setWidget('updated_at', new sfWidgetFormPlain(array('value'=>$this->getObject()->updated_at)));
  }
}
