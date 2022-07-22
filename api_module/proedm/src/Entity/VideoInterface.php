<?php

namespace Drupal\proedm\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Video entities.
 *
 * @ingroup proedm
 */
interface VideoInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Video name.
   *
   * @return string
   *   Name of the Video.
   */
  public function getName();

  /**
   * Sets the Video name.
   *
   * @param string $name
   *   The Video name.
   *
   * @return \Drupal\proedm\Entity\VideoInterface
   *   The called Video entity.
   */
  public function setName($name);

  /**
   * Gets the Video creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Video.
   */
  public function getCreatedTime();

  /**
   * Sets the Video creation timestamp.
   *
   * @param int $timestamp
   *   The Video creation timestamp.
   *
   * @return \Drupal\proedm\Entity\VideoInterface
   *   The called Video entity.
   */
  public function setCreatedTime($timestamp);

}
