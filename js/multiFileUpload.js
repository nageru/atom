(function ($) {

  "use strict";

  Qubit.multiFileUpload = Qubit.multiFileUpload || {};

  var MultiFileUpload = function (element)
  {
    this.uppy = new Uppy.Core({ debug: true, autoProceed: false })
    this.nextImageNum = 1;
    this.uploadItems = [];
    this.result = "";

    this.$element = $(element);
    this.$submitButton = this.$element.find('input[type="submit"]');
    this.$retryButton = $('<a class="c-btn" title="retry"/>')
      .attr('type','hidden')
      .text('Retry')
      .appendTo('.actions');

    this.init();
    this.listen();
  };

  MultiFileUpload.prototype = {
    constructor: MultiFileUpload,

    init: function()
    {
      this.$retryButton.hide();
      this.uppy
        .use(Uppy.Dashboard, {
          id: 'Dashboard',
          inline: true,
          target: '.uppy-dashboard',
          hideUploadButton: true,
          replaceTargetContent: true,
          showProgressDetails: true,
          hideCancelButton: true,
          hideAfterFinish: true,
          hideRetryButton: true,
          note: 'Images and video only, 2–3 files, up to 1 MB',
          doneButtonHandler: null,
          browserBackButtonClose: false,
          fileManagerSelectionType: 'files',
          proudlyDisplayPoweredByUppy: false,
          closeModalOnClickOutside: false,
          locale: {
            strings: {
              done: Qubit.multiFileUpload.i18nClear,
            }
          },
          thumbnailWidth: Qubit.multiFileUpload.thumbWidth,
          trigger: '#pick-files',
          // Enable editing of field with id 'title' label: 'Title'
          metaFields: [
            { id: 'title', name: 'Title' },
          ],
        })
        .use(Uppy.XHRUpload, {
          endpoint: Qubit.multiFileUpload.uploadResponsePath,
          formData: true,
          method: 'post',
          limit: 10,
          fieldName: 'Filedata',
          objectId: Qubit.multiFileUpload.objectId,
        })
        .on('upload-success', $.proxy(this.onUploadSuccess, this))
        .on('complete', $.proxy(this.onComplete, this))
        .on('file-added', $.proxy(this.onFileAdded, this))
        .on('cancel-all', $.proxy(this.onCancelAll, this))
    },

    listen: function ()
    {
      // Intercept AtoM's Submit button.
      this.$submitButton.on('click', $.proxy(this.onSubmitButton, this));
      this.$retryButton.on('click', $.proxy(this.onRetryButton, this));
    },

    onRetryButton: function ()
    {
      this.uppy.retryAll().then((result) => {
        if (this.uppy.getState().error === null && this.result.failed.length === 0) {
          this.$retryButton.hide();
          this.clearAlerts('alert-error');
          this.showAlert(Qubit.multiFileUpload.i18nRetrySuccess, 'alert-info');
        }
      })
    },

    onSubmitButton: function ()
    {
      // Test if any uploads failed.
      if (this.uppy.getState().error && this.result.successful.length > 0) {
        // Post any successful uploads.
        $('#multiFileUploadForm').submit();
      }
      else {
        // Trigger upload - wait on promise until all complete.
        this.uppy.upload().then((result) => {
          if (this.result.failed.length > 0) {
            this.showAlert(Qubit.multiFileUpload.i18nUploadError, 'alert-error');

            this.$retryButton.show();
          }
          else {
            // Post to multiFileUpload.
            $('#multiFileUploadForm').submit();
          }
        })
      }

      return false;
    },

    // Push a record of successful file upload into array uploadItems. 
    // These will be added to this array in order of when they completed uploading.
    // This info is needed to build the hidden form elements once all files 
    // have completed uploading to AtoM.
    onUploadSuccess: function (file, response)
    {
      this.uploadItems.push({file, response});
    },

    // onComplete runs when all uploads are complete - even if there were errors.
    // Adds the form elements in the same order as result.successful so that
    // they are imported into AtoM: Image 01, Image 02, etc.
    onComplete: function (result)
    {
      // Iterates over successfully uploaded items.
      var uploadItems = this.uploadItems;
      // Assign result when all uploads are complete from this upload attempt.
      this.result = result;

      $.each(result.successful, function(key, file) {
        // Get the corresponding upload response.
        var fileResponse = uploadItems.find(x => x.file.id === file.id).response;

        // Add hidden form elements for each successfully uploaded file.
        $('<div class="multiFileUploadItem" id=' + file.id + '>' +
          '<div class="multiFileUploadInfo">' +
            '<div class="form-item">' +
              '<input type="hidden" class="filename" value="' + fileResponse.body.name + '"/>' +
              '<input type="hidden" class="md5sum" value="' + fileResponse.body.md5sum + '"/>' +
              '<input type="hidden" name="files[' + file.id + '][name]" value="' + fileResponse.body.name + '"/>' +
              '<input type="hidden" name="files[' + file.id + '][md5sum]" value="' + fileResponse.body.md5sum + '"/>' +
              '<input type="hidden" name="files[' + file.id + '][tmpName]" value="' + fileResponse.body.tmpName + '"/>' +
              '<input type="hidden" class="title" name="files[' + file.id + '][infoObjectTitle]" value="' + file.meta.title + '"/>' +
            '</div>' +
          '</div>' +
        '</div>')
        .appendTo("#uploads");
      });
    },

    // Set objectId and template-based title when files are added to the Dashboard.
    onFileAdded: function (file) 
    {
      this.uppy.setFileMeta(file.id, {
        objectId: Qubit.multiFileUpload.objectId,
        title: this.replacePlaceHolder($('input#title').val(), this.nextImageNum++)
      });
    },

    reset: function () 
    {
      this.uploadItems = [];
      this.nextImageNum = 1;
      this.result = "";
    },

    // User pressed cancel - reset upload state.
    onCancelAll: function ()
    {
      // Delete all file upload hidden form items.
      uploads = document.getElementById("uploads");
        while (uploads.firstChild) {
          uploads.removeChild(uploads.lastChild);
        }

      // Reset internal vars.
      this.reset();
    },

    showAlert: function (message, type) {
      if (!type) {
        type = '';
      }
  
      var $alert = $('<div class="alert ' + type + '">');
      $alert.append('<button type="button" data-dismiss="alert" class="close">&times;</button>');
      $alert.append(message).prependTo($('#uploaderContainer'));
  
      return $alert;
    },

    clearAlerts: function (type) {
      $( "div" ).remove( ".alert" );
    },

    // Build title from Title field template.
    replacePlaceHolder: function (templateStr, index) 
    {
      var fileName = null;
      index = String(index);
      var matches = templateStr.match(/\%(d+)\%/);

      if (null != matches && 0 < matches[1].length) {
        while (matches[1].length > index.length) {
          index = '0' + index;
        }

        var fileName = templateStr.replace('%' + matches[1] + '%', index);
      }

      if (null == fileName || templateStr == fileName) {
        fileName = templateStr + ' ' + index;
      }

      return fileName;
    }
  };

  $(function ()
  {
    var $node = $('body');

    if (0 < $node.length)
    {
      new MultiFileUpload($node.get(0));
    }
  });

})(window.jQuery);
