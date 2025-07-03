<?php

namespace Drupal\bank_account\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\student_form\Service\ContactValidator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Bank Account Form.
 */
class BankAccountForm extends FormBase {

  /**
   * The contact validator service.
   *
   * @var \Drupal\student_form\Service\ContactValidator
   */
  protected $contactValidator;

  /**
   * Constructs a BankAccountForm object.
   *
   * @param \Drupal\student_form\Service\ContactValidator $contactValidator
   *   The contact validator service.
   */
  public function __construct(ContactValidator $contactValidator) {
    $this->contactValidator = $contactValidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('student_form.contact_validator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bank_account_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['account_holder_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Account Holder Name'),
      '#required' => TRUE,
    ];

    $form['ifsc_code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('IFSC Code'),
      '#required' => TRUE,
    ];

    $form['branch_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Branch Name'),
      '#required' => TRUE,
    ];

    $form['contact_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Contact Number'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    // Validate contact number using the service
    if (!$this->contactValidator->isValid($values['contact_number'])) {
      \Drupal::messenger()->addError($this->t('Invalid contact number. It must be 10 digits and start with 9, 8, 7, or 6.'));
      return;
    }

    // Insert data into the database.
    $connection = Database::getConnection();
    $connection->insert('bank_account_data')
      ->fields([
        'account_holder_name' => $values['account_holder_name'],
        'ifsc_code' => $values['ifsc_code'],
        'branch_name' => $values['branch_name'],
        'contact_number' => $values['contact_number'],
      ])
      ->execute();

    \Drupal::messenger()->addMessage($this->t('Bank account details saved successfully!'));

    // Redirect to the bank account list page.
    $form_state->setRedirect('bank_account.list');
  }

}
