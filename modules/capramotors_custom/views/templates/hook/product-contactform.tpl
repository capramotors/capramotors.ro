{*
 * Product page contact form template
 * Uses the contactform elementor template
 *}
<div class="product-contact-form-wrapper">
    <h4 class="section-title">{l s='Contact us to find out information about this listing' d='Modules.Capramotorscustom.Shop'}</h4>
    {include file='module:contactform/views/templates/widget/contactform-elementor.tpl' product_name=$product_name is_product_page=$is_product_page}
</div>


