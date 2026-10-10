<?php

/**
 * @file
 * Theme settings form for the Knowles theme.
 */

declare(strict_types=1);

use Drupal\Core\File\Exception\FileException;
use Drupal\Core\File\FileExists;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManager;

/**
 * Implements hook_form_FORM_ID_alter() for system_theme_settings.
 */
function knowles_form_system_theme_settings_alter(array &$form, FormStateInterface $form_state, ?string $form_id = NULL): void {
  // ThemeSettingsForm calls this directly without a form ID. When Knowles is
  // also the active theme, the theme manager calls it a second time, with
  // one; skip that call so the elements and handlers are only added once.
  if ($form_id !== NULL) {
    return;
  }

  $form['knowles'] = [
    '#type' => 'vertical_tabs',
    '#title' => t('Knowles settings'),
    '#weight' => -20,
  ];

  // Appearance.
  $form['knowles_appearance'] = [
    '#type' => 'details',
    '#title' => t('Colors and type'),
    '#group' => 'knowles',
  ];
  $form['knowles_appearance']['color_scheme_mode'] = [
    '#type' => 'radios',
    '#title' => t('Color scheme'),
    '#options' => [
      'auto' => t("Follow the visitor's device (dark or light)"),
      'dark' => t('Dark: wood and brass'),
      'light' => t('Light: parchment'),
    ],
    '#default_value' => knowles_setting('color_scheme_mode') ?: 'auto',
    '#description' => t('"Follow the device" is resolved in CSS from <code>prefers-color-scheme</code>, so it does not vary the page cache.'),
  ];
  $form['knowles_appearance']['custom_colors'] = [
    '#type' => 'checkbox',
    '#title' => t('Use custom colors'),
    '#default_value' => (bool) knowles_setting('custom_colors'),
    '#description' => t('Replace the theme palette. Lighter and darker shades are mixed from each color automatically.'),
  ];
  $form['knowles_appearance']['colors'] = [
    '#type' => 'container',
    '#tree' => TRUE,
    '#states' => [
      'visible' => [':input[name="custom_colors"]' => ['checked' => TRUE]],
    ],
  ];
  $colors = [
    'accent' => [
      t('Accent'),
      t('Links, rules, primary buttons and the logo ring. Default: antique brass (#c5a059).'),
    ],
    'secondary' => [
      t('Secondary'),
      t('Secondary buttons and success states. Default: hunter green (#3a4f41).'),
    ],
    'contrast' => [
      t('Contrast'),
      t('Contrast buttons and warnings. Default: oxblood (#5c2c2e).'),
    ],
  ];
  $defaults = knowles_default_colors();
  foreach ($colors as $key => [$title, $description]) {
    $form['knowles_appearance']['colors'][$key] = [
      '#type' => 'color',
      '#title' => $title,
      '#description' => $description,
      '#default_value' => knowles_setting('colors.' . $key) ?: $defaults[$key],
    ];
  }
  $form['knowles_appearance']['web_fonts'] = [
    '#type' => 'checkbox',
    '#title' => t('Load web fonts from Google Fonts'),
    '#default_value' => (bool) knowles_setting('web_fonts'),
    '#description' => t('Fraunces for headings and Lora for text. When off, no request is made to Google and the theme uses old-style serif fonts installed on the device.'),
  ];
  $form['knowles_appearance']['content_font'] = [
    '#type' => 'radios',
    '#title' => t('Main content font'),
    '#options' => [
      'sans' => t("Sans-serif (the device's system font)"),
      'serif' => t('Serif (the same fonts as the header and footer)'),
    ],
    '#default_value' => knowles_setting('content_font') ?: 'sans',
  ];

  // Logo. Core's own logo settings (above) set the main logo.
  $form['knowles_logo'] = [
    '#type' => 'details',
    '#title' => t('Logo'),
    '#group' => 'knowles',
  ];
  $form['knowles_logo']['logo_style'] = [
    '#type' => 'radios',
    '#title' => t('Logo style'),
    '#options' => [
      'roundel' => t('Roundel: cropped to a circle in a brass ring'),
      'plain' => t('Plain: shown as uploaded'),
    ],
    '#default_value' => knowles_setting('logo_style') ?: 'roundel',
  ];
  $form['knowles_logo']['logo_light_path'] = [
    '#type' => 'textfield',
    '#title' => t('Path to a logo for the light scheme'),
    '#default_value' => knowles_setting('logo_light_path'),
    '#description' => t('Optional. Shown instead of the main logo when the light scheme is in use. Leave blank to use the main logo for both schemes. Examples: <code>public://logo-light.png</code>, <code>themes/custom/example/logo-light.svg</code>.'),
  ];
  $form['knowles_logo']['logo_light_upload'] = [
    '#type' => 'file',
    '#title' => t('Upload a logo for the light scheme'),
    '#description' => t('Fills in the path above.'),
    '#upload_validators' => [
      'FileIsImage' => [],
    ],
  ];

  // Search.
  $form['knowles_search'] = [
    '#type' => 'details',
    '#title' => t('Search'),
    '#group' => 'knowles',
  ];
  $form['knowles_search']['search_button'] = [
    '#type' => 'checkbox',
    '#title' => t('Show a search button in the header'),
    '#default_value' => (bool) knowles_setting('search_button'),
  ];
  $form['knowles_search']['search_label'] = [
    '#type' => 'textfield',
    '#title' => t('Search button label'),
    '#default_value' => knowles_setting('search_label'),
    '#states' => [
      'visible' => [':input[name="search_button"]' => ['checked' => TRUE]],
    ],
  ];
  $form['knowles_search']['search_path'] = [
    '#type' => 'textfield',
    '#title' => t('Search page path'),
    '#default_value' => knowles_setting('search_path'),
    '#description' => t('Where the search button goes when the modal is off, or when JavaScript is unavailable. For example <code>/search/node</code> (core Search) or <code>/search</code> (a Search API view).'),
  ];
  $form['knowles_search']['search_modal'] = [
    '#type' => 'checkbox',
    '#title' => t('Open search in a modal'),
    '#default_value' => (bool) knowles_setting('search_modal'),
    '#description' => t('The button opens a dialog containing the blocks in the <em>Search modal</em> region (for example a search form block). Any link with the class <code>js-search-modal-trigger</code> opens it too.'),
  ];
  $form['knowles_search']['search_title'] = [
    '#type' => 'textfield',
    '#title' => t('Search modal heading'),
    '#default_value' => knowles_setting('search_title'),
    '#states' => [
      'visible' => [':input[name="search_modal"]' => ['checked' => TRUE]],
    ],
  ];

  // Footer.
  $form['knowles_footer'] = [
    '#type' => 'details',
    '#title' => t('Footer'),
    '#group' => 'knowles',
  ];
  $form['knowles_footer']['footer_copyright'] = [
    '#type' => 'textfield',
    '#title' => t('Copyright text'),
    '#maxlength' => 512,
    '#default_value' => knowles_setting('footer_copyright'),
    '#description' => t('<code>@year</code> is replaced with the current year and <code>@site_name</code> with the site name. Basic HTML (links, emphasis) is allowed. Leave blank to hide.'),
  ];
  $form['knowles_footer']['theme_credit'] = [
    '#type' => 'checkbox',
    '#title' => t('Show the theme credit ("Theme: Knowles, based on Pico")'),
    '#default_value' => (bool) knowles_setting('theme_credit'),
  ];

  // The form builder appends the core validate and submit handlers after
  // this runs, so this submit handler comes before the one that saves.
  $form['#validate'][] = 'knowles_theme_settings_validate';
  $form['#submit'][] = 'knowles_theme_settings_submit';
}

