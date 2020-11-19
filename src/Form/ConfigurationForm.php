<?php

namespace Drupal\onlyoffice_preview\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'onlyoffice_preview_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['onlyoffice_preview.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
      $config = $this->config('onlyoffice_preview.settings');

      $form['api_url'] = [
        '#type' => 'url',
        '#title' => t('Onlyoffice server API URL'),
        '#description' => t(<<<EOF
        The API JavaScript file can normally be found in the following editors folder: https://documentserver/web-apps/apps/api/documents/api.js<br>
        <strong>Note that if you update this value, you will probably need to empty Drupal cache to see your change effective.</strong>
        EOF),
        '#default_value' => $config->get('api_url'),
        '#required' => true,
      ];
      $form['error_message'] = [
        '#type' => 'textfield',
        '#title' => t('Preview failed message'),
        '#default_value' => $config->get('error_message') ?? t('Document preview failed.'),
        '#required' => true,
      ];

      return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this
      ->config('onlyoffice_preview.settings')
      ->set('api_url', $form_state->getValue('api_url'))
      ->set('error_message', $form_state->getValue('error_message'))
      ->save()
    ;

    parent::submitForm($form, $form_state);
  }
}
