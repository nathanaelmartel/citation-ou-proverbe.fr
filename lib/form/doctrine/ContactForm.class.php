<?php

/**
 * Contact form.
 *
 * @package    citations
 * @subpackage form
 * @author     Nathanaël Martel <nathanael@fam-martel.eu>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ContactForm extends BaseContactForm
{
  public function configure()
  { 
  		  unset($this['created_at'],
	        $this['updated_at']
        );

        $this->validatorSchema['email'] = new sfValidatorEmail();


        $this->widgetSchema->setLabels(array(
          'name' => 'Nom',
          'email' => 'Email',
          'comments' => 'Message',
      ));
  }
}
