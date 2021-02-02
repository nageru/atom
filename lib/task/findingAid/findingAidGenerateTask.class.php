<?php

/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Access to Memory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Access to Memory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Generate a finding aid
 *
 * @package    symfony
 * @subpackage task
 * @author     David Juhasz <djjuhasz@gmail.com>
 */
class findingAidGenerateTask extends arBaseTask
{
  protected $namespace        = 'finding-aid';
  protected $name             = 'generate';
  protected $briefDescription = 'Generate a Finding Aid document';

  protected $detailedDescription = <<<EOL
Generate a PDF Finding Aid for the given archival description hierarchy
EOL;

   /**
   * @see sfBaseTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument(
        'slug',
        sfCommandArgument::REQUIRED,
        'The top-level archival description slug')
    ));

    $this->addOptions(array(
      new sfCommandOption(
        'application',
        null,
        sfCommandOption::PARAMETER_OPTIONAL,
        'The application name',
        true
      ),
      new sfCommandOption(
        'env',
        null,
        sfCommandOption::PARAMETER_REQUIRED,
        'The environment',
        'cli'
      ),
      new sfCommandOption(
        'connection',
        null,
        sfCommandOption::PARAMETER_REQUIRED,
        'The connection name',
        'propel'
      ),
      new sfCommandOption(
        'model',
        'm',
        sfCommandOption::PARAMETER_REQUIRED,
        'Finding aid model ("inventory-summary" or "full-details")',
        'inventory-summary'
      ),
      new sfCommandOption(
        'verbose',
        'v',
        sfCommandOption::PARAMETER_NONE,
        'Output extra debugging information',
        null
      ),
    ));
  }

  public function execute($args = array(), $options = array())
  {
    /**
     * @see arBaseTask
     */
    parent::execute($args, $options);

    $resource = QubitInformationObject::getBySlug($args['slug']);

    if (null === $resource)
    {
      $this->log(sprintf('Invalid slug "%s"', $args['slug']));

      die(1);
    }

    $options['logger'] = $this->getLogger($options);

    $writer = new QubitFindingAidWriter($resource, $options);
    $writer->generate();
  }

  private function getLogger($options)
  {
    $logger = new sfConsoleLogger($this->dispatcher);

    if (!empty($options['verbose']))
    {
      $logger->setLogLevel(sfLogger::DEBUG);
    }

    return $logger;
  }
}
