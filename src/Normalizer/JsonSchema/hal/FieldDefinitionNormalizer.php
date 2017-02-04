<?php

namespace Drupal\data_model\Normalizer\JsonSchema\hal;

use Drupal\data_model\Normalizer\JsonSchema\json\FieldDefinitionNormalizer as JsonFieldDefinitionNormalizer;

/**
 * HAL normalizer for FieldDefinition objects.
 */
class FieldDefinitionNormalizer extends JsonFieldDefinitionNormalizer {

  use ReferenceListTrait;

  /**
   * The formats that the Normalizer can handle.
   *
   * @var array
   */
  protected $format = 'schema_json';

  /**
   * The formats that the Normalizer can handle.
   *
   * @var array
   */
  protected $describedFormat = 'hal_json';

}
