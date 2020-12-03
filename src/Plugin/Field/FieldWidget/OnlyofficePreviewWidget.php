<?php

namespace Drupal\onlyoffice_preview\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\media\Entity\Media;

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

    $handlerSettings = $this->fieldDefinition->getSettings()['handler_settings'];

    $element['target_id'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'media',
      '#selection_handler' => 'default:media',
      '#selection_settings' => [
        'target_bundles' => $handlerSettings['target_bundles'] ?? null,
      ],
      '#title' => t('Media'),
      '#default_value' => isset($items[$delta]->target_id) ? Media::load($items[$delta]->target_id) : null,
    ];

    return $element;
  }
}