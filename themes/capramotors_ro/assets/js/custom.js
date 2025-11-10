(() => {
  function fmt(n) {
    return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
  }

  function buildSliders() {
    var numericFeatures = [
      { id: 3, unit: ' km', step: 100 },
      { id: 4, unit: '', step: 1 },
      { id: 7, unit: ' cc' },
      { id: 8, unit: ' cp' },
    ];

    numericFeatures.forEach(function (feat) {
      var fid = feat.id;
      var unit = feat.unit;

      var $facet = $('aside.facet[data-feature-id="' + fid + '"]');
      if ($facet.length === 0) {
        return;
      }

      if ($facet.data('numeric-slider-added')) {
        return;
      }
      $facet.data('numeric-slider-added', true);

      var values = [];

      $facet.find('a.js-search-link').each(function () {
        var raw = $(this)
          .contents()
          .filter(function () {
            return this.nodeType === 3;
          })
          .text()
          .trim();

        var clean = raw.replace(/[^\d]/g, '');
        var num = parseInt(clean, 10);

        if (!isNaN(num)) {
          values.push(num);
        }
      });

      if (values.length < 2) {
        return;
      }

      var min = Math.min.apply(Math, values);
      var max = Math.max.apply(Math, values);

      // Don't add slider if min equals max (too narrow filter)
      if (min === max) {
        return;
      }

      // Check for active facet values
      var activeValues = [];
      $facet.find('label.facet-label.active').each(function () {
        var $link = $(this).find('a.js-search-link');
        if ($link.length > 0) {
          var value = Number($link.data('value'));
          if (!isNaN(value)) {
            activeValues.push(value);
          }
        }
      });

      // Set initial slider values based on active facets, or use full range
      var initialMin = min;
      var initialMax = max;
      if (activeValues.length > 0) {
        initialMin = Math.min.apply(Math, activeValues);
        initialMax = Math.max.apply(Math, activeValues);
      }

      var sliderId = 'facet-slider-' + fid;
      var labelId = 'facet-slider-label-' + fid;

      var $label = $('<p>', {
        id: labelId,
        text: fmt(initialMin) + unit + ' - ' + fmt(initialMax) + unit,
      });

      var $slider = $('<div>', {
        id: sliderId,
        class: 'cp-experimental-slider',
      });

      var $header = $facet.find('h4.facet-title');
      $header.after($label);
      $label.after($slider);

      var $facetUl = $slider.next('ul.facet-type-checkbox').hide();

      $('#' + sliderId).slider({
        range: true,
        min: min,
        max: max,
        step: feat.step || 5,
        values: [initialMin, initialMax],
        slide: function (e, ui) {
          $('#' + labelId).text(
            fmt(ui.values[0]) + unit + ' - ' + fmt(ui.values[1]) + unit,
          );
        },
        change: function (e, ui) {
          var filterValues = [];

          $facetUl.find('a.js-search-link').each(function () {
            var value = Number($(this).data('value'));

            if (value >= ui.values[0] && value <= ui.values[1]) {
              filterValues.push(value);
            }
          });

          var url = new URL(window.location);
          var l10nName = ($facet.data('l10n-name') || '')
            .trim()
            .replace(/\s+/g, '+');
          var segments = url.searchParams.get('q')
            ? url.searchParams.get('q').split('/')
            : [];
          // Filter out segments matching this facet, handling URL encoding (including parentheses %28, %29)
          segments = segments.filter((s) => {
            try {
              var decoded = decodeURIComponent(s);
              return !decoded.startsWith(l10nName + '-');
            } catch (e) {
              // If decoding fails, check original string
              return !s.startsWith(l10nName + '-');
            }
          });
          if (filterValues.length > 0) {
            segments.push(l10nName + '-' + filterValues.join('-'));
          }
          // Build query string manually to preserve slashes (not encode them as %2F)
          var qValue = segments.join('/');
          var queryParams = [];
          var qAdded = false;
          url.searchParams.forEach(function (value, key) {
            if (key === 'q') {
              queryParams.push('q=' + qValue);
              qAdded = true;
            } else {
              queryParams.push(key + '=' + encodeURIComponent(value));
            }
          });
          if (qValue && !qAdded) {
            queryParams.push('q=' + qValue);
          }
          var queryString = queryParams.join('&');
          if (typeof prestashop !== 'undefined' && prestashop.emit) {
            prestashop.emit(
              'updateFacets',
              url.pathname + (queryString ? '?' + queryString : ''),
            );
          }
        },
      });
    });
  }

  $(document).ready(function () {
    buildSliders();
  });

  $(document).ajaxComplete(function (event, xhr, settings) {
    if (xhr.responseJSON && xhr.responseJSON?.rendered_facets?.length > 0) {
      buildSliders();
    }
  });
})();
