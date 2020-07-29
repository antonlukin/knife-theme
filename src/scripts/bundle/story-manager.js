/**
 * Create story using Glide slider
 *
 * @since 1.3
 * @version 1.11
 */

(function () {
  var story = document.getElementById('story');


  /**
   * Check if Glide object exists
   */
  if (story === null || typeof Glide === 'undefined') {
    return false;
  }


  /**
   * Check slides existsing before story creation
   *
   * @link https://github.com/glidejs/glide/issues/224
   */
  if (story.querySelectorAll('.entry-story__slide').length < 1) {
    return false;
  }


  /**
   * Check if items exist
   */
  if (typeof knife_story_items === 'undefined' || knife_story_items.length < 1) {
    return false;
  }


  /*
   * Create Glide instance with custom options
   */
  var glide = new Glide('.entry-story', {
    gap: 0,
    rewind: false,
    touchAngle: 60,
    swipeThreshold: 60,
    dragThreshold: false,

    classes: {
      slider: 'entry-story--slider',
      swipeable: 'entry-story--swipeable',
      direction: {
        ltr: 'entry-story--ltr',
        rtl: 'entry-story--rtl'
      }
    },

    breakpoints: {
      767: {
        dragThreshold: true
      }
    }
  });


  /**
   * Declare global options object
   */
  var options = {}


  /**
   * Add slides
   */
  glide.on('mount.before', function () {
    for (var i = 0, item; item = knife_story_items[i]; i++) {
      var wrap = document.createElement('div');
      wrap.classList.add('entry-story__slide-wrap');

      var slide = document.createElement('div');
      slide.classList.add('entry-story__slide-content');

      // Append kicker
      (function () {
        if (typeof item.kicker === 'undefined') {
          return false;
        }

        var kicker = document.createElement('div');
        kicker.classList.add('entry-story__slide-kicker');
        kicker.innerHTML = item.kicker;

        wrap.appendChild(kicker);
      })();

      // Append media
      (function () {
        if (typeof item.image === 'undefined' || typeof item.ratio === 'undefined') {
          return false;
        }

        var image = document.createElement('div');
        image.classList.add('entry-story__slide-image');
        image.style.setProperty('background-image', 'url(' + item.image + ')');
        image.style.setProperty('--image-ratio', item.ratio);

        slide.appendChild(image);
      })();

      // Append entry
      (function () {
        if (typeof item.entry === 'undefined') {
          return false;
        }

        var entry = document.createElement('div');
        entry.classList.add('entry-story__slide-entry');
        entry.innerHTML = item.entry;

        slide.appendChild(entry);
      })();

      var block = document.createElement('div');
      block.classList.add('entry-story__slide');
      block.appendChild(wrap);

      wrap.appendChild(slide);

      story.querySelector('.entry-story__slides').appendChild(block);
    }
  });


  /**
   * Apend share buttons to the last slide
   */
  glide.on('mount.before', function () {
    var slides = story.querySelectorAll('.entry-story__slide');

    if (slides.length < 1) {
      return false;
    }

    var share = slides[0].querySelector('.share');

    if (share === null) {
      return false;
    }

    var clone = share.cloneNode(true);
    var slide = slides[slides.length - 1];

    slide.querySelector('.entry-story__slide-content').appendChild(clone);

    if (typeof window.shareButtons === 'function') {
      window.shareButtons();
    }
  });


  /**
   * Add bullets on Glide mounting
   */
  glide.on('mount.before', function () {
    var bullets = document.createElement('div');
    bullets.classList.add('entry-story__bullets');

    for (var i = 0; i < story.querySelectorAll('.entry-story__slide').length; i++) {
      var item = document.createElement('span');
      item.classList.add('entry-story__bullets-item');

      bullets.appendChild(item);
    }

    return story.appendChild(bullets);
  });


  /**
   * Create empty slide if next post availible
   */
  glide.on('mount.before', function (move) {
    var link = document.querySelector('link[rel="prev"]');

    if (link === null || !link.hasAttribute('href')) {
      return false;
    }

    options.href = link.href;

    var empty = document.createElement('div');
    empty.classList.add('entry-story__slide', 'entry-story__slide--empty');

    return story.querySelector('.entry-story__slides').appendChild(empty);
  });


  /**
   * Add slider controls on Glide mounting
   */
  glide.on('mount.before', function () {
    ['prev', 'next'].forEach(function (cl, i) {
      var control = document.createElement('div');
      control.classList.add('entry-story__control', 'entry-story__control--' + cl);
      control.classList.add();

      var icon = document.createElement('span');
      icon.classList.add('icon', 'icon--' + cl);
      control.appendChild(icon);

      story.appendChild(control);
    });

    story.querySelector('.entry-story__control--next').addEventListener('click', function (e) {
      e.preventDefault();

      glide.go('>');
    });

    story.querySelector('.entry-story__control--prev').addEventListener('click', function (e) {
      e.preventDefault();

      glide.go('<');
    });
  });


  /**
   * Set story height before slider mount
   */
  glide.on('mount.before', function () {
    var offset = story.getBoundingClientRect();
    story.style.height = window.innerHeight - offset.top - window.pageYOffset + 'px';
  });


  /**
   * Add bullets events
   */
  glide.on(['mount.after', 'run'], function () {
    var bullets = story.querySelectorAll('.entry-story__bullets-item');

    for (var i = 0, bullet; bullet = bullets[i]; i++) {
      bullet.classList.remove('entry-story__bullets-item--active');

      if (glide.index === i) {
        bullet.classList.add('entry-story__bullets-item--active');
      }
    }
  });


  /**
   * Add last slide index to options
   */
  glide.on('mount.after', function () {
    var slides = story.querySelectorAll('.entry-story__slide');
    options.count = slides.length - 1
  });


  /**
   * Manage prev slider control
   */
  glide.on(['mount.after', 'run'], function (move) {
    var prev = story.querySelector('.entry-story__control--prev');

    prev.classList.remove('entry-story__control--disabled');

    if (glide.index === 0) {
      prev.classList.add('entry-story__control--disabled');
    }
  });


  /**
   * Manage next slider control
   */
  glide.on('run', function (move) {
    var next = story.querySelector('.entry-story__control--next');

    next.classList.remove('entry-story__control--disabled');

    if (glide.index === options.count) {
      next.classList.add('entry-story__control--disabled');
    }
  });


  /**
   * Hide story if extra slide exists
   */
  glide.on('run', function (move) {
    if (glide.index === options.count && options.href) {
      story.classList.remove('entry-story--active');
    }
  });


  /**
   * Load next story if exists
   */
  glide.on('run.after', function (move) {
    if (glide.index === options.count && options.href) {
      document.location.href = options.href;
    }
  });


  /**
   * Disable touch bounce effect
   */
  glide.on('build.after', function () {
    var start = 0;

    story.addEventListener('touchstart', function (e) {
      var touch = e.changedTouches[0];

      start = touch.pageY;
    }, true);


    story.addEventListener('touchmove', function (e) {
      var touch = e.changedTouches[0];

      if (start < touch.pageY && window.pageYOffset === 0) {
        e.preventDefault();
      }

      if (story.classList.contains('entry-story--dragging')) {
        e.preventDefault();
      }
    }, true);
  });


  /**
   * Set background color
   */
  glide.on('build.after', function () {
    if (typeof knife_story_options.color === 'undefined' || knife_story_options.color.length < 1) {
      return false;
    }

    story.style.backgroundColor = knife_story_options.color;
  });



  /**
   * Set custom background
   */
  glide.on('build.after', function () {
    if (typeof knife_story_options.background === 'undefined' || knife_story_options.background.length < 1) {
      return story.classList.add('entry-story--active');
    }

    var image = new Image();
    image.addEventListener('load', function () {
      return story.classList.add('entry-story--active');
    });

    image.src = knife_story_options.background;

    var media = document.createElement('div');
    media.classList.add('entry-story__backdrop');
    media.style.backgroundImage = 'url(' + knife_story_options.background + ')';

    // Append blur element
    (function () {
      if (typeof knife_story_options.blur === 'undefined') {
        return false;
      }

      var blur = parseInt(knife_story_options.blur);

      if (blur <= 0) {
        return false;
      }

      // Add negative margins to blured backdrop
      // https://stackoverflow.com/a/12224347/
      media.style.margin = '-' + blur + 'px';

      // Add blur
      media.style.filter = 'blur(' + blur + 'px)';
    })();


    // Append shadow element
    (function () {
      if (typeof knife_story_options.shadow === 'undefined') {
        return false;
      }

      var alpha = parseInt(knife_story_options.shadow) / 100;

      if (alpha <= 0 || alpha > 1) {
        return false;
      }

      var shadow = document.createElement('div');
      shadow.classList.add('entry-story__shadow');
      shadow.style.backgroundColor = 'rgba(0, 0, 0, ' + alpha + ')';

      media.appendChild(shadow);
    })();


    return story.appendChild(media);
  });


  /**
   * Reload page if user go back with browser cache
   *
   * @link https://stackoverflow.com/a/13123626
   */
  window.addEventListener('pageshow', function (event) {
    if (event.persisted) {
      window.location.reload(false)
    }
  });


  /**
   * Let's rock!
   */
  return glide.mount();
})();