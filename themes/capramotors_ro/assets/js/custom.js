(() => {
  function fmt(n) {
    return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
  }

  function buildSliders() {
    var numericFeatures = [
      { id: 3, unit: ' km' }, // extend later
    ];

    numericFeatures.forEach(function (feat) {
      var fid = feat.id;
      var unit = feat.unit;

      var $facet = $('aside.facet[data-feature-id="' + fid + '"]');
      if ($facet.length === 0) {
        return;
      }

      // stop if slider already inserted (avoid double build after AJAX)
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

      var sliderId = 'facet-slider-' + fid;
      var labelId = 'facet-slider-label-' + fid;

      var $label = $('<p>', {
        id: labelId,
        text: fmt(min) + unit + ' - ' + fmt(max) + unit,
        css: { margin: '6px 0' },
      });

      var $slider = $('<div>', {
        id: sliderId,
        css: { margin: '6px 0' },
      });

      var $header = $facet.find('h4.facet-title');
      $header.after($label);
      $label.after($slider);

      $('#' + sliderId).slider({
        range: true,
        min: min,
        max: max,
        step: Math.round((max - min) / 5),
        values: [min, max],
        slide: function (e, ui) {
          $('#' + labelId).text(
            fmt(ui.values[0]) + unit + ' - ' + fmt(ui.values[1]) + unit,
          );
          console.log('drag:', ui.values[0], ui.values[1]);
        },
        change: function (e, ui) {
          console.log('set:', ui.values[0], ui.values[1]);
          console.log('Later we emit updateFacets here');
        },
      });

      console.log('%c Slider ready for feature', 'color:green', fid, min, max);
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
