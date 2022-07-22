<?php

namespace Drupal\proedm;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Mensaje entity.
 *
 * @see \Drupal\proedm\Entity\Mensaje.
 */
class MensajeAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\proedm\Entity\MensajeInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished mensaje entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published mensaje entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit mensaje entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete mensaje entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add mensaje entities');
  }


}
