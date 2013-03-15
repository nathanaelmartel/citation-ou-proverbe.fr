<?php

/**
 * Send form.
 *
 * @package    citations
 * @subpackage form
 * @author     Nathanaël Martel <nathanael@fam-martel.eu>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class SendForm extends BaseSendForm
{
  public function configure()
  {
  		  unset(
  		  		$this['created_at'],
	        	$this['updated_at']
        );
  		  
  		  $this->validatorSchema['email'] = new sfValidatorEmail();
        $this->validatorSchema['email_from'] = new sfValidatorEmail();
        
        $this->widgetSchema['citation_id'] = new sfWidgetFormInputHidden();
        $this->widgetSchema['image_url'] = new sfWidgetFormInputHidden();

        $this->widgetSchema->setLabels(array(
          'email_from' => 'Votre email',
          'email' => 'Email du destinataire',
          'subject' => 'Sujet de votre message',
          'comments' => 'Votre message',
      ));
  }
}
