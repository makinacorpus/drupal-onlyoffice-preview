<?php

namespace Drupal\onlyoffice_preview\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
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
class OnlyofficePreviewItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'title' => [
          'type' => 'varchar',
          'length' => 255,
          'not null' => true,
        ],
        'url' => [
          'type' => 'varchar',
          'length' => 255,
          'not null' => true,
        ],
        'type' => [
          'type' => 'varchar',
          'length' => 50,
          'not null' => true,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    return [
      'title' => DataDefinition::create('string')->setLabel(t('Title')),
      'url' => DataDefinition::create('string')->setLabel(t('Document URL')),
      'type' => DataDefinition::create('string')->setLabel(t('Document type')),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName() {
    return 'url';
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('url')->getValue();
    return $value === null || $value === '';
  }
}
