<?php

namespace Drupal\data_fatech\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller to fetch and display student data.
 */
class StudentListController extends ControllerBase {

  protected $database;

  /**
   * Constructor to inject database service.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * Dependency Injection for database service.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Fetch and display student data.
   */
  public function listStudents() {
    // Fetch data from student_data table
    $query = $this->database->select('student_data', 's')
      ->fields('s', ['student_id', 'student_name', 'student_contact', 'student_email'])
      ->execute();

    // Process the results
    $rows = [];
    foreach ($query as $record) {
      $rows[] = [
        'data' => [
          $record->student_id,
          $record->student_name,
          $record->student_contact,
          $record->student_email,
        ],
      ];
    }

    // Define table headers
    $header = [
      $this->t('Student ID'),
      $this->t('Name'),
      $this->t('Contact Number'),
      $this->t('Email'),
    ];

    return [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No student data found.'),
    ];
  }
}
