<?php

namespace Drupal\Tests\graphql\Kernel\DataProducer;

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Tests\graphql\Kernel\GraphQLTestBase;
use Drupal\node\Entity\NodeType;
use Drupal\graphql\GraphQL\ResolverBuilder;

/**
 * Test the entity_definition data producer and friends.
 *
 * @group graphql
 */
class EntityDefinitionTest extends GraphQLTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $content_type = NodeType::create([
      'type' => 'article',
      'name' => 'article',
    ]);
    $content_type->save();

    // Create a form display.
    $form_display = EntityFormDisplay::create([
      'targetEntityType' => 'node',
      'bundle' => 'article',
      'mode' => 'default',
    ]);
    $form_display->save();

    $schema = <<<GQL
      type EntityDefinition {
        label: String
        fields: [EntityDefinitionField]
      }

      type EntityDefinitionField {
        id: String
        label: String
        description: String
        type: String
        required: Boolean
        multiple: Boolean
        maxNumItems: Int
        status: Boolean
        defaultValue: String
        isReference: Boolean
        isHidden: Boolean
        weight: Int
        settings: [KeyValue]
      }

      scalar KeyValue

      enum FieldTypes {
        ALL
        BASE_FIELDS
        FIELD_CONFIG
      }

      type Query {
        entityDefinition(
          entity_type: String!
          bundle: String
          field_types: FieldTypes
        ): EntityDefinition
      }
