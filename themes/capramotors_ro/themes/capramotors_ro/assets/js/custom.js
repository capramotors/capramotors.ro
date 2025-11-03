/*
 * Custom code goes here.
 * A template should always ship with an empty custom.js
 */

(function () {
  'use strict';

  /**
   * Check if a facet contains numeric values
   * @param {Element} facetElement - The facet element
   * @returns {boolean} - True if facet contains numeric values
   */
  function isNumericFacet(facetElement) {
    const inputs = facetElement.querySelectorAll('input[data-search-url]');
    let numericCount = 0;

    inputs.forEach(function (input) {
      const label = input.closest('label');
      if (label) {
        const link = label.querySelector('.js-search-link, a');
        const textContent = link
          ? link.textContent.trim()
          : label.textContent.trim();
        // Extract numeric value (handles decimals like 55.5)
        const match = textContent.match(/^(\d+\.?\d*)(\s|$)/);
        if (match) {
          numericCount++;
        }
      }
    });

    // If at least 3 values are numeric, consider it a numeric facet
    return numericCount >= 3;
  }

  /**
   * Initialize slider for numeric facet values
   * @param {Element|string} facetElementOrId - The facet element or its ID
   */
  function initializeFacetSlider(facetElementOrId) {
    const facetElement =
      typeof facetElementOrId === 'string'
        ? document.getElementById(facetElementOrId)
        : facetElementOrId;

    if (!facetElement) {
      console.log('Facet not found:', facetElementOrId);
      return false;
    }

    // Check if already initialized
    if (facetElement.querySelector('.facet-slider-container')) {
      return false;
    }

    // Extract numeric values from facet items
    const values = [];
    const inputs = facetElement.querySelectorAll('input[data-search-url]');

    inputs.forEach(function (input) {
      // Try to extract number from the label text
      const label = input.closest('label');
      if (label) {
        const link = label.querySelector('.js-search-link, a');
        const textContent = link
          ? link.textContent.trim()
          : label.textContent.trim();

        // Extract numeric value from start of text (handles decimals like 55.5)
        // This matches patterns like "17718", "55.5", "123 (5)" but not "Item 123"
        const match = textContent.match(/^(\d+\.?\d*)/);
        if (match) {
          const numValue = parseFloat(match[1]);
          if (!isNaN(numValue)) {
            values.push(numValue);
          }
        }
      }
    });

    if (values.length < 2) {
      console.log('Not enough numeric values found in facet');
      return false;
    }

    // Calculate min and max
    const min = Math.min(...values);
    const max = Math.max(...values);

    const facetId =
      facetElement.id || 'facet_' + Math.random().toString(36).substr(2, 9);
    console.log('Initializing slider for facet', facetId, '- Range:', min, 'to', max);

    // Create slider container
    const sliderContainer = document.createElement('div');
    sliderContainer.className = 'facet-slider-container';
    sliderContainer.innerHTML = `
            <div class="facet-slider-wrapper">
                <input type="range"
                       id="${facetId}_slider_min"
                       class="facet-slider-min"
                       min="${min}"
                       max="${max}"
                       value="${min}"
                       step="${(max - min) / 100}">
                <input type="range"
                       id="${facetId}_slider_max"
                       class="facet-slider-max"
                       min="${min}"
                       max="${max}"
                       value="${max}"
                       step="${(max - min) / 100}">
            </div>
            <div class="facet-slider-values">
                <span class="facet-slider-value-min">${min}</span>
                <span class="facet-slider-separator"> - </span>
                <span class="facet-slider-value-max">${max}</span>
            </div>
        `;

    // Hide the original checkbox list
    const checkboxList = facetElement.querySelector('ul') || facetElement;
    checkboxList.style.display = 'none';

    // Insert slider before the hidden list
    facetElement.insertBefore(sliderContainer, checkboxList);

    // Initialize dual-range slider
    const minSlider = sliderContainer.querySelector('.facet-slider-min');
    const maxSlider = sliderContainer.querySelector('.facet-slider-max');
    const minValue = sliderContainer.querySelector('.facet-slider-value-min');
    const maxValue = sliderContainer.querySelector('.facet-slider-value-max');

    // Update min slider
    minSlider.addEventListener('input', function () {
      const minVal = parseFloat(this.value);
      if (minVal > parseFloat(maxSlider.value)) {
        maxSlider.value = minVal;
        updateMaxDisplay();
      }
      updateMinDisplay();
      updateFacetSelection(minVal, parseFloat(maxSlider.value));
    });

    // Update max slider
    maxSlider.addEventListener('input', function () {
      const maxVal = parseFloat(this.value);
      if (maxVal < parseFloat(minSlider.value)) {
        minSlider.value = maxVal;
        updateMinDisplay();
      }
      updateMaxDisplay();
      updateFacetSelection(parseFloat(minSlider.value), maxVal);
    });

    function updateMinDisplay() {
      minValue.textContent = parseFloat(minSlider.value).toLocaleString();
    }

    function updateMaxDisplay() {
      maxValue.textContent = parseFloat(maxSlider.value).toLocaleString();
    }

    // Update facet checkboxes based on slider values
    function updateFacetSelection(minVal, maxVal) {
      inputs.forEach(function (input) {
        const label = input.closest('label');
        if (label) {
          const link = label.querySelector('.js-search-link, a');
          const textContent = link
            ? link.textContent.trim()
            : label.textContent.trim();
          // Extract numeric value from start of text
          const match = textContent.match(/^(\d+\.?\d*)/);

          if (match) {
            const numValue = parseFloat(match[1]);
            // Check/uncheck based on range
            if (numValue >= minVal && numValue <= maxVal) {
              input.checked = true;
              // Trigger change event if needed
              input.dispatchEvent(new Event('change', { bubbles: true }));
            } else {
              input.checked = false;
              input.dispatchEvent(new Event('change', { bubbles: true }));
            }
          }
        }
      });
    }

    // Initialize display
    updateMinDisplay();
    updateMaxDisplay();

    return true;
  }

  /**
   * Auto-detect and initialize sliders for all numeric facets on the page
   */
  function autoDetectAndInitializeSliders() {
    // Find all facet elements (they typically have IDs starting with 'facet_' or have class 'facet')
    const facetSelectors = [
      '[id^="facet_"]',
      '.facet',
      '.facet-type-checkbox',
      'ul[id^="facet"]',
    ];

    const facets = [];
    facetSelectors.forEach(function (selector) {
      const elements = document.querySelectorAll(selector);
      elements.forEach(function (el) {
        if (!facets.includes(el)) {
          facets.push(el);
        }
      });
    });

    console.log('Found', facets.length, 'potential facets');

    // Check each facet and initialize slider if it's numeric
    facets.forEach(function (facet) {
      if (isNumericFacet(facet)) {
        console.log('Numeric facet detected:', facet.id || 'unknown');
        initializeFacetSlider(facet);
      }
    });
  }

  /**
   * Initialize sliders for multiple facet IDs
   * @param {Array} facetIds - Array of facet IDs (e.g., ['facet_78268', 'facet_12345'])
   */
  function initializeFacetSliders(facetIds) {
    if (!Array.isArray(facetIds)) {
      console.error('facetIds must be an array');
      return;
    }

    facetIds.forEach(function (facetId) {
      // Check if facet exists on page
      if (document.getElementById(facetId)) {
        initializeFacetSlider(facetId);
      } else {
        // If facet doesn't exist yet (AJAX loaded), wait for it
        const observer = new MutationObserver(function (mutations, obs) {
          if (document.getElementById(facetId)) {
            initializeFacetSlider(facetId);
            obs.disconnect();
          }
        });

        observer.observe(document.body, {
          childList: true,
          subtree: true,
        });
      }
    });
  }

   // Initialize when DOM is ready
   function init() {
     // Auto-detect numeric facets and initialize sliders
     autoDetectAndInitializeSliders();
   }

   if (document.readyState === 'loading') {
     document.addEventListener('DOMContentLoaded', init);
   } else {
     // DOM already loaded
     init();
   }

   // Also re-initialize after AJAX updates (PrestaShop often uses AJAX for facets)
   // Listen for common AJAX completion events
   document.addEventListener('DOMContentLoaded', function () {
     // Use MutationObserver to watch for new facets added via AJAX
     const observer = new MutationObserver(function (mutations) {
       let shouldCheck = false;
       mutations.forEach(function (mutation) {
         if (mutation.addedNodes.length) {
           mutation.addedNodes.forEach(function (node) {
             if (
               node.nodeType === 1 &&
               ((node.id && node.id.startsWith('facet_')) ||
                (node.querySelector && node.querySelector('[id^="facet_"]')))
             ) {
               shouldCheck = true;
             }
           });
         }
       });

       if (shouldCheck) {
         // Debounce - wait a bit for all AJAX updates to complete
         setTimeout(function () {
           autoDetectAndInitializeSliders();
         }, 300);
       }
     });

     observer.observe(document.body, {
       childList: true,
       subtree: true,
     });
   });

   // Export for manual initialization if needed
   window.initFacetSlider = initializeFacetSlider;
   window.initFacetSliders = initializeFacetSliders;
   window.autoDetectFacetSliders = autoDetectAndInitializeSliders;
})();
