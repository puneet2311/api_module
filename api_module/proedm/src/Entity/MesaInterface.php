<?php

namespace Drupal\proedm\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Mesa entities.
 *
 * @ingroup proedm
 */
interface MesaInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Mesa name.
   *
   * @return string
   *   Name of the Mesa.
   */
  public function getName();

  /**
   * Sets the Mesa name.
   *
   * @param string $name
   *   The Mesa name.
   *
   * @return \Drupal\proedm\Entity\MesaInterface
   *   The called Mesa entity.
   */
  public function setName($name);

  /**
   * Gets the Mesa creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Mesa.
   */
  public function getCreatedTime();

  /**
   * Sets the Mesa creation timestamp.
   *
   * @param int $timestamp
   *   The Mesa creation timestamp.
   *
   * @return \Drupal\proedm\Entity\MesaInterface
   *   The called Mesa entity.
   */
  public function setCreatedTime($timestamp);

}
