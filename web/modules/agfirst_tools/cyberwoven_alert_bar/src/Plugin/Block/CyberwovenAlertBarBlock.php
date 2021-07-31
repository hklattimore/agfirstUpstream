<?php

namespace Drupal\cyberwoven_alert_bar\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a 'Cyberwoven Alert Bar' block.
 *
 * @Block(
 *   id = "cyberwoven_alert_bar",
 *   admin_label = @Translation("Alert Bar block"),
 * )
 */
class CyberwovenAlertBarBlock extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $config = \Drupal::config('cyberwoven_alert_bar.settings');

    // Build the block.
    $build = [];

    $block = [
      '#theme' => 'cyberwoven_alert_bar',
      '#attributes' => [
        'class' => ['cw-alert-bar', 'alert'],
        'id' => 'cw-alert-bar',
      ],
      '#enabled' => $config->get('enabled'),
      '#expires' => $config->get('expires'),
      '#severity' => $config->get('severity'),
      '#homepage_only' => $config->get('homepage_only'),
      '#message' => array(
        '#markup' => $config->get('message')['value'],
      ),
      '#more_link_label' => $config->get('more_link_label'),
      '#more_link_url' => $config->get('more_link_url'),
      '#more_link_external' => $config->get('more_link_external'),
      '#alert_color_scheme' => $config->get('alert_color_scheme'),
      '#additional_classes' => $config->get('additional_classes'),
      '#unique_id' => $config->get('unique_id'),
      '#cache' => [
        'tags' => ['config:cyberwoven_alert_bar.settings'],
      ]
    ];

    if ($config->get('enabled')) {
      $build['cw-alert-bar'] = $block;
    }else{
      $build['cw-alert-bar'] = null;
    }

    $build['#attached']['drupalSettings']['cw_alert_bar']['unique_id']= $config->get('unique_id');
    return $build;

  }

  /**
   * {@inheritdoc}
   */
  public function blockAccess(AccountInterface $account) {

    $config = \Drupal::config('cyberwoven_alert_bar.settings');

    $enabled = $config->get('enabled') ?: 0;
    $today = new DrupalDateTime('today');
    $expires = new DrupalDateTime($config->get('expires')); // Defaults to "today".

    // If disabled, or expired, don't show the block.
    if (($enabled == 0) || (strtotime($today) > strtotime($expires))) {
      // Must add cache tag here or block will not show up immediately if admin enables it after the user has already visited the page.
      // Apparently not a problem when access is granted (see below)...but there is when forbidden.
      return AccessResult::forbidden()->addCacheTags(['config:cyberwoven_alert_bar.settings']);
    } else {
      return parent::blockAccess($account);
    }

  }

}
