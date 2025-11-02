<?php
/**
 * Blog for PrestaShop module by PrestaHome Team.
 *
 * @author    PrestaHome Team <support@prestahome.com>
 * @copyright Copyright (c) 2011-2021 PrestaHome Team - www.PrestaHome.com
 * @license   You only can use module, nothing more!
 */
require_once _PS_MODULE_DIR_ . 'ph_simpleblog/ph_simpleblog.php';

class AdminSimpleBlogCategoriesController extends ModuleAdminController
{
    protected $position_identifier = 'id_simpleblog_category';

    public function __construct()
    {
        $this->table = 'simpleblog_category';
        $this->className = 'SimpleBlogCategory';
        $this->lang = true;
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->bootstrap = true;

        $this->_where = 'AND a.`id_parent` = 0';
        $this->_orderBy = 'position';

        parent::__construct();

        $this->bulk_actions = ['delete' => ['text' => $this->module->getTranslator()->trans('Delete selected', [], 'Modules.Phsimpleblog.Admin'), 'confirm' => $this->module->getTranslator()->trans('Delete selected items?', [], 'Modules.Phsimpleblog.Admin')]];

        $this->fields_list = [
            'id_simpleblog_category' => [
                'title' => $this->module->getTranslator()->trans('ID', [], 'Modules.Phsimpleblog.Admin'),
                'align' => 'center',
                'width' => 30,
            ],
            'name' => [
                'title' => $this->module->getTranslator()->trans('Name', [], 'Modules.Phsimpleblog.Admin'),
                'width' => 'auto',
            ],
            'description' => [
                'title' => $this->module->getTranslator()->trans('Description', [], 'Modules.Phsimpleblog.Admin'),
                'width' => 500,
                'orderby' => false,
                'callback' => 'getDescriptionClean',
            ],
            'active' => [
                'title' => $this->module->getTranslator()->trans('Displayed', [], 'Modules.Phsimpleblog.Admin'),
                'width' => 25,
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false,
            ],
            'position' => [
                'title' => $this->module->getTranslator()->trans('Position', [], 'Modules.Phsimpleblog.Admin'),
                'width' => 40,
                'filter_key' => 'a!position',
                'position' => 'position',
            ],
        ];
    }

    public function renderList()
    {
        $this->initToolbar();
        $this->addRowAction('details');

        return parent::renderList();
    }

    public function getDescriptionClean($description, $row)
    {
        return strip_tags(stripslashes($description));
    }

    public function init()
    {
        parent::init();

        Shop::addTableAssociation($this->table, ['type' => 'shop']);

        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'simpleblog_category_shop` sa ON (a.`id_simpleblog_category` = sa.`id_simpleblog_category` AND sa.id_shop = ' . (int) $this->context->shop->id . ') ';
        }
        // else
        //     $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'simpleblog_category_shop` sa ON (a.`simpleblog_category` = sa.`simpleblog_category` AND sa.id_shop = a.id_shop_default) ';

