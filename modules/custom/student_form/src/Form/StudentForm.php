<?php

namespace Drupal\student_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\student_form\Service\StudentService;
use Drupal\student_form\Service\ContactValidator;
use Drupal\Core\Database\Database;

/**
 * Defines the Student Form.
 */
class StudentForm extends FormBase {

  /**
   * The student service.
   *
   * @var \Drupal\student_form\Service\StudentService
   */
  protected $studentService;

  /**
   * The contact validator service.
   *
   * @var \Drupal\student_form\Service\ContactValidator
   */
  protected $contactValidator;

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new StudentForm object.
   */
  public function __construct(StudentService $studentService, ContactValidator $contactValidator, ConfigFactoryInterface $configFactory) {
    $this->studentService = $studentService;
    $this->contactValidator = $contactValidator;
    $this->configFactory = $configFactory;
  }

  /**
   * Dependency Injection for Services.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('student_form.student_service'),
      $container->get('student_form.contact_validator'),
      $container->get('config.factory')
    );
  }

  /**
   * Returns a unique string identifying the form.
   */
  public function getFormId() {
    return 'student_form';
  }

  /**
   * Form constructor.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Load a configuration value from 'student_form.settings'.
    $config = $this->configFactory->get('student_form.settings');
    $default_email = $config->get('default_student_email') ?? '';

    $students = $this->studentService->getStudentNames();

    $form['student_select'] = [
      '#type' => 'select',
      '#title' => $this->t('Select a Student'),
      '#options' => $students,
      '#empty_option' => $this->t('- Select -'),
      '#ajax' => [
        'callback' => '::updateStudentName',
        'wrapper' => 'student-name-output',
      ],
    ];

    $form['student_name_output'] = [
      '#type' => 'markup',
      '#markup' => '<div id="student-name-output"></div>',
    ];

    $form['student_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Student ID'),
      '#required' => TRUE,
    ];

    $form['student_contact'] = [
      '#type' => 'tel',
      '#title' => $this->t('Student Contact Number'),
      '#required' => TRUE,
      '#attributes' => ['pattern' => '[0-9]{10}'],
    ];

    $form['student_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Student Email'),
      '#required' => TRUE,
      '#default_value' => $default_email, // Default email from config
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * Form validation handler.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $contact_number = $form_state->getValue('student_contact');
    if (!$this->contactValidator->isValid($contact_number)) {
      $form_state->setErrorByName('student_contact', $this->t('Invalid contact number! It must start with 9, 8, 7, or 6 and be 10 digits.'));
    }
  }

  /**
   * Form submission handler.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    // Insert data into the database.
    $connection = Database::getConnection();
    $connection->insert('student_data')
      ->fields([
        'student_id' => $values['student_id'],
        'student_contact' => $values['student_contact'],
        'student_email' => $values['student_email'],
      ])
      ->execute();

    // Save submitted email to config.
    $this->configFactory->getEditable('student_form.settings')
      ->set('default_student_email', $values['student_email'])
      ->save();

    \Drupal::messenger()->addMessage($this->t('Student data added successfully!'));
    $form_state->setRedirect('student_form.student_list'); // Replace with the correct route name
  }
}
