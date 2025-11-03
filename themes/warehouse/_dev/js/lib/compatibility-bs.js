/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


(function($) {
    "use strict";
  
    /**
     * Helper – tworzy lub pobiera instancję komponentu Bootstrap dla danego elementu.
     *
     * @param {jQuery} $element - obiekt jQuery danego elementu
     * @param {string} key - klucz, pod którym instancja jest przechowywana (np. 'bs.modal')
     * @param {Function} Constructor - konstruktor Bootstrap (np. bootstrap.Modal)
     * @param {object} options - opcje inicjalizacji (opcjonalnie)
     * @returns {object} instancja komponentu
     */
    function getOrCreateInstance($element, key, Constructor, options) {
      var instance = $element.data(key);
      if (!instance) {
        instance = new Constructor($element[0], options);
        $element.data(key, instance);
      }
      return instance;
    }
  
    /**
     * Modal – kompatybilność jQuery
     *
     * Umożliwia wywoływanie metod:
     *   $(selector).modal('show');
     *   $(selector).modal('hide');
     *   $(selector).modal('toggle');
     * Jeśli nie podamy argumentu lub podamy obiekt opcji – modal zostanie zainicjowany i wyświetlony.
     */
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
      $.fn.modal = function(option) {
        return this.each(function() {
          var $this = $(this);
          var modal = getOrCreateInstance($this, 'bs.modal', bootstrap.Modal, option);
  
          // Jeśli option jest stringiem (np. 'show', 'hide', 'toggle'), wywołaj odpowiednią metodę.
          if (typeof option === 'string' && typeof modal[option] === 'function') {
            modal[option]();
          }
          // Jeśli nie przekazano opcji – domyślnie pokaż modal.
          else if (option === undefined || option === 'show') {
            modal.show();
          }
        });
      };
    }
  
    /**
     * Dropdown – kompatybilność jQuery
     */
    if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
      $.fn.dropdown = function(option) {
        return this.each(function() {
          var $this = $(this);
          var dropdown = getOrCreateInstance($this, 'bs.dropdown', bootstrap.Dropdown, option);
          if (typeof option === 'string' && typeof dropdown[option] === 'function') {
            dropdown[option]();
          }
        });
      };
    }
  
    /**
     * Tooltip – kompatybilność jQuery
     */
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
      $.fn.tooltip = function(option) {
        return this.each(function() {
          var $this = $(this);
          var tooltip = getOrCreateInstance($this, 'bs.tooltip', bootstrap.Tooltip, option);
          if (typeof option === 'string' && typeof tooltip[option] === 'function') {
            tooltip[option]();
          }
        });
      };
    }
  
    /**
     * Popover – kompatybilność jQuery
     */
    if (typeof bootstrap !== 'undefined' && bootstrap.Popover) {
      $.fn.popover = function(option) {
        return this.each(function() {
          var $this = $(this);
          var popover = getOrCreateInstance($this, 'bs.popover', bootstrap.Popover, option);
          if (typeof option === 'string' && typeof popover[option] === 'function') {
            popover[option]();
          }
        });
      };
    }
  
    /**
     * (Opcjonalnie) Migracja atrybutów data-toggle / data-target na nowe data-bs-* (jeśli zewnętrzne moduły korzystają ze starych atrybutów)
     */
    $(function() {
      $('[data-toggle]').each(function() {
        var $el = $(this);
        var toggleValue = $el.attr('data-toggle');
        $el.attr('data-bs-toggle', toggleValue);
      });
  
      $('[data-target]').each(function() {
        var $el = $(this);
        var targetValue = $el.attr('data-target');
        $el.attr('data-bs-target', targetValue);
      });
    });



      /**
   * Dodatkowa obsługa atrybutu data-dismiss="modal"
   *
   * Pozwala zamknąć modal poprzez kliknięcie elementu z atrybutem data-dismiss="modal",
   * co było domyślnie wspierane w Bootstrap 4.
   */
  $(document).on('click', '[data-dismiss="modal"]', function(e) {
    e.preventDefault();
    var $modal = $(this).closest('.modal');
    if ($modal.length) {
      $modal.modal('hide');
    }
  });
  
  })(jQuery);