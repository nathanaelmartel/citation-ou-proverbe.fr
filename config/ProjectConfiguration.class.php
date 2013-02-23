<?php

require_once dirname(__FILE__).'/../lib/vendor/symfony/lib/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();
require_once(dirname(__FILE__).'/../plugins/swCombinePlugin/lib/config/swCombineViewConfigHandler.class.php');

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
         $this->enablePlugins(array(
             'sfDoctrinePlugin',
             'sfDoctrineGuardPlugin',
             'sfAdminDashPlugin',
             'sfImageTransformPlugin',
        		 'sfFeed2Plugin',
             'swCombinePlugin'
         ));
  }
}
