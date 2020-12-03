<?php

namespace Drupal\onlyoffice_preview\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;

/**
 * Plugin implementation of the 'onlyoffice_preview_iframe' formatter.
 *
 * @FieldFormatter(
 *   id = "onlyoffice_preview_iframe",
 *   label = @Translation("Onlyoffice preview"),
 *   field_types = {
 *     "onlyoffice_preview"
 *   }
 * )
 */
class OnlyofficePreviewFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Displays a document in a iframe balise via an Onlyoffice server.');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    $config = \Drupal::config('onlyoffice_preview.settings');

    $error_message = $config->get('error_message') ?? t('Document preview failed.');

    $element = [
      '#attached' => [
        'library' => [
          'onlyoffice_preview/onlyoffice.api',
          'onlyoffice_preview/onlyoffice.preview'
        ],
        'drupalSettings' => [
          'onlyofficePreview' => [
            'error_message' => $error_message,
          ],
        ],
      ]
    ];

    foreach ($items as $delta => $item) {
      $placeholder_id = Html::getId(\sprintf(
        '%s-%s-placeholder',
        $item->getFieldDefinition()->get('field_name'),
        $delta
      ));

      $element[$delta] = ['#markup' => \sprintf('<div id="%s" class="onlyoffice-preview-placeholder"></div>', $placeholder_id)];

      $element['#attached']['drupalSettings']['onlyofficePreview']['documents'][] = [
        'placeholder' => $placeholder_id,
        'config' => $this->buildConfig($item),
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'width' => '800px',
      'height' => '600px',
      'comment' => true,
      'download' => true,
      'edit' => true,
      'print' => true,
      'review' => true,
      'hide_right_menu' => true,
      'chat' => true,
      'help' => true,
      'plugins' => true,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    return [
      'width' => [
        '#title' => $this->t('Iframe width'),
        '#description' => $this->t('example: 600px, 50%'),
        '#type' => 'textfield',
        '#min' => 0,
        '#default_value' => $this->getSetting('width'),
      ],
      'height' => [
        '#title' => $this->t('Iframe height'),
        '#description' => $this->t('example: 600px, 50%'),
        '#type' => 'textfield',
        '#min' => 0,
        '#default_value' => $this->getSetting('height'),
      ],
      'comment' => [
        '#title' => $this->t('Allow comments'),
        '#description' => $this->t('Defines if the document can be commented or not. In case the commenting permission is set to "true" the document side bar will contain the Comment menu option; the document commenting will only be available for the document editor if the mode parameter is set to edit.'),
        '#type' => 'checkbox',
        '#default_value' => $this->getSetting('comment'),
      ],
      'download' => [
        '#title' => $this->t('Allow download'),
        '#description' => $this->t('Defines if the document can be downloaded or only viewed or edited online. In case the downloading permission is set to "false" the Download as... menu option will be absent from the File menu.'),
        '#type' => 'checkbox',
        '#default_value' => $this->getSetting('download'),
      ],
      'edit' => [
        '#title' => $this->t('Allow edit'),
        '#description' => $this->t('Defines if the document can be edited or only viewed. In case the editing permission is set to "true" the File menu will contain the Edit Document menu option.'),
        '#type' => 'checkbox',
        '#default_value' => $this->getSetting('edit'),
      ],
      'print' => [
        '#title' => $this->t('Allow print'),
        '#description' => $this->t('Defines if the document can be printed or not. In case the printing permission is set to "false" the Print menu option will be absent from the File menu.'),
        '#type' => 'checkbox',
        '#default_value' => $this->getSetting('print'),
      ],
      'review' => [
        '#title' => $this->t('Allow review'),
        '#description' => $this->t('Defines if the document can be reviewed or not. In case the reviewing permission is set to "true" the document status bar will contain the Review menu option; the document review will only be available for the document editor if the mode parameter is set to edit.'),
        '#type' => 'checkbox',
        '#default_value' => $this->getSetting('review'),
      ],
      'hide_right_menu' => [
        '#title' => $this->t('Hide right menu'),
        '#description' => $this->t('Defines if the right menu is displayed or hidden on first loading.'),
        '#type' => 'checkbox',
        '#default_value' => $this->getSetting('hide_right_menu'),
      ],
      'chat' => [
        '#title' => $this->t('Show chat'),
        '#description' => $this->t('Defines if the Chat menu button is displayed or hidden; please note that in case you hide the Chat button, the corresponding chat functionality will also be disabled.'),
        '#type' => 'checkbox',
        '#default_value' => $this->getSetting('chat'),
      ],
      'help' => [
        '#title' => $this->t('Show help'),
        '#description' => $this->t('Defines if the Help menu button is displayed or hidden.'),
        '#type' => 'checkbox',
        '#default_value' => $this->getSetting('help'),
      ],
      'plugins' => [
        '#title' => $this->t('Allow plugins'),
        '#description' => $this->t('Defines if plugins will be launched and available.'),
        '#type' => 'checkbox',
        '#default_value' => $this->getSetting('plugins'),
      ],
    ];
  }

  /**
   * For further explanation on this config array, see: https://api.onlyoffice.com/editors/advanced
   */
  private function buildConfig(FieldItemInterface $item): array {

    $media = Media::load($item->get('target_id')->getValue());
    $fid = $media->getSource()->getSourceFieldValue($media);

    $file = File::load($fid);

    return [
      "document" => [
        "title" => $item->get('title')->getValue(),
        "url" => $url = $file->url(),
        "fileType" => $type = $this->getDocumentExtension($file),
        // For the key argument, onlyoffice documentation says :
        // (see: https://api.onlyoffice.com/editors/config/document#key)
        // "Defines the unique document identifier used for document recognition by the service.
        // In case the known key is sent the document will be taken from the cache. Every time
        // the document is edited and saved, the key must be generated anew. The document url can
        // be used as the key but without the special characters and the length is limited to
        // 128 symbols."
        //
        // In order to get the benefit of the onlyoffice server cache, we define a hash based on
        // document url. It means that if we allow edition and the same document is shown
        // in another entity, cached document will appear with hypothetical edition from another
        // person on another entity.
        // I'm not sure this is what we want. An other solution could be to generate an unique id based
        // on parent entity uuid, field name and delta, but for now, we will let this like it is.
        "key" => \hash('md5', $url),
        "permissions" => [
          "comment" => (bool)$this->getSetting('comment'),
          "download" => (bool)$this->getSetting('download'),
          "edit" => (bool)$this->getSetting('edit'),
          "print" => (bool)$this->getSetting('print'),
          "review" => (bool)$this->getSetting('review'),
        ],
      ],
      "documentType" => $this->getDocumentType($type),
      "editorConfig" => [
        "callbackUrl" => \urlencode(\sprintf("//%s/url-to-callback.ashx", \base_path())),
        "customization" => [
          "comments" => (bool)$this->getSetting('comment'),
          "hideRightMenu" => (bool)$this->getSetting('hide_right_menu'),
          "chat" => (bool)$this->getSetting('chat'),
          "help" => (bool)$this->getSetting('help'),
          "plugins" => (bool)$this->getSetting('plugins'),
        ],
      ],
      "height" => $this->getSetting('height'),
      "width" => $this->getSetting('width'),
    ];
  }

  private function getDocumentType(string $extension) {
    foreach ($this->getExtensionOptions() as $documentType => $types) {
      if (\in_array($extension, $types)) {
        return $documentType;
      }
    }

    return null;
  }

  private function getDocumentExtension(File $file): ?string {

    $map = [
      'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
      'application/msword' => 'doc',
      'application/vnd.oasis.opendocument.text' => 'odt',
      'application/pdf' => 'pdf',
      'application/vnd.ms-word.document.macroenabled.12' => 'docm',
      'application/vnd.ms-word.template.macroenabled.12' => 'dotm',
      'application/vnd.openxmlformats-officedocument.wordprocessingml.template' => 'dotx',
      'application/epub+zip' => 'epub',
      'text/html' => 'html',
      'application/vnd.oasis.opendocument.text-template' => 'ott',
      'application/rtf' => 'rtf',
      'text/plain' => 'txt',
      'image/vnd.djvu' => 'djvu',
      'application/vnd.ms-xpsdocument' => 'xps',
      'application/vnd.ms-excel' => 'xls',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
      'application/vnd.oasis.opendocument.spreadsheet' => 'ods',
      'text/csv' => 'csv',
      'application/vnd.oasis.opendocument.spreadsheet-template' => 'ots',
      'application/vnd.ms-excel.sheet.macroenabled.12' => 'xlsm',
      'application/vnd.ms-excel' => 'xlt',
      'application/vnd.ms-excel.template.macroenabled.12' => 'xltm',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.template' => 'xltx',
      'application/vnd.ms-powerpoint' => 'ppt',
      'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
      'application/vnd.oasis.opendocument.presentation' => 'odp',
      'application/vnd.oasis.opendocument.presentation-template' => 'otp',
      'application/vnd.ms-powerpoint.template.macroenabled.12' => 'potm',
      'application/vnd.openxmlformats-officedocument.presentationml.template' => 'potx',
      'application/vnd.ms-powerpoint.slideshow.macroenabled.12' => 'ppsm',
      'application/vnd.openxmlformats-officedocument.presentationml.slideshow' => 'ppsx',
      'application/vnd.ms-powerpoint.presentation.macroenabled.12' => 'pptm',
    ];

    return $map[$file->getMimeType()] ?? '';
  }

  public static function getExtensionOptions() {
    return [
      'text' => ['docx', 'doc', 'odt', 'pdf', 'docm', 'dot', 'dotm', 'dotx', 'epub', 'fodt', 'htm', 'html', 'mht', 'ott', 'rtf', 'txt', 'djvu', 'xps'],
      'spreadsheet' => ['xls', 'xlsx', 'ods', 'csv', 'fods', 'ots', 'xlsm', 'xlt', 'xltm', 'xltx'],
      'presentation' => ['ppt', 'pptx', 'odp', 'fodp', 'otp', 'pot', 'potm', 'potx', 'pps', 'ppsm', 'ppsx', 'pptm'],
    ];
  }
}
