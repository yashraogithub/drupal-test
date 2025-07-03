<?php

namespace Drupal\student_form\Service;

use Drupal\Core\Database\Connection;

/**
 * Service to fetch student names from the database.
 */
class StudentService {

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs the StudentService object.
   */
  public function __construct(Connection $database, ) {
    $this->database = $database;
  }

  /**
   * Fetches student names from the student_data table.
   *
   * @return array
   *   An associative array of student names indexed by ID.
   */
  public function getStudentNames() {
    $query = $this->database->select('student_data', 's')
      ->fields('s', ['student_id', 'student_name'])
      ->execute();

    $students = [];
    foreach ($query as $record) {
      $students[$record->student_id] = $record->student_name;
    }

    return $students;
  }
}
