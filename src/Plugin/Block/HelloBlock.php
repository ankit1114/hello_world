<?php

namespace Drupal\hello_world\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;

/**
 * Provides a 'Hello' Block.
 *
 * @Block(
 *   id = "hello_block",
 *   admin_label = @Translation("Hello block"),
 *   category = @Translation("Hello World"),
 * )
 */
class HelloBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    /*return [
      '#type' => 'markup',
      '#markup' => 'Hello World',
    ];
*/
    $form = \Drupal::formBuilder()->getForm('Drupal\hello_world\Form\HelloWorldForm');
    return $form;

  }

}