GQL;

    $this->setUpSchema($schema);

    $registry = $this->registry;
    $builder = new ResolverBuilder();

    // Entity definition query.
    $registry->addFieldResolver('Query', 'entityDefinition',
      $builder->produce('entity_definition', [
        'entity_type' => $builder->fromArgument('entity_type'),
        'bundle' => $builder->fromArgument('bundle'),
        'field_types' => $builder->fromArgument('field_types'),
      ])
    );
    // Entity definition fields.
    $registry->addFieldResolver('EntityDefinition', 'label',
      $builder->produce('entity_definition_label', [
        'entity_definition' => $builder->fromParent(),
      ])
    );
    $registry->addFieldResolver('EntityDefinition', 'fields',
      $builder->produce('entity_definition_fields', [
        'entity_definition' => $builder->fromParent(),
        'bundle_context' => $builder->fromContext('bundle'),
        'field_types_context' => $builder->fromContext('field_types'),
      ])
    );
    $registry->addFieldResolver('EntityDefinitionField', 'id',
      $builder->produce('entity_definition_field_id', [
        'entity_definition_field' => $builder->fromParent(),
      ])
    );
    $registry->addFieldResolver('EntityDefinitionField', 'label',
      $builder->produce('entity_definition_field_label', [
        'entity_definition_field' => $builder->fromParent(),
      ])
    );
    $registry->addFieldResolver('EntityDefinitionField', 'description',
      $builder->produce('entity_definition_field_description', [
        'entity_definition_field' => $builder->fromParent(),
      ])
    );
    $registry->addFieldResolver('EntityDefinitionField', 'type',
      $builder->produce('entity_definition_field_type', [
        'entity_definition_field' => $builder->fromParent(),
      ])
    );
    $registry->addFieldResolver('EntityDefinitionField', 'required',
      $builder->produce('entity_definition_field_required', [
        'entity_definition_field' => $builder->fromParent(),
      ])
    );
    $registry->addFieldResolver('EntityDefinitionField', 'multiple',
      $builder->produce('entity_definition_field_multiple', [
        'entity_definition_field' => $builder->fromParent(),
      ])
    );
    $registry->addFieldResolver('EntityDefinitionField', 'maxNumItems',
      $builder->produce('entity_definition_field_max_num_items', [
        'entity_definition_field' => $builder->fromParent(),
      ])
    );
    $registry->addFieldResolver('EntityDefinitionField', 'status',
      $builder->produce('entity_definition_field_status', [
        'entity_definition_field' => $builder->fromParent(),
      ])
    );
    $registry->addFieldResolver('EntityDefinitionField', 'defaultValue',
      $builder->produce('entity_definition_field_default_value', [
        'entity_definition_field' => $builder->fromParent(),
      ])
    );
    $registry->addFieldResolver('EntityDefinitionField', 'defaultValues',
      $builder->produce('entity_definition_field_additional_default_value', [
        'entity_definition_field' => $builder->fromParent(),
      ])
    );
    $registry->addFieldResolver('EntityDefinitionField', 'isReference',
      $builder->produce('entity_definition_field_reference', [
        'entity_definition_field' => $builder->fromParent(),
      ])
    );
    $registry->addFieldResolver('EntityDefinitionField', 'isHidden',
      $builder->produce('entity_definition_field_hidden', [
        'entity_definition_field' => $builder->fromParent(),
        'entity_form_display_context' => $builder->fromContext('entity_form_display'),
      ])
    );
    $registry->addFieldResolver('EntityDefinitionField', 'weight',
      $builder->produce('entity_definition_field_weight', [
        'entity_definition_field' => $builder->fromParent(),
        'entity_form_display_context' => $builder->fromContext('entity_form_display'),
      ])
    );
    $registry->addFieldResolver('EntityDefinitionField', 'settings',
      $builder->produce('translatable_entity_definition_field_settings', [
        'entity_definition_field' => $builder->fromParent(),
        'entity_form_display_context' => $builder->fromContext('entity_form_display'),
      ])
    );
  }

  /**
   * Tests that retrieving an entity definition works.
   */
  public function testEntityDefinition() {
    $query = <<<GQL
      query {
        entityDefinition(entity_type: "node", bundle: "article") {
          label
          fields {
            id
            label
            description
            type
            required
            multiple
            maxNumItems
            status
            defaultValue
            isReference
            isHidden
            weight
            settings
          }
        }
      }
GQL;

    $this->assertResults($query, [], [
      'entityDefinition' =>
      [
        'label' => 'Content',
        'fields' =>
        [
          0 =>
          [
            'id' => 'nid',
            'label' => 'ID',
            'description' => NULL,
            'type' => 'integer',
            'required' => FALSE,
            'multiple' => FALSE,
            'maxNumItems' => 1,
            'status' => TRUE,
            'defaultValue' => NULL,
            'isReference' => FALSE,
            'isHidden' => FALSE,
            'weight' => 0,
            'settings' => NULL,
          ],
          1 =>
          [
            'id' => 'uuid',
            'label' => 'UUID',
            'description' => NULL,
            'type' => 'uuid',
            'required' => FALSE,
            'multiple' => FALSE,
            'maxNumItems' => 1,
            'status' => TRUE,
            'defaultValue' => NULL,
            'isReference' => FALSE,
            'isHidden' => FALSE,
            'weight' => 0,
            'settings' => NULL,
          ],
          2 =>
          [
            'id' => 'vid',
            'label' => 'Revision ID',
            'description' => NULL,
            'type' => 'integer',
            'required' => FALSE,
            'multiple' => FALSE,
            'maxNumItems' => 1,
            'status' => TRUE,
            'defaultValue' => NULL,
            'isReference' => FALSE,
            'isHidden' => FALSE,
            'weight' => 0,
            'settings' => NULL,
          ],
          3 =>
          [
            'id' => 'langcode',
            'label' => 'Language',
            'description' => NULL,
            'type' => 'language',
            'required' => FALSE,
            'multiple' => FALSE,
            'maxNumItems' => 1,
            'status' => TRUE,
            'defaultValue' => NULL,
            'isReference' => FALSE,
            'isHidden' => FALSE,
            'weight' => 2,
            'settings' => NULL,
          ],
          4 =>
          [
            'id' => 'type',
            'label' => 'Content type',
            'description' => NULL,
            'type' => 'entity_reference',
            'required' => TRUE,
            'multiple' => FALSE,
            'maxNumItems' => 1,
            'status' => TRUE,
            'defaultValue' => NULL,
            'isReference' => TRUE,
            'isHidden' => FALSE,
            'weight' => 0,
            'settings' => NULL,
          ],
          5 =>
          [
            'id' => 'revision_timestamp',
            'label' => 'Revision create time',
            'description' => 'The time that the current revision was created.',
            'type' => 'created',
            'required' => FALSE,
            'multiple' => FALSE,
            'maxNumItems' => 1,
            'status' => TRUE,
            'defaultValue' => NULL,
            'isReference' => FALSE,
            'isHidden' => FALSE,
            'weight' => 0,
            'settings' => NULL,
          ],
          6 =>
          [
            'id' => 'revision_uid',
            'label' => 'Revision user',
            'description' => 'The user ID of the author of the current revision.',
            'type' => 'entity_reference',
            'required' => FALSE,
            'multiple' => FALSE,
            'maxNumItems' => 1,
            'status' => TRUE,
            'defaultValue' => NULL,
            'isReference' => TRUE,
            'isHidden' => FALSE,
            'weight' => 0,
            'settings' => NULL,
          ],
          7 =>
          [
            'id' => 'revision_log',
            'label' => 'Revision log message',
            'description' => 'Briefly describe the changes you have made.',
            'type' => 'string_long',
            'required' => FALSE,
            'multiple' => FALSE,
            'maxNumItems' => 1,
            'status' => TRUE,
            'defaultValue' => NULL,
            'isReference' => FALSE,
            'isHidden' => FALSE,
            'weight' => 25,
            'settings' => NULL,
          ],
          8 =>
          [
            'id' => 'status',
            'label' => 'Published',
            'description' => NULL,
            'type' => 'boolean',
            'required' => FALSE,
            'multiple' => FALSE,
            'maxNumItems' => 1,
            'status' => TRUE,
            'defaultValue' => '1',
            'isReference' => FALSE,
            'isHidden' => FALSE,
            'weight' => 120,
            'settings' => NULL,
          ],
          9 =>
          [
            'id' => 'uid',
            'label' => 'Authored by',
            'description' => 'The username of the content author.',
            'type' => 'entity_reference',
            'required' => FALSE,
            'multiple' => FALSE,
            'maxNumItems' => 1,
            'status' => TRUE,
            'defaultValue' => NULL,
            'isReference' => TRUE,
            'isHidden' => FALSE,
            'weight' => 5,
            'settings' => NULL,
          ],
          10 =>
          [
            'id' => 'title',
            'label' => 'Title',
            'description' => NULL,
            'type' => 'string',
            'required' => TRUE,
            'multiple' => FALSE,
            'maxNumItems' => 1,
            'status' => TRUE,
            'defaultValue' => NULL,
            'isReference' => FALSE,
            'isHidden' => FALSE,
            'weight' => -5,
            'settings' => NULL,
          ],
          11 =>
          [
            'id' => 'created',
            'label' => 'Authored on',
            'description' => 'The time that the node was created.',
            'type' => 'created',
            'required' => FALSE,
            'multiple' => FALSE,
            'maxNumItems' => 1,
            'status' => TRUE,
            'defaultValue' => NULL,
            'isReference' => FALSE,
            'isHidden' => FALSE,
            'weight' => 10,
            'settings' => NULL,
          ],
          12 =>
          [
            'id' => 'changed',
            'label' => 'Changed',
            'description' => 'The time that the node was last edited.',
            'type' => 'changed',
            'required' => FALSE,
            'multiple' => FALSE,
            'maxNumItems' => 1,
            'status' => TRUE,
            'defaultValue' => NULL,
            'isReference' => FALSE,
            'isHidden' => FALSE,
            'weight' => 0,
            'settings' => NULL,
          ],
          13 =>
          [
            'id' => 'promote',
            'label' => 'Promoted to front page',
            'description' => NULL,
            'type' => 'boolean',
            'required' => FALSE,
            'multiple' => FALSE,
            'maxNumItems' => 1,
            'status' => TRUE,
            'defaultValue' => '1',
            'isReference' => FALSE,
            'isHidden' => FALSE,
            'weight' => 15,
            'settings' => NULL,
          ],
          14 =>
          [
            'id' => 'sticky',
            'label' => 'Sticky at top of lists',
            'description' => NULL,
            'type' => 'boolean',
            'required' => FALSE,
            'multiple' => FALSE,
            'maxNumItems' => 1,
            'status' => TRUE,
            'defaultValue' => '',
            'isReference' => FALSE,
            'isHidden' => FALSE,
            'weight' => 16,
            'settings' => NULL,
          ],
          15 =>
          [
            'id' => 'default_langcode',
            'label' => 'Default translation',
            'description' => 'A flag indicating whether this is the default translation.',
            'type' => 'boolean',
            'required' => FALSE,
            'multiple' => FALSE,
            'maxNumItems' => 1,
            'status' => TRUE,
            'defaultValue' => '1',
            'isReference' => FALSE,
            'isHidden' => FALSE,
            'weight' => 0,
            'settings' => NULL,
          ],
          16 =>
          [
            'id' => 'revision_default',
            'label' => 'Default revision',
            'description' => 'A flag indicating whether this was a default revision when it was saved.',
            'type' => 'boolean',
            'required' => FALSE,
            'multiple' => FALSE,
            'maxNumItems' => 1,
            'status' => TRUE,
            'defaultValue' => '',
            'isReference' => FALSE,
            'isHidden' => FALSE,
            'weight' => 0,
            'settings' => NULL,
          ],
          17 =>
          [
            'id' => 'revision_translation_affected',
            'label' => 'Revision translation affected',
            'description' => 'Indicates if the last edit of a translation belongs to current revision.',
            'type' => 'boolean',
            'required' => FALSE,
            'multiple' => FALSE,
            'maxNumItems' => 1,
            'status' => TRUE,
            'defaultValue' => '',
            'isReference' => FALSE,
            'isHidden' => FALSE,
            'weight' => 0,
            'settings' => NULL,
          ],
        ],
      ],
    ]
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function defaultCacheMaxAge() {
    // @todo this is wrong, we should have a maximum of caching for entity
    // definitions, not 0.
    return 0;
  }

  /**
   * {@inheritdoc}
   */
  protected function defaultCacheContexts() {
    return ['languages:language_interface', 'user.permissions'];
  }

  /**
   * {@inheritdoc}
   */
  protected function defaultCacheTags() {
    $tags = parent::defaultCacheTags();
    $tags[] = 'config:core.entity_form_display.node.article.default';
    return $tags;
  }

}
