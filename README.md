# Data Model

<hr/>

**Credits:** This module is a fork of the excellent
[Schemata](http://drupal.org/project/schemata) module. Much of the code present
in this repository has been taken from that module. There is an issue to merge
back this code in the Schemata module.

<hr/>

This module allows you to expose the schema of the content model in Drupal in a
comprehensive format.

This module is focused towards creating a schema document describing the entity
bundles for the different REST formats. The module currently covers:

  * The `json` format (`application/json`).
  * The `hal_json` format (`application/hal+json`).
  * The `api_json` format (`application/vnd.api+json`).

Currently, the only way to output these formats is using the
[JSON Schema](http://www.json-schema.org) specification for data description.
 Other output formats are planned, such as Markdown and HTML.

## Usage
You can obtain the schema either making a request to an exposed route or by
using the programmatic API.

Each output format should be contained in its own submodule. Enable the
submodule for the format that you need first.

```
drush en -y data_model_json_schema
```

### Request
Create a request against `/data-model/{entity_type}/{bundle}?_format={output_format}&_describes={described_format}`
For instance:

  * `/data-model/node/article?_format=schema_json&_describes=api_json`
  * `/data-model/user?_format=schema_json&_describes=api_json` (omit the bundle
  if the entity type has no bundles).

### Programmatically
```php
// Input variables.
$entity_type_id = 'node';
$bundle = 'article';
$output_format = 'schema_json';
$described_format = 'api_json';

$schema_factory = \Drupal::service('data_model.schema_factory');
$serializer = \Drupal::service('serializer');
$schema = $schema_factory->create($entity_type_id, $bundle);
$format = $output_format . ':' . $described_format;

// Output.
$serializer->serialize($schema, $format);
```
