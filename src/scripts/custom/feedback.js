(function () {
  /**
   * Check if custom options defined
   */
  if (typeof knife_theme_custom === 'undefined') {
    return false;
  }

  let content = document.querySelector('.entry-content');

  // Check if entry-content exists
  if (content === null) {
    return false;
  }

  // Check required ajaxurl
  if (typeof knife_theme_custom.ajaxurl === 'undefined') {
    return false;
  }


  /**
   * Helper to create DOM element
   */
  const buildElement = (tag, options) => {
    let element = document.createElement(tag);

    // Set single class
    if (options.hasOwnProperty('class')) {
      element.classList.add(options.class);
    }

    // Set class list
    if (options.hasOwnProperty('classes')) {
      options.classes.forEach(cl => {
        element.classList.add(cl);
      });
    }

    // Set textContent
    if (options.hasOwnProperty('text')) {
      element.textContent = options.text;
    }

    // Set innerHTML
    if (options.hasOwnProperty('html')) {
      element.innerHTML = options.html;
    }

    // Set attributes
    if (options.hasOwnProperty('attributes')) {
      for (let key in options.attributes) {
        element.setAttribute(key, options.attributes[key]);
      }
    }

    // Append child
    if (options.hasOwnProperty('parent')) {
      options.parent.appendChild(element);
    }

    return element;
  }


  /**
   * Draw formats figure
   */
  const drawFormats = (formats) => {
    let figure = content.querySelector('.figure--formats');

    // Create list
    let list = buildElement('ul', {
      'parent': figure
    });

    for (let key in formats) {
      let item = buildElement('li', {
        'parent': list,
        'text': formats[key],
      });

      item.addEventListener('click', (e) => {
        e.preventDefault();

        if (item.hasAttribute('data-selected')) {
          return item.removeAttribute('data-selected', true);
        }

        item.setAttribute('data-selected', true);
      });
    }

    return list;
  }


  /**
   * Draw brief figure
   */
  const drawBrief = (brief, list) => {
    let figure = content.querySelector('.figure--brief');

    // Create form
    let form = buildElement('form', {
      'parent': figure
    });

    for (let key in brief) {
      buildElement('input', {
        'parent': form,
        'attributes': {
          'name': key,
          'type': 'text',
          'required': 'required',
          'placeholder': brief[key]
        }
      });
    }

    let submit = buildElement('button', {
      'parent': form,
      'text': knife_theme_custom.button,
      'attributes': {
        'type': 'submit'
      }
    });

    form.addEventListener('submit', (e) => {
      e.preventDefault();

      let data = {
        'nonce': knife_theme_custom.nonce,
        'time': knife_theme_custom.time,
        'fields': [],
        'formats': []
      };

      // Try to collect all formats
      let items = list.querySelectorAll('li');

      items.forEach(item => {
        if (item.hasAttribute('data-selected')) {
          data.formats.push(item.textContent);
        }
      });

      let inputs = form.querySelectorAll('input');

      inputs.forEach(input => {
        data.fields.push({
          'label': input.getAttribute('placeholder'),
          'value': input.value
        })
      });

      // Disable button
      submit.setAttribute('disabled', 'disabled');

      // Set button loader
      submit.setAttribute('data-loading', true);

      // Send request
      let request = new XMLHttpRequest();
      request.open('POST', knife_theme_custom.ajaxurl + '/brief');
      request.setRequestHeader('Content-Type', 'application/json');

      // Check if loaded
      request.onload = function() {
        submit.removeAttribute('data-loading');

        if (request.status !== 200) {
          return submit.textContent = knife_theme_custom.error;
        }

        submit.textContent = knife_theme_custom.success;

        inputs.forEach(input => {
          input.value = '';
        });

        items.forEach(item => {
          item.removeAttribute('data-selected');
        })
      }

      request.onerror = function() {
        submit.removeAttribute('data-loading');

        // Show error on button
        submit.textContent = knife_theme_custom.error;
      }

      request.send(JSON.stringify(data));
    });
  }


  /**
   * Draw feedback figure
   */
  const drawFeedback = (feedback) => {
    let figure = content.querySelector('.figure--feedback');

    // Create form
    let form = buildElement('form', {
      'parent': figure
    });

    let input = buildElement('input', {
      'parent': form,
      'attributes': {
        'type': 'email',
        'placeholder': feedback.email,
        'required': 'required'
      }
    });

    let submit = buildElement('button', {
      'parent': form,
      'attributes': {
        'type': 'submit'
      },
      'text': feedback.button
    });

    // Submit event
    form.addEventListener('submit', (e) => {
      e.preventDefault();

      let data = {
        'nonce': knife_theme_custom.nonce,
        'time': knife_theme_custom.time,
        'email': input.value
      };

      // Disable button
      submit.setAttribute('disabled', 'disabled');

      // Set button loader
      submit.setAttribute('data-loading', true);

      // Send request
      let request = new XMLHttpRequest();
      request.open('POST', knife_theme_custom.ajaxurl + '/feedback');
      request.setRequestHeader('Content-Type', 'application/json');

      // Check if loaded
      request.onload = function() {
        submit.removeAttribute('data-loading');

        if (request.status !== 200) {
          return submit.textContent = knife_theme_custom.error;
        }

        submit.textContent = knife_theme_custom.success;
        input.value = '';
      }

      request.onerror = function() {
        submit.removeAttribute('data-loading');

        // Show error on button
        submit.textContent = knife_theme_custom.error;
      }

      request.send(JSON.stringify(data));
    });
  }


  // Draw formats
  let list = drawFormats(knife_theme_custom.figure.formats);

  // Draw brief figure
  drawBrief(knife_theme_custom.figure.brief, list);

  // Draw feedback
  drawFeedback(knife_theme_custom.figure.feedback);
})();