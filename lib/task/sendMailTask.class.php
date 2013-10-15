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
    
    $q = Doctrine_Query::create()
    ->select('*')
    ->from('Citation c')
    ->where('is_active = ?', 1)
    ->offset(rand(0, rand(0, 10000)))
    ->limit(10)
    ->orderBy('created_at desc');
    
    $citation = $q->fetchOne();
    //sfTask::log($citation->quote);die();
    
    $annonce = '<p>Découvrez <a href="http://revenudebase.info/?utm_source=citation-ou-proverbe.fr&utm_medium=email&utm_campaign=citation-ou-proverbe.fr" style="color:#000;">Le Revenu de base</a>, et vous, que feriez-vous si votre revenu était garanti ?</p>';
     
    $message_text = '<a href="http://www.citation-ou-proverbe.fr/'.$citation->Author->slug.'/'.$citation->slug.'?pk_campaign=abonnement&pk_kwd=abonnement-citation" class="card-container" style="width: 460px;display: block;text-decoration: none;">
    <blockquote style="color: '.$citation->getTextRGBColorHex().';background-color: '.$citation->getRGBColorHex() .';width: 460px;display: table-cell;font-size: 1.8em;height: 8em;line-height: 1.2em;padding: 1em;vertical-align: middle;">
    			'. $citation->quote .'</blockquote></a>
    <p>Retrouver d\'autres citations de <a style="color:#000;" href="http://www.citation-ou-proverbe.fr/'.$citation->Author->slug.'?pk_campaign=abonnement&pk_kwd=abonnement-auteur">'. $citation->Author->name.'</a></p>
    '.$annonce.'
    <p>
		-- <br />
		L\'équipe de <a href="http://www.citation-ou-proverbe.fr?pk_campaign=abonnement&pk_kwd=abonnement-footer" style="color:#000;">Citation ou Proverbe</a><br />
		<a href="http://www.citation-ou-proverbe.fr/desabonnement/[encoded_mail]?pk_campaign=abonnement&pk_kwd=abonnement-auteur">se désabonner</a><br />
		<a href="http://www.citation-ou-proverbe.fr/proposer-citation?pk_campaign=abonnement&pk_kwd=abonnement-auteur">proposer une citation</a>
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
