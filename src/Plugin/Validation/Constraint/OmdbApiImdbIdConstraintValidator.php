<?php

namespace Drupal\omdb_api\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Drupal\omdb_api\Entity\OmdbApiEntityInterface;

/**
 * Validates the Omdb Api Imdb Id constraint.
 */
class OmdbApiImdbIdConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($items, Constraint $constraint) {

    /** @var \Drupal\Core\Field\FieldItemListInterface $items */
    if ($items->isEmpty()) {
      return;
    }

    /** @var \Drupal\Core\Entity\EntityInterface $entity */
    $entity = $items->getEntity();
    $entity_type_id = $entity->getEntityTypeId();

    if ($items->getFieldDefinition()->getName() !== 'imdb_id' || $entity_type_id !== 'omdb_api') {
      // The constraint has been set on wrong field.
      throw new \Exception("The OmdbApiImdbIdConstraint cannot be set on field other than 'imdb_id' of 'omdb_api' entity type.");
    }

    if ($entity instanceof OmdbApiEntityInterface) {
      $imdb_id = $items->value;
      if (is_numeric($imdb_id)) {
        $imdb_id_formatted = str_pad($imdb_id, 7, '0', STR_PAD_LEFT);
        if (!in_array(strlen($imdb_id_formatted), [7, 8])) {
          $this->context->buildViolation($constraint->invalidImdbIdErrorMessage)
            ->atPath('imdb_id')
            ->addViolation();
        }
      }
      elseif (!preg_match("/(?:nm|tt)(\d{7,8})/", strtolower($imdb_id), $matches)) {
        $this->context->buildViolation($constraint->invalidImdbIdErrorMessage)
          ->atPath('imdb_id')
          ->addViolation();
      }
    }
  }

}