        if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
            $this->_where = ' AND sa.`id_shop` = ' . (int) Context::getContext()->shop->id;
        }

        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            unset($this->fields_list['position']);
        }
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

    public function renderForm()
    {
        $this->initFormToolBar();
        if (!$this->loadObject(true)) {
            return;
        }

        $cover = false;

        $obj = $this->loadObject(true);

        if (isset($obj->id)) {
            $this->display = 'edit';

            $cover = ImageManager::thumbnail(_PS_MODULE_DIR_ . 'ph_simpleblog/covers_cat/' . $obj->id . '.' . $obj->cover, 'ph_simpleblog_cat_' . $obj->id . '.' . $obj->cover, 350, $obj->cover, false);
        } else {
            $this->display = 'add';
        }

        $this->fields_value = [
            'cover' => $cover ? $cover : false,
            'cover_size' => $cover ? filesize(_PS_MODULE_DIR_ . 'ph_simpleblog/covers_cat/' . $obj->id . '.' . $obj->cover) / 1000 : false,
        ];

        $categories = SimpleBlogCategory::getCategories($this->context->language->id, true, true);

        array_unshift($categories, ['id' => 0, 'name' => $this->module->getTranslator()->trans('No parent', [], 'Modules.Phsimpleblog.Admin')]);

        foreach ($categories as $key => $category) {
            if (isset($obj->id) && $obj->id) {
                if ($category['id'] == $obj->id_simpleblog_category) {
                    unset($category[$key]);
                }
            }
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->getTranslator()->trans('Category', [], 'Modules.Phsimpleblog.Admin'),
                'image' => '../img/admin/tab-categories.gif',
            ],
            'input' => [
                [
                    'type' => 'select',
                    'label' => $this->module->getTranslator()->trans('Parent Category:', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'id_parent',
                    'required' => true,
                    'options' => [
                        'id' => 'id',
                        'query' => $categories,
                        'name' => 'name',
                    ],
                ],

                [
                    'type' => 'text',
                    'label' => $this->module->getTranslator()->trans('Name:', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'name',
                    'required' => true,
                    'lang' => true,
                    'class' => 'copy2friendlyUrl',
                ],

                [
                    'type' => 'textarea',
                    'label' => $this->module->getTranslator()->trans('Description:', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'description',
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 40,
                    'autoload_rte' => true,
                ],

                [
                    'type' => 'text',
                    'label' => $this->module->getTranslator()->trans('Meta title:', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'meta_title',
                    'lang' => true,
                ],

                [
                    'type' => 'text',
                    'label' => $this->module->getTranslator()->trans('Meta description:', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'meta_description',
                    'lang' => true,
                ],

                [
                    'type' => 'text',
                    'label' => $this->module->getTranslator()->trans('Meta keywords:', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'meta_keywords',
                    'lang' => true,
                ],

                [
                    'type' => 'text',
                    'label' => $this->module->getTranslator()->trans('Friendly URL:', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'link_rewrite',
                    'required' => true,
                    'lang' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->module->getTranslator()->trans('Type canonical tag', [], 'Modules.Phsimpleblog.Admin'),
                    'name' => 'canonical',
                    'hint' => $this->module->getTranslator()->trans('Leave empty if you want to left original post URL', [], 'Modules.Phsimpleblog.Admin'),
                    'lang' => true,
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

                [
                    'type' => 'file',
                    'label' => $this->module->getTranslator()->trans('Category image:', [], 'Modules.Phsimpleblog.Admin'),
                    'display_image' => true,
                    'name' => 'cover',
                    'desc' => $this->module->getTranslator()->trans('Upload a image from your computer.', [], 'Modules.Phsimpleblog.Admin'),
                ],
            ],
            'submit' => [
                'title' => $this->module->getTranslator()->trans('Save', [], 'Modules.Phsimpleblog.Admin'),
            ],
        ];

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->module->getTranslator()->trans('Shop association:', [], 'Modules.Phsimpleblog.Admin'),
                'name' => 'checkBoxShopAsso',
            ];
        }

        $this->tpl_form_vars['PS_ALLOW_ACCENTED_CHARS_URL'] = (int) Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
        $this->tpl_form_vars['PS_FORCE_FRIENDLY_PRODUCT'] = (int) Configuration::get('PS_FORCE_FRIENDLY_PRODUCT');

        return parent::renderForm();
    }

    /**
    "Details" view for PrestaShop 1.6

     **/
    public function renderDetails()
    {
        if (($id = Tools::getValue('id_simpleblog_category'))) {
            $this->lang = false;
            $this->list_id = 'details';
            $this->initFormToolBar();
            $category = $this->loadObject($id);
            $this->toolbar_title = $this->module->getTranslator()->trans('Subcategories of:', [], 'Modules.Phsimpleblog.Admin') . ' ' . $category->name[$this->context->employee->id_lang];

            unset($this->toolbar_btn['save-and-stay']);
            unset($this->toolbar_btn['new']);
            if ($this->display == 'details' || $this->display == 'add' || $this->display == 'edit') {
                $this->toolbar_btn['new'] = [
                    'href' => Context::getContext()->link->getAdminLink('AdminSimpleBlogCategories') . '&addsimpleblog_category&id_parent=' . $category->id,
                    'desc' => $this->module->getTranslator()->trans('Add new', [], 'Modules.Phsimpleblog.Admin'),
                ];
                $this->toolbar_btn['back'] = [
                    'href' => Context::getContext()->link->getAdminLink('AdminSimpleBlogCategories'),
                    'desc' => $this->module->getTranslator()->trans('Back to list', [], 'Modules.Phsimpleblog.Admin'),
                ];
            }

            $this->_select = 'b.*';
            $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'simpleblog_category_lang` b ON (b.`id_simpleblog_category` = a.`id_simpleblog_category` AND b.`id_lang` = ' . $this->context->language->id . ')';
            $this->_where = 'AND a.`id_parent` = ' . (int) $id;
            $this->_orderBy = 'position';

            $this->fields_list = [
                'id_simpleblog_category' => [
                    'title' => $this->module->getTranslator()->trans('ID', [], 'Modules.Phsimpleblog.Admin'),
                    'align' => 'center',
                    'class' => 'fixed-width-xs',
                ],

                'name' => [
                    'title' => $this->module->getTranslator()->trans('Name', [], 'Modules.Phsimpleblog.Admin'),
                    'width' => 'auto',
                    'filter_key' => 'b!name',
                ],

                'active' => [
                    'title' => $this->module->getTranslator()->trans('Enabled', [], 'Modules.Phsimpleblog.Admin'),
                    'class' => 'fixed-width-xs',
                    'align' => 'center',
                    'active' => 'status',
                    'type' => 'bool',
                    'orderby' => false,
                ],

                'position' => [
                    'title' => $this->module->getTranslator()->trans('Position', [], 'Modules.Phsimpleblog.Admin'),
                    'class' => 'fixed-width-md',
                    'filter_key' => 'a!position',
                    'position' => 'position',
                ],
            ];

            self::$currentIndex = self::$currentIndex . '&details' . $this->table;
            $this->processFilter();

            return parent::renderList();
        }
    }

    public function processDelete()
    {
        if ($this->tabAccess['delete'] == '1') {
            if (SimpleBlogCategory::getNbCats() == 1) {
                $this->errors[] = $this->module->getTranslator()->trans('You cannot remove this category because this is last category already used by module.', [], 'Modules.Phsimpleblog.Admin');
            } else {
                return parent::processDelete();
            }
        } else {
            $this->errors[] = Tools::displayError('You do not have permission to delete this.');
        }

        return false;
    }

    public function processAdd()
    {
        $object = parent::processAdd();

        // Cover
        if (isset($_FILES['cover']) && $_FILES['cover']['size'] > 0) {
            $object->cover = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
            $object->update();
        }

        if (!empty($object->cover)) {
            $this->createCover($_FILES['cover']['tmp_name'], $object);
        }

        $this->updateAssoShop($object->id);

        return true;
    }

    public function processUpdate()
    {
        $object = parent::processUpdate();

        // Cover
        if (isset($_FILES['cover']) && $_FILES['cover']['size'] > 0) {
            $object->cover = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
            $object->update();
        }

        if (!empty($object->cover) && isset($_FILES['cover']) && $_FILES['cover']['size'] > 0) {
            $this->createCover($_FILES['cover']['tmp_name'], $object);
        }

        $this->updateAssoShop($object->id);

        return true;
    }

    public function postProcess()
    {
        if (Tools::isSubmit('viewsimpleblog_category')
            && ($id_simpleblog_category = (int) Tools::getValue('id_simpleblog_category'))
            && ($SimpleBlogCategory = new SimpleBlogCategory($id_simpleblog_category, (int) $this->context->language->id))
            && Validate::isLoadedObject($SimpleBlogCategory)) {
            Tools::redirectAdmin($SimpleBlogCategory->getObjectLink((int) $this->context->language->id));
        }

        if (Tools::isSubmit('deleteCover')) {
            $this->deleteCover((int) Tools::getValue('id_simpleblog_category'));
        }

        if (($id_simpleblog_category = (int) Tools::getValue('id_simpleblog_category'))
            && ($direction = Tools::getValue('move'))
            && Validate::isLoadedObject($SimpleBlogCategory = new SimpleBlogCategory($id_simpleblog_category))) {
            if ($SimpleBlogCategory->move($direction)) {
                Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
            }
        } elseif (Tools::getValue('position') && !Tools::isSubmit('submitAdd' . $this->table)) {
            if ($this->tabAccess['edit'] !== '1') {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            } elseif (!Validate::isLoadedObject($object = new SimpleBlogCategory((int) Tools::getValue($this->identifier)))) {
                $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.') .
                    ' <b>' . $this->table . '</b> ' . Tools::displayError('(cannot load object)');
            }
            if (!$object->updatePosition((int) Tools::getValue('way'), (int) Tools::getValue('position'))) {
                $this->errors[] = Tools::displayError('Failed to update the position.');
            } else {
                Tools::redirectAdmin(self::$currentIndex . '&conf=5&token=' . Tools::getAdminTokenLite('AdminSimpleBlogCategories'));
            }
        } else {
            // Temporary add the position depend of the selection of the parent category
            if (!Tools::isSubmit('id_simpleblog_category')) {
                $_POST['position'] = SimpleBlogCategory::getNbCats(Tools::getValue('id_parent'));
            }
        }

        return parent::postProcess();
    }

    public function ajaxProcessUpdatePositions()
    {
        $way = (int) (Tools::getValue('way'));
        $id_simpleblog_category = (int) (Tools::getValue('id'));
        $positions = Tools::getValue('simpleblog_category');

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            $id_simpleblog_category = (int) $pos[2];

            if ((int) $id_simpleblog_category > 0) {
                if ($SimpleBlogCategory = new SimpleBlogCategory($id_simpleblog_category)) {
                    $SimpleBlogCategory->position = $position + 1;
                    if ($SimpleBlogCategory->update()) {
                        echo 'ok position ' . (int) $position . ' for category ' . (int) $SimpleBlogCategory->id . '\r\n';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This category (' . (int) $id_simpleblog_category . ') cant be loaded"}';
                }
            }
        }
    }

    public function createCover($img = null, $object = null)
    {
        if (!isset($img)) {
            die('AdminSimpleBlogCategoryController@createCover: No image to process');
        }

        $categoryImgX = Configuration::get('PH_CATEGORY_IMAGE_X');
        $categoryImgY = Configuration::get('PH_CATEGORY_IMAGE_Y');

        if (isset($object) && Validate::isLoadedObject($object)) {
            $fileTmpLoc = $img;
            $origPath = _PS_MODULE_DIR_ . 'ph_simpleblog/covers_cat/' . $object->id . '.' . $object->cover;

            $tmp_location = _PS_TMP_IMG_DIR_ . 'ph_simpleblog_cat_' . $object->id . '.' . $object->cover;
            if (file_exists($tmp_location)) {
                @unlink($tmp_location);
            }

            try {
                $orig = PhpThumbFactory::create($fileTmpLoc);
            } catch (Exception $e) {
                echo $e;
            }

            $orig->adaptiveResize($categoryImgX, $categoryImgY);

            return $orig->save($origPath) && ImageManager::thumbnail(_PS_MODULE_DIR_ . 'ph_simpleblog/covers_cat/' . $object->id . '.' . $object->cover, 'ph_simpleblog_cat_' . $object->id . '.' . $object->cover, 350, $object->cover);
        }
    }

    public function deleteCover($id)
    {
        $object = new SimpleBlogCategory($id, Context::getContext()->language->id);

        $tmp_location = _PS_TMP_IMG_DIR_ . 'ph_simpleblog_cat_' . $object->id . '.' . $object->cover;
        if (file_exists($tmp_location)) {
            @unlink($tmp_location);
        }

        $orig_location = _PS_MODULE_DIR_ . 'ph_simpleblog/covers_cat/' . $object->id . '.' . $object->cover;

        if (file_exists($orig_location)) {
            @unlink($orig_location);
        }

        $object->cover = null;
        $object->update();

        Tools::redirectAdmin(self::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminSimpleBlogCategories') . '&conf=7');
    }
}
