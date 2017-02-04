<?php

namespace Drupal\data_model\Normalizer\hal;

use Drupal\data_model\Normalizer\json\ListDataDefinitionNormalizer as JsonListDataDefinitionNormalizer;

/**
 * HAL normalizer for ListDataDefinitionInterface objects.
 */
class ListDataDefinitionNormalizer extends JsonListDataDefinitionNormalizer {

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
