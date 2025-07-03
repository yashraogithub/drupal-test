<?php

namespace Drupal\node_logger\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\node\NodeEvents;
use Drupal\node\Event\NodeInsertEvent;
use Psr\Log\LoggerInterface;

class NodeCreateSubscriber implements EventSubscriberInterface {

  protected $logger;

  public function __construct(LoggerInterface $logger) {
    $this->logger = $logger;
  }

  public static function getSubscribedEvents(): array {
    return [
      NodeEvents::INSERT => 'onNodeInsert',
    ];
  }

  public function onNodeInsert(NodeInsertEvent $event): void {
    $node = $event->getNode();
    $title = $node->label();
    $type = $node->bundle(); // e.g., article, page, etc.

    $this->logger->info('A new @type titled "@title" was created.', [
      '@type' => $type,
      '@title' => $title,
    ]);
  }

}
