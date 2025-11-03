/*
 * Custom code goes here.
 * A template should always ship with an empty custom.js
 */

(function () {
  'use strict';

  /**
   * Initialize slider for numeric facet values
   * @param {string} facetId - The ID of the facet (e.g., 'facet_78268')
   */
  function initializeFacetSlider(facetId) {
    const facetElement = document.getElementById(facetId);
    if (!facetElement) {
      console.log('Facet not found:', facetId);
      return;
    }

    // Extract numeric values from facet items
    const values = [];
    const inputs = facetElement.querySelectorAll('input[data-search-url]');

    inputs.forEach(function (input) {
      // Try to extract number from the label text
      const label = input.closest('label');
      if (label) {
        const link = label.querySelector('.js-search-url, a');
        const textContent = link
          ? link.textContent.trim()
          : label.textContent.trim();

        // Extract numeric value (handles decimals like 55.5)
        const match = textContent.match(/(\d+\.?\d*)/);
        if (match) {
          const numValue = parseFloat(match[1]);
          if (!isNaN(numValue)) {
            values.push(numValue);
          }
        }
      }
    });

    if (values.length === 0) {
      console.log('No numeric values found in facet:', facetId);
      return;
    }

    // Calculate min and max
    const min = Math.min(...values);
    const max = Math.max(...values);

    console.log('Initializing slider for', facetId, '- Range:', min, 'to', max);

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
          const link = label.querySelector('.js-search-url, a');
          const textContent = link
            ? link.textContent.trim()
            : label.textContent.trim();
          const match = textContent.match(/(\d+\.?\d*)/);

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
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      // Example: Initialize slider for facet_78268
      // You can add more facet IDs here
      initializeFacetSliders(['facet_78268']);
    });
  } else {
    // DOM already loaded
    initializeFacetSliders(['facet_78268']);
  }

  // Export for manual initialization if needed
  window.initFacetSlider = initializeFacetSlider;
  window.initFacetSliders = initializeFacetSliders;
})();
