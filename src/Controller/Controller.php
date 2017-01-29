<?php

namespace Drupal\data_model\Controller;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\CacheableResponse;
use Drupal\Core\Cache\CacheableResponseInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\data_model\SchemaFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class Controller extends ControllerBase {

  /**
   * @var \Symfony\Component\Serializer\SerializerInterface
   */
  protected $serializer;

  /**
   * @var \Drupal\data_model\SchemaFactory
   */
  protected $schemaFactory;

  /**
   * @var \Drupal\Core\Cache\CacheableResponse
   */
  protected $response;

  /**
   * Controller constructor.
   *
   * @param \Symfony\Component\Serializer\SerializerInterface $serializer
   * @param \Drupal\data_model\SchemaFactory $typed_data_manager
   * @param \Drupal\Core\Cache\CacheableResponse $response
   */
  public function __construct(SerializerInterface $serializer, SchemaFactory $schema_factory, CacheableResponseInterface $response) {
    $this->serializer = $serializer;
    $this->schemaFactory = $schema_factory;
    $this->response = $response;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('serializer'),
      $container->get('data_model.schema_factory'),
      new CacheableResponse()
    );
  }

  /**
   * Serializes a entity type or bundle definition.
   *
   * We have 2 different data formats involved. One is the schema format (for
   * instance JSON Schema) and the other one is the format that the schema is
   * describing (for instance jsonapi, json, hal+json, …). Ideally we should be
   * able to use the mime type to pass in both formats. Something like:
   * Accept: application/schema+json; describes=jsonapi should do.
   *
   * @param string $entity_type_id
   *   The entity type ID to describe.
   * @param string $bundle
   *   The (optional) bundle to describe.
   * @param Request $request
   *   The request object.
   *
   * @return \Drupal\Core\Cache\CacheableResponse
   *   The response object.
   */
  public function serialize($entity_type_id, $bundle, Request $request) {
    // For now, we'll manually inspect the Accept header in the controller. This
    // is not a great idea, but will do for now.
    list($data_format, $described_media_type) = $this->extractFormatNames($request);

    // Load the data to serialize from the route information on the current
    // request.
    $schema = $this->schemaFactory->create($entity_type_id, $bundle);
    // Serialize the entity type/bundle definition.
    $format = sprintf('%s:%s', $data_format, $described_media_type);
    $content = $this->serializer->serialize($schema, $format, [
      'described_media_type' => $described_media_type,
    ]);

    // Finally, set the contents of the response and return it.
    $this->response->addCacheableDependency($schema);
    $cacheable_dependency = (new CacheableMetadata())
      ->addCacheContexts(['headers:Accept']);
    $this->response->addCacheableDependency($cacheable_dependency);
    $this->response->setContent($content);
    $this->response->headers->set('Content-Type', $request->getMimeType($data_format));
    return $this->response;
  }

  /**
   * Helper function that inspects the request to extract the formats.
   *
   * Extracts the format of the response and media type being described.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return array
   *   An array containing the format of the output and the media type being
   *   described.
   */
  protected function extractFormatNames(Request $request) {
    return [
      $request->getRequestFormat(),
      $request->query->get('_describes'),
    ];
  }

}