/**
 * Form validation handler for knowles_form_system_theme_settings_alter().
 */
function knowles_theme_settings_validate(array &$form, FormStateInterface $form_state): void {
  if (\Drupal::moduleHandler()->moduleExists('file')) {
    $file = _file_save_upload_from_form($form['knowles_logo']['logo_light_upload'], $form_state, 0);
    if ($file) {
      $form_state->setValue('logo_light_upload', $file);
    }
  }

  $path = trim((string) $form_state->getValue('logo_light_path'));
  if ($path !== '' && !$form_state->getValue('logo_light_upload') && !knowles_validate_path($path)) {
    $form_state->setErrorByName('logo_light_path', t('The light scheme logo path is invalid.'));
  }

  $search_path = trim((string) $form_state->getValue('search_path'));
  if ($search_path !== '' && !str_starts_with($search_path, '/')) {
    $form_state->setErrorByName('search_path', t('The search page path must start with a slash.'));
  }
}

/**
 * Form submission handler for knowles_form_system_theme_settings_alter().
 *
 * Runs before the core theme settings submit handler, which saves every
 * remaining form value to knowles.settings.
 */
function knowles_theme_settings_submit(array &$form, FormStateInterface $form_state): void {
  $file = $form_state->getValue('logo_light_upload');
  if ($file) {
    $scheme = \Drupal::config('system.file')->get('default_scheme');
    try {
      $uri = \Drupal::service('file_system')->copy($file->getFileUri(), $scheme . '://', FileExists::Rename);
      $form_state->setValue('logo_light_path', $uri);
    }
    catch (FileException) {
      \Drupal::messenger()->addError(t('The light scheme logo could not be saved.'));
    }
  }
  $form_state->unsetValue('logo_light_upload');
  $form_state->unsetValue('knowles__active_tab');
  $form_state->setValue('logo_light_path', trim((string) $form_state->getValue('logo_light_path')));
}

/**
 * Checks that a logo path points at an existing file.
 *
 * Mirrors ThemeSettingsForm::validatePath(), which is protected.
 *
 * @param string $path
 *   A stream wrapper URI, a path relative to the public files directory, or
 *   a path relative to the Drupal root.
 *
 * @return string|false
 *   The valid path, or FALSE.
 */
function knowles_validate_path(string $path): string|false {
  if (StreamWrapperManager::getScheme($path) !== FALSE) {
    return is_file($path) ? $path : FALSE;
  }
  foreach (['public://' . $path, $path] as $candidate) {
    if (is_file($candidate)) {
      return $candidate;
    }
  }
  return FALSE;
}
