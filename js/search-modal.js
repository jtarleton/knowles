/**
 * @file
 * Site search modal.
 *
 * The header search button, and any other link with the class
 * js-search-modal-trigger, points at the search page, so it still works
 * without JS. With JS, clicking it opens #site-search (a native <dialog>
 * holding the "Search modal" region) instead of navigating.
 *
 * <dialog>.showModal() gives us the backdrop, focus trapping, inert page
 * and Esc-to-close for free; this only wires up the trigger, the close
 * button, clicks on the backdrop, and focusing the search field.
 */
((Drupal, once) => {
  /**
   * Focuses the dialog's search box, with the caret after any existing text.
   *
   * @param {HTMLDialogElement} dialog
   *   The search dialog.
   */
  function focusSearchInput(dialog) {
    const input = dialog.querySelector(
      'input[type="search"], input[type="text"]',
    );
    if (input) {
      input.focus();
      input.setSelectionRange(input.value.length, input.value.length);
    }
  }

  Drupal.behaviors.knowlesSearchModal = {
    attach(context) {
      const dialog = document.getElementById('site-search');
      if (!dialog || typeof dialog.showModal !== 'function') {
        // No modal on this page (or no <dialog> support): the link just
        // goes to the search page.
        return;
      }

      once('search-modal-trigger', '.js-search-modal-trigger', context).forEach(
        (trigger) => {
          trigger.addEventListener('click', (event) => {
            // Let modified clicks (new tab, etc.) through to the search page.
            if (
              event.metaKey ||
              event.ctrlKey ||
              event.shiftKey ||
              event.altKey ||
              event.button !== 0
            ) {
              return;
            }
            event.preventDefault();
            dialog.showModal();
            document.documentElement.classList.add('search-modal-is-open');
            focusSearchInput(dialog);
          });
        },
      );

      once('search-modal', dialog).forEach(() => {
        const close = () => dialog.close();
        dialog
          .querySelectorAll('[data-search-modal-close]')
          .forEach((button) => {
            button.addEventListener('click', close);
          });

        // Pico makes the <dialog> itself the full-viewport backdrop, so a
        // click that lands on the dialog element (not inside the panel)
        // is a click outside.
        dialog.addEventListener('click', (event) => {
          if (event.target === dialog) {
            dialog.close();
          }
        });

        // Esc first closes an open autocomplete list (jQuery UI handles
        // that); only close the dialog when no suggestion menu is showing.
        dialog.addEventListener('cancel', (event) => {
          const menu = dialog.querySelector('.ui-autocomplete');
          if (menu && menu.offsetParent !== null) {
            event.preventDefault();
          }
        });

        dialog.addEventListener('close', () => {
          document.documentElement.classList.remove('search-modal-is-open');
        });
      });
    },
  };
})(Drupal, once);
