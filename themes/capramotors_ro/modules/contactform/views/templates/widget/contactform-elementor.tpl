
    <div class="contact-form">
        <form  class="js-elementor-contact-form"  action="{url entity='module' name='iqitelementor' controller='Actions' params=['process' => 'handleWidget', 'ajax' => 1, 'form_recipient' => $form_recipient]}" method="post"
              {if $contact.allow_file_upload}enctype="multipart/form-data"{/if}>

            <div class="js-elementor-contact-norifcation-wrapper">
            {if $notifications}
                <div class="col-xs-12 alert {if  isset($notifications.nw_error) && $notifications.nw_error}alert-danger{else}alert-success{/if}">
                    <ul>
                        {foreach $notifications.messages as $notif}
                            <li>{$notif}</li>
                        {/foreach}
                    </ul>
                </div>
            {/if}
            </div>
            <section class="form-fields">

                {if $form_recipient == 'selection'}
                <div class="form-group d-none">
                    <label class="form-control-label">{l s='Subject' d='Shop.Forms.Labels'}</label>
                    <div class="custom-select2">
                        <select name="id_contact" class="form-control form-control-select">
                            {foreach from=$contact.contacts item=contact_elt}
                                <option value="{$contact_elt.id_contact}" {if isset($cms) && $cms.id == 10 && $contact_elt.id_contact == 3}selected{/if}>{$contact_elt.name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                    {else}
                    <input type="hidden" name="id_contact" value="{$form_recipient}" />
                {/if}

                {* Add product name if on product page *}
                {if isset($is_product_page) && $is_product_page && isset($product_name) && !empty($product_name)}
                    <div class="form-group">
                        <label class="form-control-label">{l s='Product' d='Shop.Forms.Labels'}</label>
                        <input type="hidden" name="product_name" class="form-control" value="{$product_name|escape:'html':'UTF-8'}" readonly />
                    </div>
                {/if}

                <div class="form-group">
                    <label class="form-control-label">{l s='Email address' d='Shop.Forms.Labels'}</label>
                        <input
                                class="form-control"
                                name="from"
                                type="email"
                                value="{if isset($from)}{$from}{/if}"
                                placeholder="{l s='your@email.com' d='Shop.Forms.Help'}"
                        >
                </div>

                <div class="form-group">
                    <label class="form-control-label">{l s='Phone number' d='Shop.Forms.Labels'}</label>
                        <input
                                class="form-control"
                                name="phone"
                                type="tel"
                                value="{if isset($contact.phone)}{$contact.phone}{/if}"
                                placeholder="{l s='Your phone number' d='Shop.Forms.Help'}"
                                required
                        >
                </div>

                {* Buyback fields - only show on CMS page 9 (Buyback page) *}
                {if isset($cms) && $cms.id == 9}
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-control-label">{l s='Brand' d='Shop.Forms.Labels'}</label>
                            <input
                                    class="form-control"
                                    name="buyback_brand"
                                    type="text"
                                    value="{if isset($smarty.post.buyback_brand)}{$smarty.post.buyback_brand}{/if}"
                                    placeholder="{l s='Brand' d='Shop.Forms.Help'}"
                            >
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-control-label">{l s='Model' d='Shop.Forms.Labels'}</label>
                            <input
                                    class="form-control"
                                    name="buyback_model"
                                    type="text"
                                    value="{if isset($smarty.post.buyback_model)}{$smarty.post.buyback_model}{/if}"
                                    placeholder="{l s='Model' d='Shop.Forms.Help'}"
                            >
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label class="form-control-label">{l s='Year' d='Shop.Forms.Labels'}</label>
                            <select name="buyback_year" class="form-control form-control-select">
                                <option value="">{l s='-- Select year --' d='Shop.Forms.Labels'}</option>
                                {assign var="current_year" value=$smarty.now|date_format:"%Y"}
                                {assign var="start_year" value=1920}
                                {section name=year loop=200}
                                    {assign var="year_value" value=$current_year-$smarty.section.year.index}
                                    {if $year_value >= $start_year}
                                        <option value="{$year_value}" {if isset($smarty.post.buyback_year) && $smarty.post.buyback_year == $year_value}selected{/if}>{$year_value}</option>
                                    {/if}
                                {/section}
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-control-label">{l s='Cubic centimeters' d='Shop.Forms.Labels'}</label>
                            <input
                                    class="form-control"
                                    name="buyback_cc"
                                    type="text"
                                    value="{if isset($smarty.post.buyback_cc)}{$smarty.post.buyback_cc}{/if}"
                                    placeholder="{l s='cc' d='Shop.Forms.Help'}"
                            >
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-control-label">{l s='Kilometers' d='Shop.Forms.Labels'}</label>
                            <input
                                    class="form-control"
                                    name="buyback_km"
                                    type="text"
                                    value="{if isset($smarty.post.buyback_km)}{$smarty.post.buyback_km}{/if}"
                                    placeholder="{l s='km' d='Shop.Forms.Help'}"
                            >
                        </div>
                    </div>

                    {if $contact.allow_file_upload}
                        <div class="form-group elementor-attachment-field">
                            <label class="form-control-label">{l s='Attachment' d='Shop.Forms.Labels'}</label>
                                <input type="file" name="fileUpload" class="filestyle" data-buttonText="{l s='Choose file' d='Shop.Theme.Actions'}">
                        </div>
                    {/if}
                {/if}



                <div class="form-group">
                    <label class="form-control-label">{l s='Message' d='Shop.Forms.Labels'}</label>

          <textarea
                  class="form-control"
                  name="message"
                  placeholder="{l s='How can we help?' d='Shop.Forms.Help'}"
                  rows="3"
          >{if $contact.message}{$contact.message}{/if}</textarea>

                </div>
                {if isset($id_module)}
                <div class="form-group ">
                    {hook h='displayGDPRConsent' id_module=$id_module}
                </div>
                {/if}
            </section>

            <footer class="form-footer {if $btn_align == 'center'}text-center {/if} {if $btn_align == 'right'}text-right {/if}">
                <style>
                    input[name=url] {
                        display: none !important;
                    }
                </style>
                <input type="text" name="url" value=""/>

                <div class="js-csfr-token">
                    <input type="hidden" name="token" value="{$token}" />
                </div>

                <input type="hidden" name="submitMessage" value="1" />


                <input class="btn btn-primary btn-elementor-send {$btn_size} {if $btn_align == 'justify'} btn-block{/if}" type="submit"
                       value="{l s='Send' d='Shop.Theme.Actions'}">
            </footer>

        </form>
    </div>
