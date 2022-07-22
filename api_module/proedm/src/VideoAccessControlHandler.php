<?php

namespace Drupal\proedm;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Video entity.
 *
 * @see \Drupal\proedm\Entity\Video.
 */
class VideoAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\proedm\Entity\VideoInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished video entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published video entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit video entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete video entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add video entities');
  }


}
