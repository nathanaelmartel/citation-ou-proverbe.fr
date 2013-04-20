<?php

/**
 * newsletter actions.
 *
 * @package    citations
 * @subpackage citation
 * @author     Nathanaël Martel <nathanael@fam-martel.eu>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class newsletterActions extends sfActions
{
  public function executeNew(sfWebRequest $request)
  {
    $this->form = new NewsletterForm();
    
    $response = $this->getResponse();
    $response->setTitle('Abonnement');
  }
  
	public function executeCreate(sfWebRequest $request)
	{
		$this->forward404Unless($request->isMethod(sfRequest::POST));

		$this->form = new NewsletterForm();

		$this->processForm($request, $this->form);

		$this->setTemplate('new');
	}

	public function executeEdit(sfWebRequest $request)
	{
	}

	public function executeUpdate(sfWebRequest $request)
	{
		$this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
		$this->forward404Unless($newsletter = Doctrine_Core::getTable('Newsletter')->find(array($request->getParameter('id'))), sprintf('Object newsletter does not exist (%s).', $request->getParameter('id')));
		$this->form = new NewsletterForm($newsletter);

		$this->processForm($request, $this->form);

		$this->setTemplate('edit');
	}

	protected function processForm(sfWebRequest $request, sfForm $form)
	{
		$form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
		if ($form->isValid())
		{
      $newsletter = $form->save();
      
			$message = Swift_Message::newInstance()
			->setFrom(sfConfig::get('app_newsletter_mail_from'))
			->setReplyTo(sfConfig::get('app_newsletter_mail_from'))
			->setTo($newsletter->getEmail())
			->setSubject(sfConfig::get('app_newsletter_mail_subject_newsletter'))
			->setContentType('text/html')
			->setBody($this->getPartial('confirmation_client', array('newsletter' => $newsletter)));
			$this->getMailer()->send($message);

			$this->redirect('newsletter/edit');
		}
	}

	public function executeConfirmation(sfWebRequest $request)
	{
		$email = base64_decode($request->getParameter('code'));

		$this->forward404Unless($newsletter = Doctrine_Core::getTable('Newsletter')->findOneByEmail(array($email)), sprintf('Object newsletter does not exist (%s).', $request->getParameter('code')));
		
		if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1')))
		{
			require_once sfConfig::get('sf_lib_dir').'/vendor/piwik/PiwikTracker.php';
			PiwikTracker::$URL = 'http://piwik.fam-martel.eu/';
            
      $this->getUser()->setAttribute('mail', $contact->email);
		
			$piwikTracker = new PiwikTracker( $idSite = 17 );
			$piwikTracker->setCustomVariable( 1, 'email', $email, 'visit');
			$piwikTracker->setCustomVariable( 3, 'newsletter', 'inscrit', 'visit');
			$piwikTracker->doTrackPageView('Abonnement');
			$piwikTracker->doTrackGoal($idGoal = 4, $revenue = 100);
		}
		
		$newsletter->is_confirmed = true;
		$newsletter->save();
	}
  
  public function executeDel(sfWebRequest $request)
  {
    $email = base64_decode($request->getParameter('code'));

    $this->forward404Unless($newsletter = Doctrine_Core::getTable('Newsletter')->findOneByEmail(array($email)), sprintf('Object newsletter does not exist (%s).', $request->getParameter('code')));

    $newsletter->is_confirmed = false;
    $newsletter->save();
    $newsletter->delete();
  }
  
}



