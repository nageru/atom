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
class findingAidGenerateTask extends exportBulkBaseTask
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
        'The archival description slug')
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
        sfCommandOption::PARAMETER_NONE,
        'Finding aid modle ("summary" or "', null),
    ));
  }

  /**
   * @see sfTask
   */
  public function execute($arguments = array(), $options = array())
  {
    parent::execute($arguments, $options);

    // Offer to abort if not using --force or --dry-run options
    if (!$options['force'] && !$options['dry-run'])
    {
      $confirmation = $this->askConfirmation("Are you sure you'd like to delete all unlinked physical objects?");

      if (!$confirmation)
      {
        $this->log('Aborted.');
        exit();
      }
    }
  }
}
