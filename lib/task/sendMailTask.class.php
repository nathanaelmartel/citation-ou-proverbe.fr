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


  	$baseline = array(
  		'«&nbsp;L’education est l’arme la plus puissante pour changer le monde&nbsp;» — Nelson Mandela',
  		'L’éducation de nos enfants est notre priorité : nous nous sommes donnés comme objectif de construire une école',
  		'«&nbsp;Si tu veux aller vite, marche seul ; mais si tu veux aller loin, marchons ensemble&nbsp;» — proverbe africain',
  		'«&nbsp;Un Enfant sans éducation est comme un oiseau sans ailes&nbsp;» — proverbe Tibétain',
  		'Vous pouvez lire et écrire ceci… Pas les enfants de Niellé.',
  	);

    $annonce = '<p>Découvrez <a href="http://revenudebase.info/?utm_source=citation-ou-proverbe.fr&utm_medium=email&utm_campaign=citation-ou-proverbe.fr" style="color:#000;">Le Revenu de base</a>, et vous, que feriez-vous si votre revenu était garanti ?</p>';
    $annonce = '<p>Accrochez des citations à vos murs avec <a href="http://www.wallshop.fr/fr/?pk_campaign=email-citation-ou-proverbe&pk_kwd=email-footer-citation-ou-proverbe" style="color:#000;">WallShop.fr</a></p>';

    $annonce = '<p>'.$baseline[rand(0, count($baseline)-1)].' <a href="http://www.badessatellites.com/?utm_medium=email&utm_campaign=citation-ou-proverbe.fr">Une École à Niellé</a></p>';

    $message_text = '<a href="https://www.citation-ou-proverbe.fr/'.$citation->Author->slug.'/'.$citation->slug.'?pk_campaign=abonnement&pk_kwd=abonnement-citation" class="card-container" style="width: 460px;display: block;text-decoration: none;">
    <blockquote style="color: '.$citation->getTextRGBColorHex().';background-color: '.$citation->getRGBColorHex() .';width: 460px;display: table-cell;font-size: 1.8em;height: 8em;line-height: 1.2em;padding: 1em;vertical-align: middle;">
    			'. $citation->quote .'</blockquote></a>
    <p>Retrouver d\'autres citations de <a style="color:#000;" href="https://www.citation-ou-proverbe.fr/'.$citation->Author->slug.'?pk_campaign=abonnement&pk_kwd=abonnement-auteur">'. $citation->Author->name.'</a></p>
    '.$annonce.'
    <p>
		-- <br />
		L\'équipe de <a href="https://www.citation-ou-proverbe.fr?pk_campaign=abonnement&pk_kwd=abonnement-footer" style="color:#000;">Citation ou Proverbe</a><br />
		<a href="https://www.citation-ou-proverbe.fr/desabonnement/[encoded_mail]?pk_campaign=abonnement&pk_kwd=abonnement-auteur">se désabonner</a><br />
		<a href="https://www.citation-ou-proverbe.fr/proposer-citation?pk_campaign=abonnement&pk_kwd=abonnement-auteur">proposer une citation</a>
		</p>';


    $q = Doctrine::getTable('Newsletter')
    ->createQuery('a')
    ->where('is_confirmed = ?', 1)
    ->andWhere('hour(TIMEDIFF(now(), last_send_at)) > ?', 36)
    ->limit(35)
    ->orderBy('last_send_at ASC');
//    die($q->getSqlQuery());
    $newsletters = $q->execute();


    foreach ($newsletters as $newsletter) {

      $personalized_message = str_replace('[encoded_mail]', base64_encode($newsletter->getEmail()), $message_text);

	  	$message = $this->getMailer()->compose(
	      sfConfig::get('app_newsletter_mail_from'),
	      $newsletter->getEmail(),
	      'citation du jour',
	  	  $personalized_message
	    );
      $message->setContentType("text/html");
      $message->setContentType("text/html");
      if ($this->getMailer()->send($message)) {
    		sfTask::log('  -> ok');
	    	$newsletter->last_send_at = new Doctrine_Expression('NOW()');
	    	$newsletter->save();
      } else {
    		sfTask::log('  -> #failed');
      }

    }
    sfTask::log('send '.$citation->id.' at '.date('r').' to '.count($newsletters).' mails');
  }
}
