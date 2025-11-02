<?php
/**
 * Blog for PrestaShop module by PrestaHome Team.
 *
 * @author    PrestaHome Team <support@prestahome.com>
 * @copyright Copyright (c) 2011-2021 PrestaHome Team - www.PrestaHome.com
 * @license   You only can use module, nothing more!
 */
require_once _PS_MODULE_DIR_ . 'ph_simpleblog/ph_simpleblog.php';

class AdminSimpleBlogCommentsController extends ModuleAdminController
{
    public $is_16;

    public function __construct()
    {
        $this->table = 'simpleblog_comment';
        $this->className = 'SimpleBlogComment';

        $this->bootstrap = true;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->is_16 = (bool) (version_compare(_PS_VERSION_, '1.6.0', '>=') === true);

        parent::__construct();

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->module->getTranslator()->trans('Delete selected', [], 'Modules.Phsimpleblog.Admin'),
                'confirm' => $this->module->getTranslator()->trans('Delete selected items?', [], 'Modules.Phsimpleblog.Admin'),
            ],
            'enableSelection' => ['text' => $this->module->getTranslator()->trans('Enable selection', [], 'Modules.Phsimpleblog.Admin')],
            'disableSelection' => ['text' => $this->module->getTranslator()->trans('Disable selection', [], 'Modules.Phsimpleblog.Admin')],
        ];

        $this->_select = 'sbpl.title AS `post_title`';

        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'simpleblog_post_lang` sbpl ON (sbpl.`id_simpleblog_post` = a.`id_simpleblog_post` AND sbpl.`id_lang` = ' . (int) Context::getContext()->language->id . ')';

        $this->fields_list = [
            'id_simpleblog_comment' => [
                'title' => $this->module->getTranslator()->trans('ID', [], 'Modules.Phsimpleblog.Admin'),
                'type' => 'int',
                'align' => 'center',
                'width' => 25,
            ],
            'id_simpleblog_post' => [
                'title' => $this->module->getTranslator()->trans('Post ID', [], 'Modules.Phsimpleblog.Admin'),
                'type' => 'int',
                'align' => 'center',
                'width' => 25,
            ],
            'post_title' => [
                'title' => $this->module->getTranslator()->trans('Comment for', [], 'Modules.Phsimpleblog.Admin'),
                'width' => 'auto',
            ],
            'name' => [
                'title' => $this->module->getTranslator()->trans('Name', [], 'Modules.Phsimpleblog.Admin'),
            ],
            'email' => [
                'title' => $this->module->getTranslator()->trans('E-mail', [], 'Modules.Phsimpleblog.Admin'),
            ],
            'comment' => [
                'title' => $this->module->getTranslator()->trans('Comment', [], 'Modules.Phsimpleblog.Admin'),
                'width' => 'auto',
            ],
            'active' => [
                'title' => $this->module->getTranslator()->trans('Status', [], 'Modules.Phsimpleblog.Admin'),
                'width' => 70,
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
            ],
        ];
    }

    public function renderForm()
    {
        $id_lang = $this->context->language->id;
        $obj = $this->loadObject(true);

        $this->fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->module->getTranslator()->trans('Comment', [], 'Modules.Phsimpleblog.Admin'),
            ],
            'input' => [
                [
                    'type' => 'hidden',
                    'name' => 'id_simpleblog_post',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'id_customer',
                    'label' => $this->module->getTranslator()->trans('Customer', [], 'Modules.Phsimpleblog.Admin'),
                ],
                [
                    'type' => 'text',
                    'name' => 'id_simpleblog_post',
                    'label' => $this->module->getTranslator()->trans('Post ID', [], 'Modules.Phsimpleblog.Admin'),
                ],
                [
                    'type' => 'text',
                    'name' => 'name',
                    'label' => $this->module->getTranslator()->trans('Name', [], 'Modules.Phsimpleblog.Admin'),
                    'required' => false,
                    'lang' => false,
                ],
                [
                    'type' => 'text',
                    'name' => 'email',
                    'label' => $this->module->getTranslator()->trans('E-mail', [], 'Modules.Phsimpleblog.Admin'),
                    'required' => false,
                    'lang' => false,
                ],
                [
                    'type' => 'text',
                    'name' => 'ip',
                    'label' => $this->module->getTranslator()->trans('IP Address', [], 'Modules.Phsimpleblog.Admin'),
                    'required' => false,
                    'lang' => false,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->module->getTranslator()->trans('Comment', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'comment',
                    'cols' => 75,
                    'rows' => 7,
                    'required' => false,
                    'lang' => false,
                ],
                [
                    'type' => 'switch',
                    'label' => $this->module->getTranslator()->trans('Displayed', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->getTranslator()->trans('Enabled', [], 'Modules.Phsimpleblog.Admin'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->getTranslator()->trans('Disabled', [], 'Modules.Phsimpleblog.Admin'),
                        ],
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->module->getTranslator()->trans('Save', [], 'Modules.Phsimpleblog.Admin'),
                'name' => 'savePostComment',
            ],
        ];

        $this->multiple_fieldsets = true;

        $SimpleBlogPost = new SimpleBlogPost($obj->id_simpleblog_post, $id_lang);

        $this->tpl_form_vars = [
            'customerLink' => $this->context->link->getAdminLink('AdminCustomers'),
            'blogPostLink' => $this->context->link->getAdminLink('AdminSimpleBlogPost'),
            'blogPostName' => $SimpleBlogPost->meta_title,
        ];

        return parent::renderForm();
    }
}
