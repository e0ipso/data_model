<?php

namespace Drupal\data_model\Normalizer\jsonapi;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityTypeManager;

/**
 * Normalizer for Entity References.
 *
 * DataReferenceDefinitions are embedded inside ComplexDataDefinitions, and
 * represent a type property. The key for this is usually "entity", and it is
 * found alongside a "target_id" value which refers to the specific entity
 * instance for the reference. The target_id is not normalized by this class,
 * instead it comes through the DataDefinitionNormalizer as a scalar value.
 */
class DataReferenceDefinitionNormalizer extends DataDefinitionNormalizer {

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var string
   */
  protected $supportedInterfaceOrClass = '\Drupal\Core\TypedData\DataReferenceDefinitionInterface';

  /**
   * EntityTypeManager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Constructs an DataReferenceDefinitionNormalizer object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_type_manager
   *   The Entity Type Manager.
   */
  public function __construct(EntityTypeManager $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize($entity, $format = NULL, array $context = []) {
    // A relationship has very similar schema every time.
    $resource_identifier_object = [
      'type' => 'object',
      'required' => ['type', 'id'],
      'properties' => [
        'type' => ['type' => 'string', 'title' => t('Referenced resource')],
        'id' => ['type' => 'string', 'title' => t('Resource ID'), 'format' => 'uuid', 'maxLength' => 128],
      ],
    ];
    // Handle the multivalue variant.
    if ($context['cardinality'] == 1) {
      $data = $resource_identifier_object;
    }
    else {
      $data = [
        'type' => 'array',
        'items' => $resource_identifier_object,
      ];
    }
    /* @var $entity \Drupal\Core\TypedData\DataReferenceDefinitionInterface */
    if ($target_entity_type = $context['parent']->getSetting('target_type')) {
      $handler_settings = $context['parent']->getSetting('handler_settings');
      $target_bundles = empty($handler_settings['target_bundles']) ?
        [$target_entity_type] :
        array_values($handler_settings['target_bundles']);
      $data['properties']['type']['enum'] = array_map(function ($bundle) use ($target_entity_type) {
        return sprintf('%s--%s', $target_entity_type, $bundle);
      }, $target_bundles);
    }
    $properties = $this->extractPropertyData($entity, $context);
    // Remove the unneeded data.
    unset($properties['type']);

    $normalized = [
      'type' => 'object',
      'properties' => [
        'data' => $data,
      ],
    ];

    return NestedArray::mergeDeepArray($normalized, $properties);
  }

}
