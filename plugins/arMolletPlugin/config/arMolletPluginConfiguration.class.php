<?php

class arMolletPluginConfiguration extends sfPluginConfiguration
{
  // Summary and version. AtoM recognizes any plugin as a theme as long as
  // the $summary string contains the word "theme" in it (case-insensitive).
  public static
    $summary = 'Theme plugin for Ajuntament de Mollet del Vallès',
    $version = '1.0.';

  public function contextLoadFactories(sfEvent $event)
  {
    // Here we are including the CSS stylesheet build in our pages.
    $context = $event->getSubject();
    $context->response->addStylesheet('/plugins/arMolletPlugin/css/main.css', 'last', array('media' => 'all'));
  }

  public function initialize()
  {
    // Run the class method contextLoadFactories defined above once Symfony
    // is done loading the internal framework factories.
    $this->dispatcher->connect('context.load_factories', array($this, 'contextLoadFactories'));

    // This allows us to override the application decorators.
    $decoratorDirs = sfConfig::get('sf_decorator_dirs');
    $decoratorDirs[] = $this->rootDir.'/templates';
    sfConfig::set('sf_decorator_dirs', $decoratorDirs);

    // This allows us to override the contents of the application modules.
    $moduleDirs = sfConfig::get('sf_module_dirs');
    $moduleDirs[$this->rootDir.'/modules'] = false;
    sfConfig::set('sf_module_dirs', $moduleDirs);
  }
}
