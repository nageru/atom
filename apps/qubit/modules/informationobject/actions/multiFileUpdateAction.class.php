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

class InformationObjectMultiFileUpdateAction extends sfAction
{
  protected $informationObjectList = array();
  protected $informationObjectOrignalTitles = array();

  protected function earlyExecute()
  {
    $this->resource = $this->getRoute()->resource;

    // Check user authorization
    if (!QubitAcl::check($this->resource, 'update') && !$this->getUser()->hasGroup(QubitAclGroup::EDITOR_ID))
    {
      QubitAcl::forwardUnauthorized();
    }
  }

  public function execute($request)
  { 
    $this->resource = $this->getRoute()->resource;

    // Check that object exists and that it is not the root
    if (!isset($this->resource) || !isset($this->resource->parent))
    {
      $this->forward404();
    }

    $this->requestIds = $request->ids;

    if (isset($request->ids))
    {
      if (false === $this->ids = explode(",", $request->ids))
      {
        $this->forward404();
      }

      foreach ($this->ids as $id)
      {
        if (!ctype_digit($id))
        {
          $this->forward404();
        }

        if (null !== $io = QubitInformationObject::getById($id))
        {
          if (!QubitAcl::check($io, 'update') && !$this->getUser()->hasGroup(QubitAclGroup::EDITOR_ID))
          {
            QubitAcl::forwardUnauthorized();
          }

          // Child IOs should not be root and should be direct descendants of resource.
          if (!isset($io->parent) || $io->parentId !== $this->resource->id)
          {
            $this->forward404();
          }

          $this->informationObjectList[$id] = $io;
          $this->informationObjectOrignalTitles[$id] = $io->title;
        }
        else
        {
          $this->forward404();
        }
      }
    }

    $this->digitalObjectTitleForm = new DigitalObjectTitleUpdateForm(array(), 
      array('informationObjects' => $this->informationObjectList));

    // Handle POST data (form submit)
    if ($request->isMethod('post'))
    {
      $this->digitalObjectTitleForm->bind($request->titles);

        if ($this->digitalObjectTitleForm->isValid())
        {
          $this->updateDigitalObjectTitles($this->digitalObjectTitleForm);
          $this->redirect(array($this->resource, 'module' => 'informationobject'));
        }
    }

    $this->populateDigitalObjectTitleForm($this->digitalObjectTitleForm);
  }

  /**
   * Populate the ui_label form with database values (localized)
   */
  protected function populateDigitalObjectTitleForm($form)
  {
    foreach ($form->getInformationObjects() as $io)
    {
      $form->setDefault($io->id, $io->getTitle(array('cultureFallback' => true)));
    }
  }

  /**
   * Update ui_label db values with form values (localized)
   *
   * @return $this
   */
  protected function updateDigitalObjectTitles($form)
  {
    foreach ($form->getInformationObjects() as $informationObject)
    {
      if (null !== $title = $form->getValue($informationObject->id))
      {
        // Test if title changed.
        if ($this->informationObjectOrignalTitles[$informationObject->id] !== $title)
        {
          $informationObject->title = $title;
          $informationObject->save();
        }
      }
    }

    return $this;
  }

  protected function processForm()
  {
    $this->redirect(array($this->resource, 'module' => 'informationobject'));
  }
}
