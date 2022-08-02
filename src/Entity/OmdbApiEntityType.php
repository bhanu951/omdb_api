<?php

namespace Drupal\omdb_api\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the OMDB API Type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "omdb_api_type",
 *   label = @Translation("OMDB API Type"),
 *   label_collection = @Translation("OMDB API Types"),
 *   label_singular = @Translation("omdb api type"),
 *   label_plural = @Translation("omdb apis types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count omdb apis type",
 *     plural = "@count omdb apis types",
 *   ),
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\omdb_api\Entity\Form\OmdbApiEntityTypeForm",
 *       "edit" = "Drupal\omdb_api\Entity\Form\OmdbApiEntityTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "list_builder" = "Drupal\omdb_api\Entity\ListBuilder\OmdbApiEntityTypeListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   admin_permission = "administer omdb api types",
 *   bundle_of = "omdb_api",
 *   config_prefix = "omdb_api_type",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/omdb_api_types/add",
 *     "edit-form" = "/admin/structure/omdb_api_types/manage/{omdb_api_type}",
 *     "delete-form" = "/admin/structure/omdb_api_types/manage/{omdb_api_type}/delete",
 *     "collection" = "/admin/structure/omdb_api_types",
 *     "canonical" = "/admin/structure/omdb_api_types"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "uuid",
 *   }
 * )
 */
class OmdbApiEntityType extends ConfigEntityBundleBase {

  /**
   * The machine name of this omdb api type.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the omdb api type.
   *
   * @var string
   */
  protected $label;

}
