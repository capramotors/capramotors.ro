<?php
/**
 * Blog for PrestaShop module by Krystian Podemski from PrestaHome.
 *
 * @author    Krystian Podemski <krystian@prestahome.com>
 * @copyright Copyright (c) 2008-2020 Krystian Podemski - www.PrestaHome.com / www.Podemski.info
 * @license   You only can use module, nothing more!
 */
require_once _PS_MODULE_DIR_ . 'ph_simpleblog/ph_simpleblog.php';

class AdminSimpleBlogAuthorsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'simpleblog_author';
        $this->className = 'SimpleBlogPostAuthor';
        $this->lang = true;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->bootstrap = true;

        parent::__construct();

        $this->bulk_actions = ['delete' => ['text' => $this->module->getTranslator()->trans('Delete selected', [], 'Modules.Phsimpleblog.Admin'), 'confirm' => $this->module->getTranslator()->trans('Delete selected items?', [], 'Modules.Phsimpleblog.Admin')]];

        $this->_select = 'IFNULL(sbp.posts, 0) as number_of_posts';
        $this->_join = 'LEFT JOIN (SELECT id_simpleblog_author, COUNT(`id_simpleblog_post`) as posts FROM ' . _DB_PREFIX_ . 'simpleblog_post GROUP BY id_simpleblog_author) sbp ON a.id_simpleblog_author = sbp.id_simpleblog_author';

        $this->fields_list = [
            'id_simpleblog_author' => [
                'title' => $this->module->getTranslator()->trans('ID', [], 'Modules.Phsimpleblog.Admin'),
                'align' => 'center',
                'width' => 30,
            ],
            'firstname' => [
                'title' => $this->module->getTranslator()->trans('Firstname', [], 'Modules.Phsimpleblog.Admin'),
                'width' => 'auto',
            ],
            'lastname' => [
                'title' => $this->module->getTranslator()->trans('Lastname', [], 'Modules.Phsimpleblog.Admin'),
                'width' => 'auto',
            ],
            'email' => [
                'title' => $this->module->getTranslator()->trans('E-mail', [], 'Modules.Phsimpleblog.Admin'),
                'width' => 'auto',
            ],
            'number_of_posts' => [
                'title' => $this->module->getTranslator()->trans('Posts', [], 'Modules.Phsimpleblog.Admin'),
                'width' => 'auto',
            ],
            'active' => [
                'title' => $this->module->getTranslator()->trans('Active', [], 'Modules.Phsimpleblog.Admin'),
                'width' => 25,
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false,
            ],
        ];
    }

    public function initFormToolBar()
    {
        unset($this->toolbar_btn['back']);
        $this->toolbar_btn['save-and-stay'] = [
            'short' => 'SaveAndStay',
            'href' => '#',
            'desc' => $this->module->getTranslator()->trans('Save and stay', [], 'Modules.Phsimpleblog.Admin'),
        ];
        $this->toolbar_btn['back'] = [
            'href' => self::$currentIndex . '&token=' . Tools::getValue('token'),
            'desc' => $this->module->getTranslator()->trans('Back to list', [], 'Modules.Phsimpleblog.Admin'),
        ];
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();

        $this->addJS([
            _MODULE_DIR_ . 'ph_simpleblog/js/admin.js',
        ]);

        Media::addJsDef([
            'PS_ALLOW_ACCENTED_CHARS_URL' => Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL')
        ]);
    }

    public function renderForm()
    {
        $this->initFormToolBar();
        if (!$this->loadObject(true)) {
            return;
        }

        $obj = $this->loadObject(true);

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->getTranslator()->trans('Author', [], 'Modules.Phsimpleblog.Admin'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->module->getTranslator()->trans('Firstname:', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'firstname',
                    'lang' => false,
                ],

                [
                    'type' => 'text',
                    'label' => $this->module->getTranslator()->trans('Lastname:', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'lastname',
                    'lang' => false,
                ],

                [
                    'type' => 'select_image',
                    'label' => $this->module->getTranslator()->trans('Photo:', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'photo',
                    'lang' => false,
                    'desc' => $this->module->getTranslator()->trans('Module will not crop your photo, it is recommended to use something around 400x400 px', [], 'Modules.Phsimpleblog.Admin'),
                ],

                [
                    'type' => 'textarea',
                    'label' => $this->module->getTranslator()->trans('Bio:', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'bio',
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 40,
                    'autoload_rte' => true,
                ],

                // [
                //     'type' => 'textarea',
                //     'label' => $this->module->getTranslator()->trans('Additional info:', [], 'Modules.Phsimpleblog.Admin'),
                //     'name' => 'additional_info',
                //     'lang' => true,
                //     'rows' => 5,
                //     'cols' => 40,
                //     'autoload_rte' => true,
                // ],

                [
                    'type' => 'text',
                    'label' => $this->module->getTranslator()->trans('E-mail:', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'email',
                    'lang' => false,
                ],

                // [
                //     'type' => 'text',
                //     'label' => $this->module->getTranslator()->trans('Phone:', [], 'Modules.Phsimpleblog.Admin'),
                //     'name' => 'phone',
                //     'lang' => false,
                // ],

                [
                    'type' => 'text',
                    'label' => $this->module->getTranslator()->trans('Facebook:', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'facebook',
                    'lang' => false,
                ],

                [
                    'type' => 'text',
                    'label' => $this->module->getTranslator()->trans('Instagram:', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'instagram',
                    'lang' => false,
                ],

                [
                    'type' => 'text',
                    'label' => $this->module->getTranslator()->trans('Twitter:', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'twitter',
                    'lang' => false,
                ],

                // [
                //     'type' => 'text',
                //     'label' => $this->module->getTranslator()->trans('Google:', [], 'Modules.Phsimpleblog.Admin'),
                //     'name' => 'google',
                //     'lang' => false,
                // ],

                [
                    'type' => 'text',
                    'label' => $this->module->getTranslator()->trans('LinkedIn:', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'linkedin',
                    'lang' => false,
                ],

                // [
                //     'type' => 'text',
                //     'label' => $this->module->getTranslator()->trans('WWW:', [], 'Modules.Phsimpleblog.Admin'),
                //     'name' => 'www',
                //     'lang' => false,
                // ],

                [
                    'type' => 'text',
                    'label' => $this->module->getTranslator()->trans('Friendly URL:', [], 'Modules.Phsimpleblog.Admin'),
                    'desc' => $this->module->getTranslator()->trans('for example: firstname-lastname or author-nickname', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'link_rewrite',
                    'required' => true,
                    'lang' => false,
                    'class' => 'use-str2url',
                ],

                [
                    'type' => 'switch',
                    'label' => $this->module->getTranslator()->trans('Active', [], 'Modules.Phsimpleblog.Admin'),
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
            ],
        ];

        return parent::renderForm();
    }
}
