(function ($, Drupal) {
  $(function () {
    if (drupalSettings.onlyofficePreview) {
      if (drupalSettings.onlyofficePreview.documents) {
        for (documentConfig of drupalSettings.onlyofficePreview.documents) {
          if (typeof DocsAPI !== 'undefined') {
            new DocsAPI.DocEditor(documentConfig.placeholder, documentConfig.config);
          } else {
            // Onlyoffice API is missing, the module
            console.log("Onlyoffice API can't be found, OnlyOffice Preview Field Module is not configured properly or Onlyoffice API can't be reached.");

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