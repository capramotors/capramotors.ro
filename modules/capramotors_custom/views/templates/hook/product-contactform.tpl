{*
 * Product page contact form template
 * Uses the contactform elementor template
 *}
<div class="product-contact-form-wrapper elementor-contactform-wrapper">
    <h4 class="section-title">{l s='Contacteazza-ne pentru a afla mai multe despre acest anunt' d='Modules.Capramotorscustom.Shop'}</h4>

    {* Notification area above form for AJAX responses *}
    <div class="js-product-contact-notification-wrapper"></div>

    {include file='module:contactform/views/templates/widget/contactform-elementor.tpl'
        product_name=$product_name
        is_product_page=$is_product_page
        form_recipient='selection'
    }
</div>

<script>
(function() {
    var wrapper = document.querySelector('.product-contact-form-wrapper');

    if (!wrapper) {
        return;
    }

    var notificationWrapper = wrapper.querySelector('.js-product-contact-notification-wrapper');
    var errorMessage = '{l s="A intervenit o eroare, te rugam mai incerca." d="Modules.Contactform.Shop"|escape:"javascript"}';

    // Use event delegation - no document.ready needed
    wrapper.addEventListener('submit', function(e) {
        var form = e.target;

        // Only handle if it's the contact form
        if (!form || !form.classList.contains('js-elementor-contact-form')) {
            return;
        }

        e.preventDefault();

        var formData = new FormData(form);
        var originalNotification = wrapper.querySelector('.js-elementor-contact-norifcation-wrapper');

        fetch(form.getAttribute('action'), {
            method: 'POST',
            body: formData
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data && data.preview) {
                // Create temporary container to parse HTML
                var tempDiv = document.createElement('div');
                tempDiv.innerHTML = data.preview;

                // Extract notification from the response
                var notification = tempDiv.querySelector('.js-elementor-contact-norifcation-wrapper');

                if (notification && notificationWrapper) {
                    // Insert notification above the form
                    notificationWrapper.innerHTML = notification.innerHTML;

                    // Scroll to notification
                    notificationWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            } else {
                if (notificationWrapper) {
                    notificationWrapper.innerHTML =
                        '<div class="alert alert-danger">' + errorMessage + '</div>';
                }
            }
        })
        .catch(function(error) {
            if (notificationWrapper) {
                notificationWrapper.innerHTML =
                    '<div class="alert alert-danger">' + errorMessage + '</div>';
            }
        });
    });
})();
</script>


