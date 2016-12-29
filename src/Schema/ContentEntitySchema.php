<?php

namespace Drupal\jsonapi_model\Schema;

use Drupal\node\Entity\NodeType;
use Drupal\Core\Entity\TypedData\EntityDataDefinitionInterface;

/**
 * Specialized schema for Node Entities.
 *
 * Leverages NodeType configuration for additional metadata.
 */
class ContentEntitySchema extends Schema {

  /**
   * NodeType associated with the current bundle.
   *
   * @var \Drupal\node\Entity\NodeType
   */
  protected $nodeType;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityDataDefinitionInterface $entity_type, $bundle = NULL, $described_media_type, $properties = []) {
    $this->nodeType = NodeType::load($bundle);
    parent::__construct($entity_type, $bundle, $described_media_type, $properties);
  }

  /**
   * {@inheritdoc}
   */
  protected function createDescription($entityType, $bundle = '') {
    $description = $this->nodeType->getDescription();
    if (empty($description)) {
      return parent::createDescription($entityType, $bundle);
    }

    return $description;
  }

}
