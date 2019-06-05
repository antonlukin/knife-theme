/**
 * Random generator post type handler
 *
 * @since 1.6
 * @version 1.8
 */

(function() {
  var generator = document.getElementById('generator');


  /**
   * Check if generator object exists
   */
  if(generator === null || typeof knife_generator_options === 'undefined') {
    return false;
  }


  /**
   * Set generator color options
   */
  (function() {
    // Set background color
    if(typeof knife_generator_options.page_background !== 'undefined') {
      generator.style.backgroundColor = knife_generator_options.page_background;
    }

    // Set text color
    if(typeof knife_generator_options.page_color !== 'undefined') {
      generator.style.color = knife_generator_options.page_color;
    }
  })();


  /**
   * Check if items exist
   */
  if(typeof knife_generator_items === 'undefined' || knife_generator_items.length < 1) {
    return false;
  }


  /**
   * Start button element
   */
  var button = null;


  /**
   * Replace share links
   */
  function replaceShare(heading, index) {
    // Check generator share links
    if(typeof knife_generator_options.share_links === 'undefined') {
      return false;
    }

    // Check generator permalink
    if(typeof knife_generator_options.permalink === 'undefined') {
      return false;
    }

    var links = generator.querySelectorAll('.share > .share__link');

    for(var i = 0, link; link = links[i]; i++) {
      var label = link.getAttribute('data-label');

      // Strip twitter heading
      if(label === 'twitter' && heading.length > 200) {
        heading = heading.substring(0, 200) + '…';
      }

      var matches = [
        knife_generator_options.permalink.replace(/\/?$/, '/') + index + '/', heading
      ];

      if(typeof knife_generator_options.share_links[label] === 'undefined') {
        continue;
      }

      var options = knife_generator_options.share_links[label];

      link.href = options.link.replace(/%([\d])\$s/g, function(match, i) {
        return encodeURIComponent(matches[i - 1]);
      });
    }

    if(window.shareButtons === 'function') {
      window.shareButtons();
    }
  }


  /**
   * Load poster
   */
  function loadPoster(item) {
  }


  /**
   * Create loader
   */
  (function() {
    var loader = document.createElement('div');
    loader.classList.add('entry-generator__loader');
    generator.insertBefore(loader, generator.firstChild);

    var bounce = document.createElement('span');
    bounce.classList.add('entry-generator__loader-bounce');
    loader.appendChild(bounce);
  })();


  /**
   * Create generator button
   */
  (function() {
    if(typeof knife_generator_options.button_text === 'undefined') {
      return false;
    }

    // Create button outer element
    var wrapper = document.createElement('div');
    wrapper.classList.add('entry-generator__button');
    generator.appendChild(wrapper);

    // Create button button
    button = document.createElement('button');
    button.classList.add('button');
    button.setAttribute('type', 'button');
    button.textContent = knife_generator_options.button_text;
    wrapper.appendChild(button);

    // Set button color
    if(typeof knife_generator_options.button_background !== 'undefined') {
      button.style.backgroundColor = knife_generator_options.button_background;
    }

    // Set button text color
    if(typeof knife_generator_options.button_color !== 'undefined') {
      button.style.color = knife_generator_options.button_color;
    }
  })();


  /**
   * Generate button click
   */
  button.addEventListener('click', function(e) {
    e.preventDefault();

    var rand = Math.floor(Math.random() * knife_generator_items.length);
    var item = knife_generator_items[rand];

    // Update generator repeat button text
    if(typeof knife_generator_options.button_repeat !== 'undefined') {
      button.textContent = knife_generator_options.button_repeat;
    }

    // Update generator share buttons
    if(generator.querySelector('.entry-generator__share')) {
      var text = item.heading || '';

      if(knife_generator_options.fulltext !== 'undefined' && knife_generator_options.fulltext) {
        var span = document.createElement('span');
        span.innerHTML = ' ' + item.description || '';
        text = text + span.textContent;
      }

      replaceShare(text, rand + 1);
    }

    var content = generator.querySelector('.entry-generator__content');

    if(content === null) {
      var content = document.createElement('div');
      content.classList.add('entry-generator__content');
      generator.insertBefore(content, generator.lastChild);
    }

    content.innerHTML = item.description || '';

    // Add generator loader class
    generator.classList.add('entry-generator--loader');


    // Don't show poster for blank items
    if(knife_generator_options.blank === 'undefined' || !knife_generator_options.blank) {
      var poster = generator.querySelector('.entry-generator__poster');

      if(poster !== null) {
        poster.parentNode.removeChild(poster);
      }

      if(typeof item.poster !== 'undefined') {
        poster = new Image();

        poster.classList.add('entry-generator__poster');
        generator.insertBefore(poster, generator.firstChild);

        poster.addEventListener('load', function() {
          setTimeout(function() {
            generator.classList.remove('entry-generator--loader');
          }, 600);
        });

        poster.setAttribute('alt', item.heading || '');
        poster.setAttribute('src', item.poster);

        return generator.classList.add('entry-generator--poster');
      }
    }

    // Show heading if blank settings or no poster
    var heading = generator.querySelector('.entry-generator__title');

    if(heading === null) {
      heading = document.createElement('div');
      heading.classList.add('entry-generator__title');

      generator.insertBefore(heading, generator.firstChild);
    }

    heading.innerHTML = item.heading || '';

    setTimeout(function() {
      generator.classList.remove('entry-generator--loader');
    }, 600);


    return generator.classList.add('entry-generator--blank');
  });
})();