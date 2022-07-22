<?php

namespace Drupal\proedm;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Noticia entity.
 *
 * @see \Drupal\proedm\Entity\Noticia.
 */
class NoticiaAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\proedm\Entity\NoticiaInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished noticia entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published noticia entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit noticia entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete noticia entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add noticia entities');
  }


}
