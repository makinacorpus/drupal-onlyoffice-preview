<?php

namespace Drupal\onlyoffice_preview\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Provides a field type of Onlyoffice document.
 *
 * @FieldType(
 *   id = "onlyoffice_preview",
 *   label = @Translation("Onlyoffice preview"),
 *   default_formatter = "onlyoffice_preview_iframe",
 *   default_widget = "onlyoffice_preview_widget",
 * )
 */
class OnlyofficePreviewItem extends EntityReferenceItem {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);

    $schema['columns']['title'] = [
      'type' => 'varchar',
      'length' => 255,
      'not null' => true,
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {

    $properties = parent::propertyDefinitions($field_definition);

    $properties['title'] = DataDefinition::create('string')->setLabel(t('Title'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'target_type' => 'media',
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {

    $element = parent::storageSettingsForm($form, $form_state, $has_data);

    $element['target_type']['#disabled'] = true;

    return $element;
  }
}
