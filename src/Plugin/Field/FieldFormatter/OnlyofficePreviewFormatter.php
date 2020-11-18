<?php

namespace Drupal\onlyoffice_preview\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Config\ConfigException;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\onlyoffice_preview\Plugin\Field\FieldWidget\OnlyofficePreviewWidget;

/**
 * Plugin implementation of the 'onlyoffice_preview_iframe' formatter.
 *
 * @FieldFormatter(
 *   id = "onlyoffice_preview_iframe",
 *   label = @Translation("Iframe"),
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
      $placeholder_id = Html::getId(
        \sprintf(
          '%s-%s-placeholder',
          $item->getFieldDefinition()->get('field_name'),
          $delta
        )
      );

      $element[$delta] = ['#markup' => \sprintf('<div id="%s" class="onlyoffice-preview-placeholder"></div>', $placeholder_id)];

      $element['#attached']['drupalSettings']['onlyofficePreview']['documents'][] = [
        'placeholder' => $placeholder_id,
        'title' => $item->get('title')->getValue(),
        'url' => $url = $item->get('url')->getValue(),
        'key' => \hash('md5', $url),
        'type' => $type = $item->get('type')->getValue(),
        'width' => (bool)$this->getSetting('width'),
        'height' => (bool)$this->getSetting('height'),
        'comment' => (bool)$this->getSetting('comment'),
        'download' => (bool)$this->getSetting('download'),
        'edit' => (bool)$this->getSetting('edit'),
        'print' => (bool)$this->getSetting('print'),
        'review' => (bool)$this->getSetting('review'),
        'hide_right_menu' => (bool)$this->getSetting('hide_right_menu'),
        'chat' => (bool)$this->getSetting('chat'),
        'help' => (bool)$this->getSetting('help'),
        'plugins' => (bool)$this->getSetting('plugins'),
        'document_type' => $this->getDocumentType($type),
        'height' => $this->getSetting('height'),
        'width' => $this->getSetting('width'),
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

  private function getDocumentType(string $type) {
    foreach (OnlyofficePreviewWidget::getExtensionOptions(true) as $documentType => $types) {
      if (\in_array($type, $types)) {
        return \strtolower($documentType);
      }
    }

    return null;
  }
}
