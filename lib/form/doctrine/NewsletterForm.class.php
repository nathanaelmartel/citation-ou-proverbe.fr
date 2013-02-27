<?php

/**
 * Newsletter form.
 *
 * @package    citations
 * @subpackage form
 * @author     Nathanaël Martel <nathanael@fam-martel.eu>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class NewsletterForm extends BaseNewsletterForm
{
  public function configure()
  {
    unset(
          $this['id'],
          $this['updated_at'],
          $this['created_at'], 
          $this['is_confirmed']
       );
       
    $this->setValidators(array(
      'email'   => new sfValidatorEmail()
    ));
  }
}
