<?php

namespace Drupal\student_form\Service;

/**
 * Provides a service to validate student contact numbers.
 */
class ContactValidator {

  /**
   * Validates the student contact number.
   *
   * @param string $contact_number
   *   The contact number to validate.
   *
   * @return bool
   *   Returns TRUE if valid, FALSE otherwise.
   */
  public function isValid($contact_number) {
    // Ensure the number is exactly 10 digits and starts with 9, 8, 7, or 6.
    if (preg_match('/^[9876]\d{9}$/', $contact_number)) {
      return TRUE;
    }
    return FALSE;
  }
}
