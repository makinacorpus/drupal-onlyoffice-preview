<?php

namespace Drupal\onlyoffice_preview\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * A widget bar.
 *
 * @FieldWidget(
 *   id = "onlyoffice_preview_widget",
 *   label = @Translation("Onlyoffice Document widget"),
 *   field_types = {
 *     "onlyoffice_preview"
 *   }
 * )
 */
class OnlyofficePreviewWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['#type'] = 'fieldset';

    $element['title'] =[
      '#type' => 'textfield',
      '#title' => t('Title'),
      '#default_value' => isset($items[$delta]->title) ? $items[$delta]->title : '',
    ];

    $element['url'] = [
      '#type' => 'url',
      '#title' => t('URL'),
      '#default_value' => isset($items[$delta]->url) ? $items[$delta]->url : '',
    ];

    $element['type'] = [
      '#type' => 'select',
      '#title' => t('Type'),
      '#options' => self::getExtensionOptions(),
      '#default_value' => isset($items[$delta]->type) ? $items[$delta]->type : '',
    ];

    return $element;
  }

  public static function getExtensionOptions($rawLabel = false) {

    return [
      'Text' => [
        'docx' => 'docx',
        'doc' => 'doc',
        'odt' => 'odt',
        'pdf' => 'pdf',
        'docm' => 'docm',
        'dot' => 'dot',
        'dotm' => 'dotm',
        'dotx' => 'dotx',
        'epub' => 'epub',
        'fodt' => 'fodt',
        'htm' => 'htm',
        'html' => 'html',
        'mht' => 'mht',
        'ott' => 'ott',
        'rtf' => 'rtf',
        'txt' => 'txt',
        'djvu' => 'djvu',
        'xps' => 'xps'
      ],
      'Spreadsheet' => [
        'xls' => 'xls',
        'xlsx' => 'xlsx',
        'ods' => 'ods',
        'csv' => 'csv',
        'fods' => 'fods',
        'ots' => 'ots',
        'xlsm' => 'xlsm',
        'xlt' => 'xlt',
        'xltm' => 'xltm',
        'xltx' => 'xltx'
      ],
      'Presentation' => [
        'ppt' => 'ppt',
        'pptx' => 'pptx',
        'odp' => 'odp',
        'fodp' => 'fodp',
        'otp' => 'otp',
        'pot' => 'pot',
        'potm' => 'potm',
        'potx' => 'potx',
        'pps' => 'pps',
        'ppsm' => 'ppsm',
        'ppsx' => 'ppsx',
        'pptm' => 'pptm',
      ],
    ];
  }
}