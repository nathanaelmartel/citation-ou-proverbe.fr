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
            $this->redirect('@citation_short?id='.$send->citation_id);
        }
    }
}
