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

            var documentLink = document.createElement("a");
            documentLink.appendChild(document.createTextNode(documentConfig.config.document.title));
            documentLink.href = documentConfig.config.document.url;
            errorMessage.classList.add("onlyoffice-preview-link");
            document.getElementById(documentConfig.placeholder).appendChild(documentLink);

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