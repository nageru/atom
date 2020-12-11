<?php use_helper('Javascript') ?>
<?php decorate_with('layout_1col.php') ?>

<?php slot('title') ?>
  <h1 class="multiline">
    <?php echo __('Import multiple digital objects') ?>
    <span class="sub"><?php echo render_title(new sfIsadPlugin($resource)) ?> </span>
  </h1>
<?php end_slot() ?>

<?php slot('content') ?>

  <noscript>
    <div class="messages warning">
      <?php echo __('Your browser does not support JavaScript. See %1%minimum requirements%2%.', array('%1%' => '<a href="https://www.accesstomemory.org/wiki/index.php?title=Minimum_requirements">', '%2%' => '</a>')) ?>
    </div>

    <section class="actions">
      <ul>
        <li><?php echo link_to(__('Cancel'), array($resource, 'module' => 'informationobject'), array('class' => 'c-btn')) ?></li>
      </ul>
    </section>
  </noscript>

  <?php if (QubitDigitalObject::reachedAppUploadLimit()): ?>

    <div id="upload_limit_reached">
      <div class="messages warning">
        <?php echo __('The maximum disk space of %1% GB available for uploading digital objects has been reached. Please contact your system administrator to increase the available disk space.',  array('%1%' => sfConfig::get('app_upload_limit'))) ?>
      </div>

      <section class="actions">
        <ul>
          <li><?php echo link_to(__('Cancel'), array($resource, 'module' => 'informationobject')) ?></li>
        </ul>
      </sectin>
    </div>

  <?php else: ?>

    <?php echo $form->renderGlobalErrors() ?>

    <?php echo $form->renderFormTag(url_for(array($resource, 'module' => 'informationobject', 'action' => 'multiFileUpload')), array('id' => 'multiFileUploadForm', 'style' => 'inline')) ?>

      <?php echo $form->renderHiddenFields() ?>

      <section id="content">

        <fieldset class="collapsible">

          <legend><?php echo __('Import multiple digital objects') ?></legend>

          <?php echo $form->title
            ->help(__('The "<strong>%dd%</strong>" placeholder will be replaced with a incremental number (e.g. \'image <strong>01</strong>\', \'image <strong>02</strong>\')'))
            ->label(__('Title'))
            ->renderRow() ?>

          <?php echo $form->levelOfDescription
            ->label(__('Level of description'))
            ->renderRow() ?>

          <div class="multiFileUploadSection">

            <h3><?php echo __('Digital objects') ?></h3>

            <div id="uploads"></div>

            <div id="uiElements" style="display: inline;">
              <div id="uploaderContainer">
                  <div class="uppy-dashboard"></div>
              </div>
            </div>
          </div>
        </fieldset>

      </section>

      <section class="actions">
        <ul>
          <li><?php echo link_to(__('Cancel'), array($resource, 'module' => 'informationobject'), array('class' => 'c-btn')) ?></li>
          <li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Import') ?>"/></li>
        </ul>
      </section>

    </form>

  <?php endif; ?>

<?php end_slot() ?>

<?php slot('after-content') ?>

<?php echo javascript_tag(<<<content
//Qubit.multiFileUpload.maxUploadSize = '$maxUploadSize';
//Qubit.multiFileUpload.uploadTmpDir = '$uploadTmpDir';
Qubit.multiFileUpload.uploadResponsePath = '$uploadResponsePath';
Qubit.multiFileUpload.objectId = '$resource->id';
Qubit.multiFileUpload.thumbWidth = 150;

Qubit.multiFileUpload.i18nClear = '{$sf_context->i18n->__('Clear')}';
Qubit.multiFileUpload.i18nInfoObjectTitle = '{$sf_context->i18n->__('Title')}';
//Qubit.multiFileUpload.i18nUploading = '{$sf_context->i18n->__('Uploading...')}';
//Qubit.multiFileUpload.i18nLoadingPreview = '{$sf_context->i18n->__('Loading preview...')}';
//Qubit.multiFileUpload.i18nWaiting = '{$sf_context->i18n->__('Waiting...')}';
Qubit.multiFileUpload.i18nUploadError = '{$sf_context->i18n->__('Some files failed to upload. Press the \\\'Import\\\' button to continue importing anyways, or press \\\'Retry\\\' to re-attempt upload.')}';
Qubit.multiFileUpload.i18nRetrySuccess = '{$sf_context->i18n->__('Files successfully uploaded! Press the \\\'Import\\\' button to complete the import.')}';
//Qubit.multiFileUpload.i18nFilename  = '{$sf_context->i18n->__('Filename')}';
//Qubit.multiFileUpload.i18nFilesize  = '{$sf_context->i18n->__('Filesize')}';
//Qubit.multiFileUpload.i18nDelete = '{$sf_context->i18n->__('Delete')}';
//Qubit.multiFileUpload.i18nCancel = '{$sf_context->i18n->__('Cancel')}';
//Qubit.multiFileUpload.i18nStart = '{$sf_context->i18n->__('Start')}';
//Qubit.multiFileUpload.i18nDuplicateFile = '{$sf_context->i18n->__('Warning: duplicate of %1%')}';
//Qubit.multiFileUpload.i18nOversizedFile = '{$sf_context->i18n->__('This file couldn\\\'t be uploaded because of file size upload limits')}';
//Qubit.multiFileUpload.uploadResponsePath = '$uploadResponsePath';

content
) ?>
<?php end_slot() ?>
