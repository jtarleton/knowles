# Knowles

Knowles is a warm, old-world blog theme for Drupal 10.3 and 11, built on
[Pico CSS v2](https://picocss.com). It has two color schemes, dark wood
and light parchment. Visitors can get the one their device prefers, or the
site can force one. The palette uses brass, hunter green and oxblood
accents, with Fraunces for headings and Lora for text. The theme is meant
for personal blogs, journals and small editorial sites on any subject.

For a full description of the theme, visit the
[project page](https://www.drupal.org/project/knowles).

Submit bug reports and feature suggestions, or track changes in the
[issue queue](https://www.drupal.org/project/issues/knowles).


## Table of contents

- Requirements
- Installation
- Configuration
- Regions
- Components and style guide
- Integrations
- Third-party assets
- Maintainers


## Requirements

This theme requires no modules outside of Drupal core. It uses core's
Stable 9 as its base theme.


## Installation

Install as you would normally install a contributed Drupal theme. For further
information, see
[Installing Drupal Themes](https://www.drupal.org/docs/extending-drupal/themes/installing-themes).

```sh
composer require drupal/knowles
drush theme:enable knowles
drush config:set system.theme default knowles -y
```

On install, Knowles places the site branding, main and account menus,
breadcrumbs, messages, help, page title, tabs, actions and main content
blocks. It also puts a core Search form in the search modal when the Search
module is enabled.


## Configuration

Go to **Appearance » Settings » Knowles**
(`/admin/appearance/settings/knowles`). Besides core's logo, favicon and
site name/slogan toggles, the theme adds:

**Colors and type**
- **Color scheme**: follow the visitor's device (default), always dark, or
  always light. "Follow the device" is resolved in CSS with
  `prefers-color-scheme`, so it causes no cache variation and no flash of
  the wrong scheme.
- **Custom colors**: replace the accent (brass), secondary (hunter green)
  and contrast (oxblood) colors. Lighter and darker shades are mixed from
  each color with CSS `color-mix()`. Emails sent through HTML Mail use the
  same colors.
- **Load web fonts**: turn off to make no requests to Google Fonts. The
  theme then uses old-style serif fonts installed on the device.
- **Main content font**: sans-serif (system UI) or serif.

**Logo**
- **Logo style**: *roundel* crops the logo to a circle in a brass ring,
  which suits square marks. *Plain* shows it as uploaded, up to the header
  height, which suits wide wordmarks.
- **Logo for the light scheme**: an optional second logo (path or upload)
  shown when the light scheme is active.

**Search**
- **Show a search button in the header**, with a configurable label.
- **Search page path**: where the button goes, for example `/search/node`
  (core Search) or `/search` (a Search API view).
- **Open search in a modal**: the button opens a dialog containing the
  *Search modal* region. Any link with the class `js-search-modal-trigger`
  opens it too, for example a menu link with that class added through a
  module such as Menu Link Attributes.
- **Search modal heading**.

**Footer**
- **Copyright text**: `@year` and `@site_name` are replaced with the current
  year and the site name. Basic HTML is allowed. Leave blank to hide.
- **Theme credit**: show or hide "Theme: Knowles, based on Pico".

The text settings can be translated with the Configuration Translation
module.


## Regions

| Region | Use |
|---|---|
| Header (branding and menus) | Site branding, main menu, account menu, on one row |
| Primary menu | Extra items at the end of the header bar |
| Breadcrumb, Highlighted, Help | Above the main content |
| Content top, Content | Main column |
| Sidebar | Optional right column, shown only when it has content |
| Footer brand | Left footer column, above the theme credit |
| Footer column one/two/three | Footer link columns (menus work well) |
| Footer legal | Bottom row, after the copyright line |
| Search modal | Hidden until the search button opens it |

The account menu's last link (Log in / Log out) is styled as a brass plate.


## Components and style guide

Open `styleguide/index.html` in a browser to see every component outside
Drupal (`?scheme=light` previews the parchment scheme). Classes you can use
in content or templates include:

- Buttons: `.button--primary`, `.secondary`, `.contrast`, `.outline`.
- Cards: `article` (desk blotter), `.card--book` (with `--hunter` /
  `--tobacco`), `.card--note`, `.card-grid`.
- Quotes and callouts: `.pull-quote`, `.callout--tip`, `.callout--warning`.
- Media frames: `.frame--map`, `.frame--polaroid`, `.frame--cornered`.
- Navigation grid: `.nav-grid` with `.nav-grid__item` links.
- Surfaces: add `data-theme="light"` or `.surface-parchment` to any element
  to make it a parchment surface on the dark scheme (and vice versa with
  `data-theme="dark"`).

All colors are CSS custom properties prefixed `--kn-` (see
`css/base/tokens.css`). A sub-theme can override them on `:root`.


## Integrations

Knowles includes optional styling for these contributed modules. It has no
effect when they are not installed.

- [HTML Mail](https://www.drupal.org/project/html_mail): a branded email
  template (`templates/misc/html-mail.html.twig`).
- [Search API](https://www.drupal.org/project/search_api): a view with the
  ID `search` gets the same form and result styling as core Search, and
  autocomplete lists open inside the search modal.
- [Magic Login](https://www.drupal.org/project/magic_login) and
  [OpenID Connect](https://www.drupal.org/project/openid_connect): login
  form styling.


## Third-party assets

These load from public CDNs and are not included in this project:

- Pico CSS 2.1.1 (MIT), from jsDelivr, pinned with Subresource Integrity.
  Knowles uses its *conditional* build, which only styles markup inside
  `.pico`, so the admin toolbar and dialogs keep their core styling.
- Fraunces and Lora (SIL Open Font License 1.1), from Google Fonts, unless
  turned off in the theme settings.

To serve them from your own server, override the `knowles/pico` and
`knowles/fonts` libraries with `libraries-override` in a sub-theme.


## Maintainers

- [jtarleton](https://www.drupal.org/u/jtarleton)
