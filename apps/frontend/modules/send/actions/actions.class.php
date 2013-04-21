<?php

/**
 * citation actions.
 *
 * @package    citations
 * @subpackage citation
 * @author     Nathanaël Martel <nathanael@fam-martel.eu>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sendActions extends sfActions
{
    public function executeIndex(sfWebRequest $request)
    {
    		$id = $request->getParameter('id');
  			$this->forward404Unless($citation = Doctrine_Core::getTable('Citation')->findOneById(array($id)), sprintf('Object citation does not exist (%s).', $id));

  			$this->citation = $citation;
        $this->form = new SendForm();
    		$this->form->setDefault('citation_id', $id);
    		$this->form->setDefault('image_url', $this->getController()->genUrl('@citation_image?sf_format=png&slug='.$citation->slug.'&author='.$citation->Author->slug.'&authorb='.$citation->Author->slug, array('absolute' => true)));
    		
    		$response = $this->getResponse();
    		$response->setTitle('Envoyer la citation par mail');
    }


    public function executeCreate(sfWebRequest $request)
    {
        $this->forward404Unless($request->isMethod(sfRequest::POST));

        $this->form = new SendForm();

        $this->processForm($request, $this->form);

        $this->setTemplate('index');
    }

    protected function processForm(sfWebRequest $request, sfForm $form)
    {
        $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
        if ($form->isValid()) {
            $send = $form->save();
  					$this->forward404Unless($citation = Doctrine_Core::getTable('Citation')->findOneById(array($send->citation_id)), sprintf('Object citation does not exist (%s).', $send->citation_id));

            $message = Swift_Message::newInstance()
                    ->setFrom($send->email_from)
                    ->setTo($send->email)
                    ->setReplyTo($send->email_from)
                    ->setSubject($send->subject?$send->subject:sfConfig::get('app_Send_email_subject'))
                    ->setContentType('text/html')
                    ->attach(Swift_Attachment::fromPath($send->image_url))
                    ->setBody($this->getPartial('send/email', array('send' => $send, 'citation' => $citation, 'form' => $form)));

            foreach (sfConfig::get('app_Send_email_bcc') as $mail)
            {
                $message->addBcc($mail);
            }
            $this->getMailer()->send($message);

            $this->getUser()->setFlash('confirmation', 'Votre message a bien été envoyé !');
      			$this->getUser()->setAttribute('mail', $contact->email_from);	
            
            if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1')))
            {
            	require_once sfConfig::get('sf_lib_dir').'/vendor/piwik/PiwikTracker.php';
            	PiwikTracker::$URL = 'http://piwik.fam-martel.eu/';
            
            	$piwikTracker = new PiwikTracker( $idSite = 17 );
            	$piwikTracker->setCustomVariable( 1, 'email', $send->email_from, 'visit');
            	$piwikTracker->setCustomVariable( 4, 'dernière citation envoyée', $send->citation_id, 'visit');
            	$piwikTracker->doTrackPageView('Envoyer la citation par mail');
            	$piwikTracker->doTrackGoal($idGoal = 2, $revenue = 100);
            }
            
            $this->redirect('@citation_short?id='.$send->citation_id);
        }
    }
}
