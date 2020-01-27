<?php
/**
 * @file
 * Contains \Drupal\booking\Plugin\Block\CustomBlock.
 */
namespace Drupal\booking\Plugin\Block;

use Drupal\Core\Block\BlockBase;
/**
 * Provides a booking block.
 *
 * @Block(
 *   id = "booking",
 *   admin_label = @Translation("Booking"),
 *   category = @Translation("Booking block")
 * )
 */
class BookingBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
//  public function build() {
//    return array(
//      '#type' => 'markup',
//      '#markup' => 'This custom block content.',
//    );
//  }

  public function referencedEntities() {
    if (empty($this->list)) {
      return array();
    }

    // Collect the IDs of existing entities to load, and directly grab the
    // "autocreate" entities that are already populated in $item->entity.
    $target_entities = $ids = array();
    foreach ($this->list as $delta => $item) {
      if ($item->target_id !== NULL) {
        $ids[$delta] = $item->target_id;
      }
      elseif ($item
        ->hasNewEntity()) {
        $target_entities[$delta] = $item->entity;
      }
    }

    // Load and add the existing entities.
    if ($ids) {
      $target_type = $this
        ->getFieldDefinition()
        ->getSetting('target_type');
      $entities = \Drupal::entityManager()
        ->getStorage($target_type)
        ->loadMultiple($ids);
      foreach ($ids as $delta => $target_id) {
        if (isset($entities[$target_id])) {
          $target_entities[$delta] = $entities[$target_id];
        }
      }

      // Ensure the returned array is ordered by deltas.
      ksort($target_entities);
    }
    return $target_entities;
  }

  /**
   * @inheritDoc
   */
  public function build() {
    // TODO: Implement build() method.

  }

}
