<?php

/**
 * @file
 * Contains video.page.inc.
 *
 * Page callback for Video entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Video templates.
 *
 * Default template: video.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_video(array &$variables) {
  // Fetch Video Entity Object.
  $video = $variables['elements']['#video'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
