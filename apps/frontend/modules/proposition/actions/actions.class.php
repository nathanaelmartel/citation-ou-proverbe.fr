<?php

/**
 * proposition actions.
 *
 * @package    citations
 * @subpackage proposition
 * @author     Nathanaël Martel <nathanael@fam-martel.eu>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class propositionActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
        $this->form = new PropositionForm();

    		$response = $this->getResponse();
    		$response->setTitle('Proposer une citation');
  }


    public function executeCreate(sfWebRequest $request)
    {
        $this->forward404Unless($request->isMethod(sfRequest::POST));

        $this->form = new PropositionForm();

        $this->processForm($request, $this->form);

        $this->setTemplate('index');
    }

    protected function processForm(sfWebRequest $request, sfForm $form)
    {

        $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
        if ($form->isValid()) {
            $citation = $form->save();
		    		if (($citation->author_id == '142') && ($request->getParameter('author_name') != '') && ($request->getParameter('author_name') != 'Anonyme')) {
		    			AuthorTable::addAuthor($request->getParameter('author_name'));

			    		$author = Doctrine::getTable('Author')->findOneByName($request->getParameter('author_name'));
			    		$citation->author_id = $author->id;
			    		$citation->save();
		    		}

            $message = Swift_Message::newInstance()
                    ->setFrom(sfConfig::get('app_proposition_email_from'))
                    ->setTo(sfConfig::get('app_proposition_email_to'))
                    ->setSubject(sfConfig::get('app_proposition_email_subject'))
                    ->setContentType('text/html')
                    ->setBody($this->getPartial('proposition/email', array('citation' => $citation, 'form' => $form)));

            foreach (sfConfig::get('app_proposition_email_bcc') as $mail)
            {
                $message->addBcc($mail);
            }
            $this->getMailer()->send($message);

            $this->getUser()->setFlash('confirmation', 'Merci pour votre proposition de citation, nous allons la vérifier avant de la mettre en ligne.');

            if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1')))
            {
            	require_once sfConfig::get('sf_lib_dir').'/vendor/piwik/PiwikTracker.php';
            	PiwikTracker::$URL = 'https://piwik.simplement-web.com/';

            	$piwikTracker = new PiwikTracker( $idSite = 17 );
            	$piwikTracker->setCustomVariable( 5, 'dernière citation proposée', $citation->id, 'visit');
            	$piwikTracker->doTrackPageView('Proposition');
            	$piwikTracker->doTrackGoal($idGoal = 5, $revenue = 100);
            }

            $this->redirect('@new_citation');
        }
    }


    public function executeApprobate(sfWebRequest $request)
    {
        $this->forward404Unless($citation = Doctrine_Core::getTable('Citation')->findOneById(array($request->getParameter('id'))), sprintf('Object citation does not exist (%s).', $request->getParameter('id')));

        $citation->is_active = true;
        $citation->note = 100;
        if ($citation->slug == '')  {
        	$citation->generateSlug();
        }
       	$citation->hash = CitationTable::buidHash($citation->quote);
       	$citation->save();

        $this->getUser()->setFlash('confirmation', 'La nouvelle citation a été approuvée');

        $this->redirect('@citation?slug='.$citation->slug.'&author='.$citation->Author->slug, 301);
    }


}
