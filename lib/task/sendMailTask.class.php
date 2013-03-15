<?php

class sendMailTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'frontend'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = '';
    $this->name             = 'sendMail';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [sendMail|INFO] task does things.
Call it with:

  [php symfony sendMail|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    
    $citations = Doctrine::getTable('Citation')
      ->createQuery('a')
      ->where('is_active = ?', 1)
      ->limit(5)
      ->orderBy('last_published_at desc')
      ->execute();
    $citation = $citations[0];
    
    $message_text = '<a href="http://www.citation-ou-proverbe.fr/'.$citation->Author->slug.'/'.$citation->slug.'?pk_campaign=abonnement&pk_kwd=abonnement-citation" class="card-container" style="width: 460px;display: block;text-decoration: none;">
    <blockquote style="color: '.$citation->getTextRGBColorHex().';background-color: '.$citation->getRGBColorHex() .';width: 460px;display: table-cell;font-size: 1.8em;height: 8em;line-height: 1.2em;padding: 1em;vertical-align: middle;">
    			'. $citation->quote .'</blockquote></a>
    <p>Retrouver d\'autres citations de <a style="color:#000;" href="http://www.citation-ou-proverbe.fr/'.$citation->Author->slug.'?pk_campaign=abonnement&pk_kwd=abonnement-auteur">'. $citation->Author->name.'</a></p>
    <p>
		-- <br />
		L\'équipe de <a href="http://www.citation-ou-proverbe.fr?pk_campaign=abonnement&pk_kwd=abonnement-footer" style="color:#000;">Citation ou Proverbe</a><br />
		<a href="http://www.citation-ou-proverbe.fr/desabonnement/[encoded_mail]">désabonnement</a>
		</p>'; 
        
    
    $newsletters = Doctrine::getTable('Newsletter')
    	->createQuery('a')
        ->where('is_confirmed = ?', 1)
        ->execute();
 
    
    foreach ($newsletters as $newsletter) {
      
      $personalized_message = str_replace('[encoded_mail]', base64_encode($newsletter->getEmail()), $message_text);
      
	  	$message = $this->getMailer()->compose(
	      sfConfig::get('app_newsletter_mail_from'),
	      $newsletter->getEmail(),
	      'citation du jour',
	  	  $personalized_message
	    );
      $message->setContentType("text/html");
  		$this->getMailer()->send($message);
    	sfTask::log($newsletter->getEmail());
    }
    sfTask::log('send '.$citation->id.' at '.date('r').' to '.count($newsletters).' mails');
  }
}
