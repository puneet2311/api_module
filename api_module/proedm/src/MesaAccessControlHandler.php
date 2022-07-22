<?php

namespace Drupal\proedm;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Mesa entity.
 *
 * @see \Drupal\proedm\Entity\Mesa.
 */
class MesaAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\proedm\Entity\MesaInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished mesa entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published mesa entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit mesa entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete mesa entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add mesa entities');
  }


}
