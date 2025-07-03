<?php

namespace Drupal\student_form\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;

/**
 * Controller for listing student data.
 */
class StudentListController extends ControllerBase {

  /**
   * Returns a list of students.
   */
  public function listStudents() {
    $connection = Database::getConnection();
    $query = $connection->select('student_data', 's')
      ->fields('s', ['student_id', 'student_name', 'student_contact', 'student_email'])
      ->execute();

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

    // Define the table headers.
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

