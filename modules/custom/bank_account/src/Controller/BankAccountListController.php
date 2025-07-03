<?php

namespace Drupal\bank_account\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;

/**
 * Controller for displaying the list of bank accounts.
 */
class BankAccountListController extends ControllerBase {

  /**
   * Displays a list of bank accounts.
   */
  public function listAccounts() {
    $connection = Database::getConnection();
    $query = $connection->select('bank_account_data', 'b')
      ->fields('b', ['id', 'account_holder_name', 'ifsc_code', 'branch_name', 'contact_number'])
      ->execute();
    
    $rows = [];
    foreach ($query as $record) {
      $rows[] = [
        'data' => [
          $record->id,
          $record->account_holder_name,
          $record->ifsc_code,
          $record->branch_name,
          $record->contact_number,
        ],
      ];
    }

    $header = [
      $this->t('ID'),
      $this->t('Account Holder Name'),
      $this->t('IFSC Code'),
      $this->t('Branch Name'),
      $this->t('Contact Number'),
    ];

    $table = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No bank accounts found.'),
    ];

    return $table;
  }

}
