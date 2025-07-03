<?php

namespace Drupal\example_form\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ExampleConfigForm extends ConfigFormBase {

    protected function getEditableConfigNames(){
        return['example_form.settings'];
    }

    public function getFormId(){
        return 'example_form_settings';
    }

    public function buildForm(array $form, FormStateInterface $form_state){
        $config = $this->config('example_form.settings');

        $form['contact_email'] = [
            '#type' => 'email',
            '#title' => $this->t('Contact Email'),
            '#default_value' => $config->get('contact_email'),
        ];

        return parent::buildForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state){
        $config = $this->config('example_form.settings');
        $config->set('contact_email', $form_state->getValue('contact_email'));
        $config->save();

        parent::submitForm($form, $form_state); 

    }
}