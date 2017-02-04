<?php


namespace Drupal\data_model\Normalizer\hal;

use Drupal\data_model\Normalizer\json\DataDefinitionNormalizer as JsonDataDefinitionNormalizer;

class DataDefinitionNormalizer extends JsonDataDefinitionNormalizer {

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
