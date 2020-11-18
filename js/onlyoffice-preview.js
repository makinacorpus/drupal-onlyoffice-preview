(function ($, Drupal) {
  $(function () {
    if (drupalSettings.onlyofficePreview) {
      if (drupalSettings.onlyofficePreview.documents) {
        for (documentConfig of drupalSettings.onlyofficePreview.documents) {
          if (typeof DocsAPI !== 'undefined') {
            new DocsAPI.DocEditor(
              documentConfig.placeholder,
              {
                "document": {
                  "key": documentConfig.key,
                  "title": documentConfig.title,
                  "url": documentConfig.url,
                  "fileType": documentConfig.type,
                  "permissions": {
                    "comment": documentConfig.comment,
                    "download": documentConfig.download,
                    "edit": documentConfig.edit,
                    "print": documentConfig.print,
                    "review": documentConfig.review
                  }
                },
                "documentType": documentConfig.document_type,
                "editorConfig": {
                  "callbackUrl":"\/\/" + window.location.origin + "\/url-to-callback.ashx",
                  "customization": {
                    "comments": documentConfig.comment,
                    "hideRightMenu": documentConfig.hide_right_menu,
                    "chat": documentConfig.chat,
                    "help": documentConfig.help,
                    "plugins": documentConfig.plugins
                  },
                },
                "height": documentConfig.height,
                "width": documentConfig.width,
              }
            );
          } else {
            console.log("Onlyoffice API can't be found");
            var errorMessage = document.createElement("p");
            errorMessage.innerHTML = drupalSettings.onlyofficePreview.error_message;
            errorMessage.classList.add("onlyoffice-preview-error");
            document.getElementById(documentConfig.placeholder).appendChild(errorMessage);
          }
        }
      }
    }
  });
})(jQuery, Drupal);