/*!
 * jQuery UI Touch Punch 0.2.2
 *
 * Copyright 2011, Dave Furfero
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Depends:
 *  jquery.ui.widget.js
 *  jquery.ui.mouse.js
 */
(function (b) {
  b.support.touch = 'ontouchend' in document;
  if (!b.support.touch) {
    return;
  }
  var c = b.ui.mouse.prototype,
    e = c._mouseInit,
    a;
  function d(g, h) {
    if (g.originalEvent.touches.length > 1) {
      return;
    }
    g.preventDefault();
    var i = g.originalEvent.changedTouches[0],
      f = document.createEvent('MouseEvents');
    f.initMouseEvent(
      h,
      true,
      true,
      window,
      1,
      i.screenX,
      i.screenY,
      i.clientX,
      i.clientY,
      false,
      false,
      false,
      false,
      0,
      null,
    );
    g.target.dispatchEvent(f);
  }
  c._touchStart = function (g) {
    var f = this;
    if (a || !f._mouseCapture(g.originalEvent.changedTouches[0])) {
      return;
    }
    a = true;
    f._touchMoved = false;
    d(g, 'mouseover');
    d(g, 'mousemove');
    d(g, 'mousedown');
  };
  c._touchMove = function (f) {
    if (!a) {
      return;
    }
    this._touchMoved = true;
    d(f, 'mousemove');
  };
  c._touchEnd = function (f) {
    if (!a) {
      return;
    }
    d(f, 'mouseup');
    d(f, 'mouseout');
    if (!this._touchMoved) {
      d(f, 'click');
    }
    a = false;
  };
  c._mouseInit = function () {
    var f = this;
    f.element
      .bind('touchstart', b.proxy(f, '_touchStart'))
      .bind('touchmove', b.proxy(f, '_touchMove'))
      .bind('touchend', b.proxy(f, '_touchEnd'));
    e.call(f);
  };
})(jQuery);

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
      });

      var $slider = $('<div>', {
        id: sliderId,
        class: 'cp-experimental-slider',
      });

      var $header = $facet.find('h4.facet-title');
      $header.after($label);
      $label.after($slider);

      $('#' + sliderId).slider({
        range: true,
        min: min,
        max: max,
        step: 5,
        values: [min, max],
        slide: function (e, ui) {
          $('#' + labelId).text(
            fmt(ui.values[0]) + unit + ' - ' + fmt(ui.values[1]) + unit,
          );
          console.log('drag:', ui.values[0], ui.values[1]);
        },
        change: function (e, ui) {},
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
