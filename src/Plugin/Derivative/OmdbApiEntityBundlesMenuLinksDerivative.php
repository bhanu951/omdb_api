<?php

namespace Drupal\omdb_api\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Derivative class that provides the menu links for OMDB API Entities.
 */
class OmdbApiEntityBundlesMenuLinksDerivative extends DeriverBase implements ContainerDeriverInterface {

  use StringTranslationTrait;

  /**
   * Entity Type Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity bundle info.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * Creates an OMDB API Entities Bundles Menu Links Derivative instance.
   *
   * @param string $base_plugin_id
   *   Plugin Base ID.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity Type Manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle info.
   */
  public function __construct($base_plugin_id, EntityTypeManagerInterface $entity_type_manager, EntityTypeBundleInfoInterface $entity_type_bundle_info) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $base_plugin_id,
      $container->get('entity_type.manager'),
      $container->get('entity_type.bundle.info')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {

    // Get OMDB API Entity Bundle Information.
    $bundles = $this->entityTypeBundleInfo->getBundleInfo('omdb_api');

    foreach ($bundles as $key => $bundle) {
      $this->derivatives[$key] = $base_plugin_definition;
      $this->derivatives[$key]['route_name'] = 'entity.omdb_api.add_form';
      $this->derivatives[$key]['route_parameters'] = ['omdb_api_type' => $key];
      $this->derivatives[$key]['parent'] = 'entity.omdb_api.collection';
      $this->derivatives[$key]['title'] = ucfirst($bundle['label']);
      $this->derivatives[$key]['admin_label'] = $this->t('Adds : @type', ['@type' => ucfirst($bundle['label'])]);
    }

    return parent::getDerivativeDefinitions($base_plugin_definition);
  }

}
