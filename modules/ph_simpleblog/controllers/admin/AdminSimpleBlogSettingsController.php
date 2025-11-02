<?php
/**
 * Blog for PrestaShop module by Krystian Podemski from PrestaHome.
 *
 * @author    Krystian Podemski <krystian@prestahome.com>
 * @copyright Copyright (c) 2008-2020 Krystian Podemski - www.PrestaHome.com / www.Podemski.info
 * @license   You only can use module, nothing more!
 */
require_once _PS_MODULE_DIR_ . 'ph_simpleblog/ph_simpleblog.php';

class AdminSimpleBlogSettingsController extends ModuleAdminController
{
    public $is_16;
    public $is_17;

    public function __construct()
    {
        parent::__construct();

        $this->bootstrap = true;

        $this->initOptions();

        $this->is_16 = (version_compare(_PS_VERSION_, '1.6.0', '>=') === true && version_compare(_PS_VERSION_, '1.7.0', '<') === true) ? true : false;
        $this->is_17 = (version_compare(_PS_VERSION_, '1.7.0', '>=') === true) ? true : false;
    }

    public function initOptions()
    {
        $this->optionTitle = $this->module->getTranslator()->trans('Settings', [], 'Modules.Phsimpleblog.Admin');

        $blogCategories = SimpleBlogCategory::getCategories($this->context->language->id);

        $simpleBlogCategories = [];

        $simpleBlogCategories[0] = $this->module->getTranslator()->trans('All categories', [], 'Modules.Phsimpleblog.Admin');
        $simpleBlogCategories[9999] = $this->module->getTranslator()->trans('Featured only', [], 'Modules.Phsimpleblog.Admin');

        foreach ($blogCategories as $key => $category) {
            $simpleBlogCategories[$category['id']] = $category['name'];
        }

        $relatedPosts = [];

        if (Module::isInstalled('ph_relatedposts')) {
            $relatedPosts = [
                'related_posts' => [
                    'submit' => ['title' => $this->module->getTranslator()->trans('Update', [], 'Modules.Phsimpleblog.Admin'), 'class' => 'button'],
                    'title' => $this->module->getTranslator()->trans('Related Posts widget settings', [], 'Modules.Phsimpleblog.Admin'),
                    'image' => '../img/t/AdminOrderPreferences.gif',
                    'fields' => [
                        'PH_RELATEDPOSTS_GRID_COLUMNS' => [
                            'title' => $this->module->getTranslator()->trans('Grid columns:', [], 'Modules.Phsimpleblog.Admin'),
                            'cast' => 'intval',
                            'desc' => $this->module->getTranslator()->trans('Working only with "Recent Posts layout:" setup to "Grid"', [], 'Modules.Phsimpleblog.Admin'),
                            'show' => true,
                            'required' => true,
                            'type' => 'radio',
                            'choices' => [
                                '2' => $this->module->getTranslator()->trans('2 columns', [], 'Modules.Phsimpleblog.Admin'),
                                '3' => $this->module->getTranslator()->trans('3 columns', [], 'Modules.Phsimpleblog.Admin'),
                                '4' => $this->module->getTranslator()->trans('4 columns', [], 'Modules.Phsimpleblog.Admin'),
                            ],
                        ], // PH_RELATEDPOSTS_GRID_COLUMNS
                    ],
                ],
            ];
        }

        $timezones = [
            'Pacific/Midway' => '(GMT-11:00) Midway Island',
            'US/Samoa' => '(GMT-11:00) Samoa',
            'US/Hawaii' => '(GMT-10:00) Hawaii',
            'US/Alaska' => '(GMT-09:00) Alaska',
            'US/Pacific' => '(GMT-08:00) Pacific Time (US &amp; Canada)',
            'America/Tijuana' => '(GMT-08:00) Tijuana',
            'US/Arizona' => '(GMT-07:00) Arizona',
            'US/Mountain' => '(GMT-07:00) Mountain Time (US &amp; Canada)',
            'America/Chihuahua' => '(GMT-07:00) Chihuahua',
            'America/Mazatlan' => '(GMT-07:00) Mazatlan',
            'America/Mexico_City' => '(GMT-06:00) Mexico City',
            'America/Monterrey' => '(GMT-06:00) Monterrey',
            'Canada/Saskatchewan' => '(GMT-06:00) Saskatchewan',
            'US/Central' => '(GMT-06:00) Central Time (US &amp; Canada)',
            'US/Eastern' => '(GMT-05:00) Eastern Time (US &amp; Canada)',
            'US/East-Indiana' => '(GMT-05:00) Indiana (East)',
            'America/Bogota' => '(GMT-05:00) Bogota',
            'America/Lima' => '(GMT-05:00) Lima',
            'America/Caracas' => '(GMT-04:30) Caracas',
            'Canada/Atlantic' => '(GMT-04:00) Atlantic Time (Canada)',
            'America/La_Paz' => '(GMT-04:00) La Paz',
            'America/Santiago' => '(GMT-04:00) Santiago',
            'Canada/Newfoundland' => '(GMT-03:30) Newfoundland',
            'America/Buenos_Aires' => '(GMT-03:00) Buenos Aires',
            'Greenland' => '(GMT-03:00) Greenland',
            'Atlantic/Stanley' => '(GMT-02:00) Stanley',
            'Atlantic/Azores' => '(GMT-01:00) Azores',
            'Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde Is.',
            'Africa/Casablanca' => '(GMT) Casablanca',
            'Europe/Dublin' => '(GMT) Dublin',
            'Europe/Lisbon' => '(GMT) Lisbon',
            'Europe/London' => '(GMT) London',
            'Africa/Monrovia' => '(GMT) Monrovia',
            'Europe/Amsterdam' => '(GMT+01:00) Amsterdam',
            'Europe/Belgrade' => '(GMT+01:00) Belgrade',
            'Europe/Berlin' => '(GMT+01:00) Berlin',
            'Europe/Bratislava' => '(GMT+01:00) Bratislava',
            'Europe/Brussels' => '(GMT+01:00) Brussels',
            'Europe/Budapest' => '(GMT+01:00) Budapest',
            'Europe/Copenhagen' => '(GMT+01:00) Copenhagen',
            'Europe/Ljubljana' => '(GMT+01:00) Ljubljana',
            'Europe/Madrid' => '(GMT+01:00) Madrid',
            'Europe/Paris' => '(GMT+01:00) Paris',
            'Europe/Prague' => '(GMT+01:00) Prague',
            'Europe/Rome' => '(GMT+01:00) Rome',
            'Europe/Sarajevo' => '(GMT+01:00) Sarajevo',
            'Europe/Skopje' => '(GMT+01:00) Skopje',
            'Europe/Stockholm' => '(GMT+01:00) Stockholm',
            'Europe/Vienna' => '(GMT+01:00) Vienna',
            'Europe/Warsaw' => '(GMT+01:00) Warsaw',
            'Europe/Zagreb' => '(GMT+01:00) Zagreb',
            'Europe/Athens' => '(GMT+02:00) Athens',
            'Europe/Bucharest' => '(GMT+02:00) Bucharest',
            'Africa/Cairo' => '(GMT+02:00) Cairo',
            'Africa/Harare' => '(GMT+02:00) Harare',
            'Europe/Helsinki' => '(GMT+02:00) Helsinki',
            'Europe/Istanbul' => '(GMT+02:00) Istanbul',
            'Asia/Jerusalem' => '(GMT+02:00) Jerusalem',
            'Europe/Kiev' => '(GMT+02:00) Kyiv',
            'Europe/Minsk' => '(GMT+02:00) Minsk',
            'Europe/Riga' => '(GMT+02:00) Riga',
            'Europe/Sofia' => '(GMT+02:00) Sofia',
            'Europe/Tallinn' => '(GMT+02:00) Tallinn',
            'Europe/Vilnius' => '(GMT+02:00) Vilnius',
            'Asia/Baghdad' => '(GMT+03:00) Baghdad',
            'Asia/Kuwait' => '(GMT+03:00) Kuwait',
            'Africa/Nairobi' => '(GMT+03:00) Nairobi',
            'Asia/Riyadh' => '(GMT+03:00) Riyadh',
            'Asia/Tehran' => '(GMT+03:30) Tehran',
            'Europe/Moscow' => '(GMT+04:00) Moscow',
            'Asia/Baku' => '(GMT+04:00) Baku',
            'Europe/Volgograd' => '(GMT+04:00) Volgograd',
            'Asia/Muscat' => '(GMT+04:00) Muscat',
            'Asia/Tbilisi' => '(GMT+04:00) Tbilisi',
            'Asia/Yerevan' => '(GMT+04:00) Yerevan',
            'Asia/Kabul' => '(GMT+04:30) Kabul',
            'Asia/Karachi' => '(GMT+05:00) Karachi',
            'Asia/Tashkent' => '(GMT+05:00) Tashkent',
            'Asia/Kolkata' => '(GMT+05:30) Kolkata',
            'Asia/Kathmandu' => '(GMT+05:45) Kathmandu',
            'Asia/Yekaterinburg' => '(GMT+06:00) Ekaterinburg',
            'Asia/Almaty' => '(GMT+06:00) Almaty',
            'Asia/Dhaka' => '(GMT+06:00) Dhaka',
            'Asia/Novosibirsk' => '(GMT+07:00) Novosibirsk',
            'Asia/Bangkok' => '(GMT+07:00) Bangkok',
            'Asia/Jakarta' => '(GMT+07:00) Jakarta',
            'Asia/Krasnoyarsk' => '(GMT+08:00) Krasnoyarsk',
            'Asia/Chongqing' => '(GMT+08:00) Chongqing',
            'Asia/Hong_Kong' => '(GMT+08:00) Hong Kong',
            'Asia/Kuala_Lumpur' => '(GMT+08:00) Kuala Lumpur',
            'Australia/Perth' => '(GMT+08:00) Perth',
            'Asia/Singapore' => '(GMT+08:00) Singapore',
            'Asia/Taipei' => '(GMT+08:00) Taipei',
            'Asia/Ulaanbaatar' => '(GMT+08:00) Ulaan Bataar',
            'Asia/Urumqi' => '(GMT+08:00) Urumqi',
            'Asia/Irkutsk' => '(GMT+09:00) Irkutsk',
            'Asia/Seoul' => '(GMT+09:00) Seoul',
            'Asia/Tokyo' => '(GMT+09:00) Tokyo',
            'Australia/Adelaide' => '(GMT+09:30) Adelaide',
            'Australia/Darwin' => '(GMT+09:30) Darwin',
            'Asia/Yakutsk' => '(GMT+10:00) Yakutsk',
            'Australia/Brisbane' => '(GMT+10:00) Brisbane',
            'Australia/Canberra' => '(GMT+10:00) Canberra',
            'Pacific/Guam' => '(GMT+10:00) Guam',
            'Australia/Hobart' => '(GMT+10:00) Hobart',
            'Australia/Melbourne' => '(GMT+10:00) Melbourne',
            'Pacific/Port_Moresby' => '(GMT+10:00) Port Moresby',
            'Australia/Sydney' => '(GMT+10:00) Sydney',
            'Asia/Vladivostok' => '(GMT+11:00) Vladivostok',
            'Asia/Magadan' => '(GMT+12:00) Magadan',
            'Pacific/Auckland' => '(GMT+12:00) Auckland',
            'Pacific/Fiji' => '(GMT+12:00) Fiji',
        ];

        $timezones_select = [];

        foreach ($timezones as $value => $name) {
            $timezones_select[] = ['id' => $value, 'name' => $name];
        }

        $pre_settings_content = '<button type="submit" name="regenerateThumbnails" class="button btn btn-default"><i class="process-icon-cogs"></i>' . $this->module->getTranslator()->trans('Regenerate thumbnails', [], 'Modules.Phsimpleblog.Admin') . '</button>&nbsp;';
        $pre_settings_content .= '<button type="submit" name="submitExportSettings" class="button btn btn-default"><i class="process-icon-export"></i>' . $this->module->getTranslator()->trans('Export settings', [], 'Modules.Phsimpleblog.Admin') . '</button>&nbsp;';
        $pre_settings_content .= '<br /><br />';

        $standard_options = [
            'general' => [
                'title' => $this->module->getTranslator()->trans('Blog for PrestaShop - Settings', [], 'Modules.Phsimpleblog.Admin'),
                'info' => $pre_settings_content,
                'fields' => [
                    'PH_BLOG_TIMEZONE' => [
                        'title' => $this->module->getTranslator()->trans('Timezone:', [], 'Modules.Phsimpleblog.Admin'),
                        'desc' => $this->module->getTranslator()->trans('If you want to use future post publication date you need to setup your timezone', [], 'Modules.Phsimpleblog.Admin'),
                        'type' => 'select',
                        'list' => $timezones_select,
                        'identifier' => 'id',
                        'required' => true,
                        'validation' => 'isGenericName',
                    ], // PH_BLOG_TIMEZONE

                    'PH_BLOG_POSTS_PER_PAGE' => [
                        'title' => $this->module->getTranslator()->trans('Posts per page:', [], 'Modules.Phsimpleblog.Admin'),
                        'cast' => 'intval',
                        'desc' => $this->module->getTranslator()->trans('Number of blog posts displayed per page. Default is 10.', [], 'Modules.Phsimpleblog.Admin'),
                        'type' => 'text',
                        'required' => true,
                        'validation' => 'isUnsignedId',
                    ], // PH_BLOG_POSTS_PER_PAGE

                    'PH_BLOG_SLUG' => [
                        'title' => $this->module->getTranslator()->trans('Blog main URL (by default: blog)', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isGenericName',
                        'required' => true,
                        'type' => 'text',
                        'size' => 40,
                    ], // PH_BLOG_SLUG

                    'PH_BLOG_MAIN_TITLE' => [
                        'title' => $this->module->getTranslator()->trans('Blog title:', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isGenericName',
                        'type' => 'textLang',
                        'size' => 40,
                        'desc' => $this->module->getTranslator()->trans('Meta Title for blog homepage', [], 'Modules.Phsimpleblog.Admin'),
                    ], // PH_BLOG_MAIN_TITLE

                    'PH_BLOG_MAIN_META_DESCRIPTION' => [
                        'title' => $this->module->getTranslator()->trans('Blog description:', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isGenericName',
                        'type' => 'textLang',
                        'size' => 75,
                        'desc' => $this->module->getTranslator()->trans('Meta Description for blog homepage', [], 'Modules.Phsimpleblog.Admin'),
                    ], // PH_BLOG_MAIN_META_DESCRIPTION

                    'PH_BLOG_DATEFORMAT' => [
                        'title' => $this->module->getTranslator()->trans('Blog default date format:', [], 'Modules.Phsimpleblog.Admin'),
                        'desc' => $this->module->getTranslator()->trans('More details: https://www.smarty.net/docsv2/en/language.modifier.date.format.tpl', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isGenericName',
                        'type' => 'text',
                        'size' => 40,
                    ], // PH_BLOG_DATEFORMAT

                    'PH_CATEGORY_SORTBY' => [
                        'title' => $this->module->getTranslator()->trans('Sort categories by:', [], 'Modules.Phsimpleblog.Admin'),
                        'desc' => $this->module->getTranslator()->trans('Select which method use to sort categories in SimpleBlog Categories Block', [], 'Modules.Phsimpleblog.Admin'),
                        'show' => true,
                        'required' => true,
                        'type' => 'radio',
                        'choices' => [
                            'position' => $this->module->getTranslator()->trans('Position (1-9)', [], 'Modules.Phsimpleblog.Admin'),
                            'name' => $this->module->getTranslator()->trans('Name (A-Z)', [], 'Modules.Phsimpleblog.Admin'),
                            'id' => $this->module->getTranslator()->trans('ID (1-9)', [], 'Modules.Phsimpleblog.Admin'),
                        ],
                    ], // PH_CATEGORY_SORTBY

                    'PH_BLOG_FB_INIT' => [
                        'title' => $this->module->getTranslator()->trans('Init Facebook?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'desc' => $this->module->getTranslator()->trans('If you already use some Facebook widgets in your theme please select option to "No". If you select "Yes" then SimpleBlog will add facebook connect script on single post page.', [], 'Modules.Phsimpleblog.Admin'),
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_FB_INIT

                    // @todo - 2.0.0
                    // 'PH_BLOG_LOAD_FA' => array(
                    //     'title' => $this->module->getTranslator()->trans('Load FontAwesome?', [], 'Modules.Phsimpleblog.Admin'),
                    //     'validation' => 'isBool',
                    //     'cast' => 'intval',
                    //     'desc' => $this->module->getTranslator()->trans('If you already use FontAwesome in your theme please select option to "No".', [], 'Modules.Phsimpleblog.Admin'),
                    //     'required' => true,
                    //     'type' => 'bool'
                    // ), // PH_BLOG_LOAD_FA
                ],
                'submit' => ['title' => $this->module->getTranslator()->trans('Update', [], 'Modules.Phsimpleblog.Admin'), 'class' => 'button'],
            ],

            'layout' => [
                'submit' => ['title' => $this->module->getTranslator()->trans('Update', [], 'Modules.Phsimpleblog.Admin'), 'class' => 'button'],
                'title' => $this->module->getTranslator()->trans('Appearance Settings - General', [], 'Modules.Phsimpleblog.Admin'),
                'fields' => [
                    'PH_BLOG_DISPLAY_BREADCRUMBS' => [
                        'title' => $this->module->getTranslator()->trans('Display breadcrumbs in center-column?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'desc' => $this->module->getTranslator()->trans('Sometimes you want to remove breadcrumbs from center-column. Option for 1.6 only', [], 'Modules.Phsimpleblog.Admin'),
                        'required' => true,
                        'type' => 'bool',
                        'class' => '',
                    ], // PH_BLOG_DISPLAY_BREADCRUMBS

                    'PH_BLOG_LIST_LAYOUT' => [
                        'title' => $this->module->getTranslator()->trans('Posts list layout:', [], 'Modules.Phsimpleblog.Admin'),
                        'show' => true,
                        'required' => true,
                        'type' => 'radio',
                        'choices' => [
                            'full' => $this->module->getTranslator()->trans('Full width with large images', [], 'Modules.Phsimpleblog.Admin'),
                            'grid' => $this->module->getTranslator()->trans('Grid', [], 'Modules.Phsimpleblog.Admin'),
                        ],
                    ], // PH_BLOG_LIST_LAYOUT

                    'PH_BLOG_GRID_COLUMNS' => [
                        'title' => $this->module->getTranslator()->trans('Grid columns:', [], 'Modules.Phsimpleblog.Admin'),
                        'cast' => 'intval',
                        'desc' => $this->module->getTranslator()->trans('Working only with "Posts list layout" setup to "Grid"', [], 'Modules.Phsimpleblog.Admin'),
                        'show' => true,
                        'required' => true,
                        'type' => 'radio',
                        'choices' => [
                            '2' => $this->module->getTranslator()->trans('2 columns', [], 'Modules.Phsimpleblog.Admin'),
                            '3' => $this->module->getTranslator()->trans('3 columns', [], 'Modules.Phsimpleblog.Admin'),
                            '4' => $this->module->getTranslator()->trans('4 columns', [], 'Modules.Phsimpleblog.Admin'),
                        ],
                    ], // PH_BLOG_GRID_COLUMNS

                    'PH_BLOG_MASONRY_LAYOUT' => array(
                        'title' => $this->module->getTranslator()->trans('Use Masonry layout?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'desc' => $this->module->getTranslator()->trans('You can use masonry layout if you use Grid as a post list layout', [], 'Modules.Phsimpleblog.Admin'),
                        'type' => 'bool',
                    ), // PH_BLOG_MASONRY_LAYOUT

                    'PH_BLOG_CSS' => [
                        'title' => $this->module->getTranslator()->trans('Custom CSS', [], 'Modules.Phsimpleblog.Admin'),
                        'show' => true,
                        'required' => false,
                        'type' => 'textarea',
                        'cols' => '70',
                        'rows' => '10',
                    ], // PH_BLOG_CSS
                ],
            ],

            'single_post' => [
                'submit' => ['title' => $this->module->getTranslator()->trans('Update', [], 'Modules.Phsimpleblog.Admin'), 'class' => 'button'],
                'title' => $this->module->getTranslator()->trans('Appearance Settings - Single post', [], 'Modules.Phsimpleblog.Admin'),
                'fields' => [
                    'PH_BLOG_DISPLAY_LIKES' => [
                        'title' => $this->module->getTranslator()->trans('Display "likes"?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_LIKES

                    'PH_BLOG_DISPLAY_SHARER' => [
                        'title' => $this->module->getTranslator()->trans('Use share icons on single post page?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_SHARER

                    'PH_BLOG_DISPLAY_AUTHOR' => [
                        'title' => $this->module->getTranslator()->trans('Display post author?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                        'desc' => $this->module->getTranslator()->trans('This option also applies to the list of posts from the category', [], 'Modules.Phsimpleblog.Admin'),
                    ], // PH_BLOG_DISPLAY_AUTHOR

                    'PH_BLOG_DISPLAY_VIEWS' => [
                        'title' => $this->module->getTranslator()->trans('Display "views"?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                        'desc' => $this->module->getTranslator()->trans('This option also applies to the list of posts from the category', [], 'Modules.Phsimpleblog.Admin'),
                    ], // PH_BLOG_DISPLAY_VIEWS

                    'PH_BLOG_DISPLAY_DATE' => [
                        'title' => $this->module->getTranslator()->trans('Display post creation date?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                        'desc' => $this->module->getTranslator()->trans('This option also applies to the list of posts from the category', [], 'Modules.Phsimpleblog.Admin'),
                    ], // PH_BLOG_DISPLAY_DATE

                    'PH_BLOG_DISPLAY_FEATURED' => [
                        'title' => $this->module->getTranslator()->trans('Display post featured image?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_FEATURED

                    'PH_BLOG_DISPLAY_CATEGORY' => [
                        'title' => $this->module->getTranslator()->trans('Display post category?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                        'desc' => $this->module->getTranslator()->trans('This option also applies to the list of posts from the category', [], 'Modules.Phsimpleblog.Admin'),
                    ], // PH_BLOG_DISPLAY_CATEGORY

                    'PH_BLOG_DISPLAY_TAGS' => [
                        'title' => $this->module->getTranslator()->trans('Display post tags?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_TAGS

                    'PH_BLOG_DISPLAY_RELATED' => [
                        'title' => $this->module->getTranslator()->trans('Display related products?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_RELATED
                ],
            ],

            'category_page' => [
                'submit' => ['title' => $this->module->getTranslator()->trans('Update', [], 'Modules.Phsimpleblog.Admin'), 'class' => 'button'],
                'title' => $this->module->getTranslator()->trans('Appearance Settings - Post lists', [], 'Modules.Phsimpleblog.Admin'),
                'fields' => [
                    'PH_BLOG_DISPLAY_MORE' => [
                        'title' => $this->module->getTranslator()->trans('Display "Read more"?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_MORES

                    'PH_BLOG_DISPLAY_COMMENTS' => [
                        'title' => $this->module->getTranslator()->trans('Display number of comments?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_COMMENTS

                    'PH_BLOG_DISPLAY_THUMBNAIL' => [
                        'title' => $this->module->getTranslator()->trans('Display post thumbnails?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_THUMBNAILS


                    'PH_BLOG_DISPLAY_CAT_DESC' => [
                        'title' => $this->module->getTranslator()->trans('Display category description on category page?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_CAT_DESC

                    'PH_BLOG_DISPLAY_CATEGORY_IMAGE' => [
                        'title' => $this->module->getTranslator()->trans('Display category image?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_CATEGORY_IMAGE

                    'PH_BLOG_DISPLAY_CATEGORY_CHILDREN' => [
                        'title' => $this->module->getTranslator()->trans('Display subcategories?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_DISPLAY_CATEGORY_CHILDREN

                    'PH_CATEGORY_IMAGE_X' => [
                        'title' => $this->module->getTranslator()->trans('Default category image width (px)', [], 'Modules.Phsimpleblog.Admin'),
                        'cast' => 'intval',
                        'desc' => $this->module->getTranslator()->trans('Default: 535 (For PrestaShop 1.5), 870 (For PrestaShop 1.6), 1000 (For PrestaShop 1.7)', [], 'Modules.Phsimpleblog.Admin'),
                        'type' => 'text',
                        'required' => true,
                        'validation' => 'isUnsignedId',
                    ], // PH_CATEGORY_IMAGE_X

                    'PH_CATEGORY_IMAGE_Y' => [
                        'title' => $this->module->getTranslator()->trans('Default category image height (px)', [], 'Modules.Phsimpleblog.Admin'),
                        'cast' => 'intval',
                        'desc' => $this->module->getTranslator()->trans('Default: 150', [], 'Modules.Phsimpleblog.Admin'),
                        'type' => 'text',
                        'required' => true,
                        'validation' => 'isUnsignedId',
                    ], // PH_CATEGORY_IMAGE_Y
                ],
            ],

            'comments' => [
                'submit' => ['title' => $this->module->getTranslator()->trans('Update', [], 'Modules.Phsimpleblog.Admin'), 'class' => 'button'],
                'title' => $this->module->getTranslator()->trans('Comments', [], 'Modules.Phsimpleblog.Admin'),
                'fields' => [
                    'PH_BLOG_COMMENTS_SYSTEM' => [
                        'title' => $this->module->getTranslator()->trans('Comments system:', [], 'Modules.Phsimpleblog.Admin'),
                        'desc' => $this->module->getTranslator()->trans('What type of comments system do you want to use?', [], 'Modules.Phsimpleblog.Admin'),
                        'show' => true,
                        'required' => true,
                        'type' => 'radio',
                        'choices' => [
                            'native' => $this->module->getTranslator()->trans('Default native comments', [], 'Modules.Phsimpleblog.Admin'),
                            'facebook' => $this->module->getTranslator()->trans('Facebook comments', [], 'Modules.Phsimpleblog.Admin'),
                            'disqus' => $this->module->getTranslator()->trans('Disqus comments', [], 'Modules.Phsimpleblog.Admin'),
                        ],
                    ], // PH_BLOG_GRID_COLUMNS

                    'PH_BLOG_COMMENT_AUTO_APPROVAL' => [
                        'title' => $this->module->getTranslator()->trans('Automatically approve new comments?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_COMMENT_AUTO_APPROVAL

                    'PH_BLOG_COMMENT_ALLOW' => [
                        'title' => $this->module->getTranslator()->trans('Allow comments?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_COMMENT_ALLOW

                    'PH_BLOG_COMMENT_ALLOW_GUEST' => [
                        'title' => $this->module->getTranslator()->trans('Allow comments for non logged in users?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_COMMENT_ALLOW_GUEST

                    'PH_BLOG_COMMENT_NOTIFICATIONS' => [
                        'title' => $this->module->getTranslator()->trans('Notify about new comments?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'desc' => $this->module->getTranslator()->trans('Only for native comment system', [], 'Modules.Phsimpleblog.Admin'),
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_COMMENT_NOTIFICATIONS

                    'PH_BLOG_COMMENT_NOTIFY_EMAIL' => [
                        'title' => $this->module->getTranslator()->trans('E-mail for notifications', [], 'Modules.Phsimpleblog.Admin'),
                        'type' => 'text',
                        'desc' => $this->module->getTranslator()->trans('Only for native comment system', [], 'Modules.Phsimpleblog.Admin'),
                        'size' => 55,
                        'required' => false,
                    ], // PH_BLOG_COMMENT_NOTIFY_EMAIL

                    'PS_COMMENTS_MARK_EMAILS' => [
                        'title' => $this->module->getTranslator()->trans('E-mail\'s for highlighted comments', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isGenericName',
                        'type' => 'text',
                        'hint' => $this->module->getTranslator()->trans('Separated by comma.', [], 'Modules.Phsimpleblog.Admin'),
                        'desc' => $this->module->getTranslator()->trans('Type e-mails of customer which comments will be highlighted on the comment list', [], 'Modules.Phsimpleblog.Admin'),
                    ],
                ],
            ],

            'facebook_comments' => [
                'submit' => ['title' => $this->module->getTranslator()->trans('Update', [], 'Modules.Phsimpleblog.Admin'), 'class' => 'button'],
                'title' => $this->module->getTranslator()->trans('Facebook comments - settings', [], 'Modules.Phsimpleblog.Admin'),
                'fields' => [
                    'PH_BLOG_FACEBOOK_MODERATOR' => [
                        'title' => $this->module->getTranslator()->trans('Facebook comments moderator User ID', [], 'Modules.Phsimpleblog.Admin'),
                        'type' => 'text',
                        'size' => 55,
                    ], // PH_BLOG_FACEBOOK_MODERATOR

                    'PH_BLOG_FACEBOOK_APP_ID' => [
                        'title' => $this->module->getTranslator()->trans('Facebook application ID (may be required for comments moderation)', [], 'Modules.Phsimpleblog.Admin'),
                        'type' => 'text',
                        'size' => 75,
                    ], // PH_BLOG_FACEBOOK_APP_ID

                    'PH_BLOG_FACEBOOK_COLOR_SCHEME' => [
                        'title' => $this->module->getTranslator()->trans('Faceboook comments color scheme', [], 'Modules.Phsimpleblog.Admin'),
                        'show' => true,
                        'required' => true,
                        'type' => 'radio',
                        'choices' => [
                            'light' => $this->module->getTranslator()->trans('Light', [], 'Modules.Phsimpleblog.Admin'),
                            'dark' => $this->module->getTranslator()->trans('Dark', [], 'Modules.Phsimpleblog.Admin'),
                        ],
                    ], // PH_BLOG_FACEBOOK_COLOR_SCHEME
                ],
            ],

            'facebook_share' => [
                'submit' => ['title' => $this->module->getTranslator()->trans('Update', [], 'Modules.Phsimpleblog.Admin'), 'class' => 'button'],
                'title' => $this->module->getTranslator()->trans('Facebook sharing - settings', [], 'Modules.Phsimpleblog.Admin'),
                'fields' => [
                    'PH_BLOG_IMAGE_FBSHARE' => [
                        'title' => $this->module->getTranslator()->trans('Which image use as a image shared on Facebook?', [], 'Modules.Phsimpleblog.Admin'),
                        'show' => true,
                        'required' => true,
                        'type' => 'radio',
                        'choices' => [
                            'featured' => $this->module->getTranslator()->trans('Featured', [], 'Modules.Phsimpleblog.Admin'),
                            'thumbnail' => $this->module->getTranslator()->trans('Thumbnail', [], 'Modules.Phsimpleblog.Admin'),
                        ],
                    ], // PH_BLOG_IMAGE_FBSHARE
                ],
            ],

            'disqus_comments' => [
                'submit' => ['title' => $this->module->getTranslator()->trans('Update', [], 'Modules.Phsimpleblog.Admin'), 'class' => 'button'],
                'title' => $this->module->getTranslator()->trans('Disqus comments - settings', [], 'Modules.Phsimpleblog.Admin'),
                'fields' => [
                    'PH_BLOG_DISQUS_SHORTNAME' => [
                        'title' => $this->module->getTranslator()->trans('Shortname', [], 'Modules.Phsimpleblog.Admin'),
                        'type' => 'text',
                        'size' => 55,
                    ], // PH_BLOG_DISQUS_SHORTNAME
                ],
            ],

            'comments_spam_protection' => [
                'submit' => ['title' => $this->module->getTranslator()->trans('Update', [], 'Modules.Phsimpleblog.Admin'), 'class' => 'button'],
                'title' => $this->module->getTranslator()->trans('Comments - Spam Protection for native comments system', [], 'Modules.Phsimpleblog.Admin') . ' - reCAPTCHA v2, checkbox version',
                'info' => '<div class="alert alert-info">' . $this->module->getTranslator()->trans('Spam protection is provided by Google reCAPTCHA service, to gain keys:', [], 'Modules.Phsimpleblog.Admin') . '
                    <ol>
                        <li>' . $this->module->getTranslator()->trans('Login to your Google Account and go to this page:', [], 'Modules.Phsimpleblog.Admin') . ' https://www.google.com/recaptcha/admin</li>
                        <li>' . $this->module->getTranslator()->trans('Register a new site', [], 'Modules.Phsimpleblog.Admin') . '</li>
                        <li>' . $this->module->getTranslator()->trans('Get Site Key and Secret Key and provide these keys here in Settings', [], 'Modules.Phsimpleblog.Admin') . '</li>
                        <li>' . $this->module->getTranslator()->trans('Remember: if you do not specify the correct keys, the captcha will not work', [], 'Modules.Phsimpleblog.Admin') . '</li>
                    </ol>
                </div>',
                'fields' => [
                    'PH_BLOG_COMMENTS_RECAPTCHA' => [
                        'title' => $this->module->getTranslator()->trans('Enable spam protection?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_COMMENTS_RECAPTCHA

                    'PH_BLOG_COMMENTS_RECAPTCHA_SITE_KEY' => [
                        'title' => $this->module->getTranslator()->trans('Site key:', [], 'Modules.Phsimpleblog.Admin'),
                        'type' => 'text',
                        'size' => 255,
                        'required' => false,
                    ], // PH_BLOG_COMMENTS_RECAPTCHA_SITE_KEY

                    'PH_BLOG_COMMENTS_RECAPTCHA_SECRET_KEY' => [
                        'title' => $this->module->getTranslator()->trans('Secret key:', [], 'Modules.Phsimpleblog.Admin'),
                        'type' => 'text',
                        'size' => 255,
                        'required' => false,
                    ], // PH_BLOG_COMMENTS_RECAPTCHA_SECRET_KEY

                    'PH_BLOG_COMMENTS_RECAPTCHA_THEME' => [
                        'title' => $this->module->getTranslator()->trans('reCAPTCHA color scheme:', [], 'Modules.Phsimpleblog.Admin'),
                        'show' => true,
                        'required' => true,
                        'type' => 'radio',
                        'choices' => [
                            'light' => $this->module->getTranslator()->trans('Light', [], 'Modules.Phsimpleblog.Admin'),
                            'dark' => $this->module->getTranslator()->trans('Dark', [], 'Modules.Phsimpleblog.Admin'),
                        ],
                    ], // PH_BLOG_COMMENTS_RECAPTCHA_THEME
                ],
            ],

            'thumbnails' => [
                'submit' => ['title' => $this->module->getTranslator()->trans('Update', [], 'Modules.Phsimpleblog.Admin'), 'class' => 'button'],
                'title' => $this->module->getTranslator()->trans('Thumbnails Settings', [], 'Modules.Phsimpleblog.Admin'),
                'info' => '<div class="alert alert-info">' . $this->module->getTranslator()->trans('Remember to regenerate thumbnails after doing changes here', [], 'Modules.Phsimpleblog.Admin') . '</div>',
                'fields' => [
                    'PH_BLOG_THUMB_METHOD' => [
                        'title' => $this->module->getTranslator()->trans('Resize method:', [], 'Modules.Phsimpleblog.Admin'),
                        'cast' => 'intval',
                        'desc' => $this->module->getTranslator()->trans('Select wich method use to resize thumbnail. Adaptive resize: What it does is resize the image to get as close as possible to the desired dimensions, then crops the image down to the proper size from the center.', [], 'Modules.Phsimpleblog.Admin'),
                        'show' => true,
                        'required' => true,
                        'type' => 'radio',
                        'choices' => [
                            '1' => $this->module->getTranslator()->trans('Adaptive resize (recommended)', [], 'Modules.Phsimpleblog.Admin'),
                            '2' => $this->module->getTranslator()->trans('Crop from center', [], 'Modules.Phsimpleblog.Admin'),
                        ],
                    ], // PH_BLOG_THUMB_METHOD

                    'PH_BLOG_THUMB_X' => [
                        'title' => $this->module->getTranslator()->trans('Default thumbnail width (px)', [], 'Modules.Phsimpleblog.Admin'),
                        'cast' => 'intval',
                        'desc' => $this->module->getTranslator()->trans('Default: 255 (For PrestaShop 1.5), 420 (For PrestaShop 1.6), 600 (For PrestaShop 1.7)', [], 'Modules.Phsimpleblog.Admin'),
                        'type' => 'text',
                        'required' => true,
                        'validation' => 'isUnsignedId',
                    ], // PH_BLOG_THUMB_X

                    'PH_BLOG_THUMB_Y' => [
                        'title' => $this->module->getTranslator()->trans('Default thumbnail height (px)', [], 'Modules.Phsimpleblog.Admin'),
                        'cast' => 'intval',
                        'desc' => $this->module->getTranslator()->trans('Default: 200 (For PrestaShop 1.5 and 1.6)', [], 'Modules.Phsimpleblog.Admin'),
                        'type' => 'text',
                        'required' => true,
                        'validation' => 'isUnsignedId',
                    ], // PH_BLOG_THUMB_Y

                    'PH_BLOG_THUMB_X_WIDE' => [
                        'title' => $this->module->getTranslator()->trans('Default thumbnail width (wide version) (px)', [], 'Modules.Phsimpleblog.Admin'),
                        'cast' => 'intval',
                        'desc' => $this->module->getTranslator()->trans('Default: 535 (For PrestaShop 1.5), 870 (For PrestaShop 1.6), 1000 (For PrestaShop 1.7)', [], 'Modules.Phsimpleblog.Admin'),
                        'type' => 'text',
                        'required' => true,
                        'validation' => 'isUnsignedId',
                    ], // PH_BLOG_THUMB_X_WIDE

                    'PH_BLOG_THUMB_Y_WIDE' => [
                        'title' => $this->module->getTranslator()->trans('Default thumbnail height (wide version) (px)', [], 'Modules.Phsimpleblog.Admin'),
                        'cast' => 'intval',
                        'desc' => $this->module->getTranslator()->trans('Default: 350 (For PrestaShop 1.5 and 1.6)', [], 'Modules.Phsimpleblog.Admin'),
                        'type' => 'text',
                        'required' => true,
                        'validation' => 'isUnsignedId',
                    ], // PH_BLOG_THUMB_Y_WIDE
                ],
            ],

            'troubleshooting' => [
                'submit' => ['title' => $this->module->getTranslator()->trans('Update', [], 'Modules.Phsimpleblog.Admin'), 'class' => 'button'],
                'title' => $this->module->getTranslator()->trans('Troubleshooting', [], 'Modules.Phsimpleblog.Admin'),
                'fields' => [
                    'PH_BLOG_RELATED_PRODUCTS_USE_DEFAULT_LIST' => [
                        'title' => $this->module->getTranslator()->trans('Use product list from your theme for related products?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'desc' => $this->module->getTranslator()->trans('By default Blog for PrestaShop uses default-bootstrap product list markup for related products, you can switch this option to load your product-list.tpl instead. In PrestaShop 1.7 we always use theme products list.', [], 'Modules.Phsimpleblog.Admin'),
                        'type' => 'bool',
                    ], // PH_BLOG_RELATED_PRODUCTS_USE_DEFAULT_LIST

                    'PH_BLOG_LOAD_FONT_AWESOME' => [
                        'title' => $this->module->getTranslator()->trans('Load FontAwesome from module? Only for PS 1.6.', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'desc' => $this->module->getTranslator()->trans('Important: Blog for PrestaShop uses fa fa-iconname format instead of icon-iconname format used by default in PrestaShop.', [], 'Modules.Phsimpleblog.Admin'),
                        'type' => 'bool',
                    ], // PH_BLOG_LOAD_FONT_AWESOME

                    'PH_BLOG_LOAD_BXSLIDER' => [
                        'title' => $this->module->getTranslator()->trans('Load BxSlider from module? Only for PS 1.6.', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_LOAD_BXSLIDER

                    // 'PH_BLOG_LOAD_MASONRY' => array(
                    //     'title' => $this->module->getTranslator()->trans('Load Masonry from module?', [], 'Modules.Phsimpleblog.Admin'),
                    //     'validation' => 'isBool',
                    //     'cast' => 'intval',
                    //     'required' => true,
                    //     'type' => 'bool',
                    // ), // PH_BLOG_LOAD_MASONRY

                    'PH_BLOG_LOAD_FITVIDS' => [
                        'title' => $this->module->getTranslator()->trans('Load FitVids from module?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_LOAD_FITVIDS

                    'PH_BLOG_WAREHOUSE_COMPAT' => [
                        'title' => $this->module->getTranslator()->trans('Force Warehouse theme compatibility?', [], 'Modules.Phsimpleblog.Admin'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'required' => true,
                        'type' => 'bool',
                    ], // PH_BLOG_WAREHOUSE_COMPAT
                ],
            ],
        ];

        $widgets_options = [];
        $widgets_options = array_merge($relatedPosts, []);

        $import_settings = [
            'import_settings' => [
                'submit' => ['title' => $this->module->getTranslator()->trans('Import settings', [], 'Modules.Phsimpleblog.Admin'), 'class' => 'button'],
                'title' => $this->module->getTranslator()->trans('Import settings', [], 'Modules.Phsimpleblog.Admin'),
                'fields' => [
                    'PH_BLOG_IMPORT_SETTINGS' => [
                        'title' => $this->module->getTranslator()->trans('Paste here content of your settings file to import', [], 'Modules.Phsimpleblog.Admin'),
                        'show' => false,
                        'required' => false,
                        'type' => 'textarea',
                        'cols' => '70',
                        'rows' => '10',
                    ], // PH_BLOG_IMPORT_SETTINGS
                ], ],
        ];

        //$this->hide_multishop_checkbox = true;
        $this->fields_options = array_merge($standard_options, $widgets_options, $import_settings);

        return parent::renderOptions();
    }

    public static function prepareValueForLangs($value)
    {
        $languages = Language::getLanguages(false);

        $output = [];

        foreach ($languages as $lang) {
            $output[$lang['id_lang']] = $value;
        }

        return $output;
    }

    public static function getValueForLangs($field)
    {
        $languages = Language::getLanguages(false);

        $output = [];

        foreach ($languages as $lang) {
            $output[$lang['id_lang']] = Configuration::get($field, $lang['id_lang']);
        }

        return $output;
    }

    public function beforeUpdateOptions()
    {
        $importSettings = Tools::getValue('PH_BLOG_IMPORT_SETTINGS', false);

        if (trim($importSettings) != '') {
            if (!is_array(unserialize($importSettings))) {
                die(Tools::displayError('File with settings is invalid'));
            }

            $settings = unserialize($importSettings);
            $simple_fields = [];

            foreach ($this->fields_options as $category_data) {
                if (!isset($category_data['fields'])) {
                    continue;
                }

                foreach ($category_data['fields'] as $name => $field) {
                    $simple_fields[$name] = $field;
                }
            }

            foreach ($settings as $conf_name => $conf_value) {
                Configuration::deleteByName($conf_name);

                // if($simple_fields[$conf_name]['type'] == 'textLang')
                //     Configuration::updateValue($conf_name, self::prepareValueForLangs($conf_value));
                // else
                //     Configuration::updateValue($conf_name, $conf_value);
                Configuration::updateValue($conf_name, $conf_value);
            }

            Tools::redirectAdmin(self::$currentIndex . '&token=' . Tools::getValue('token') . '&conf=6');
        }

        $customCSS = '/** custom css for SimpleBlog **/' . PHP_EOL;
        $customCSS .= Tools::getValue('PH_BLOG_CSS', false);

        if ($customCSS) {
            $handle = _PS_MODULE_DIR_ . 'ph_simpleblog/css/custom.css';

            if (!file_put_contents($handle, $customCSS)) {
                die(Tools::displayError('Problem with saving custom CSS, contact with module author'));
            }
        }

        // delete routing from PREFIX_configuration
        Db::getInstance()->query(
            'DELETE FROM `'._DB_PREFIX_.'configuration`
            WHERE `name` LIKE \'PS_ROUTE_module-ph_simpleblog%\''
        );
    }

    public function initContent()
    {
        $this->multiple_fieldsets = true;

        if (Tools::isSubmit('regenerateThumbnails')) {
            SimpleBlogPost::regenerateThumbnails();
            Tools::redirectAdmin(self::$currentIndex . '&token=' . Tools::getValue('token') . '&conf=9');
        }

        if (Tools::isSubmit('submitExportSettings')) {
            header('Content-type: text/plain');
            header('Content-Disposition: attachment; filename=ph_simpleblog_configuration_' . date('d-m-Y') . '.txt');

            $configs = [];
            foreach ($this->fields_options as $category_data) {
                if (!isset($category_data['fields'])) {
                    continue;
                }

                $fields = $category_data['fields'];

                foreach ($fields as $field => $values) {
                    if ($values['type'] == 'textLang') {
                        $configs[$field] = self::getValueForLangs($field);
                    } else {
                        $configs[$field] = Configuration::get($field);
                    }
                }
            }

            echo serialize($configs);

            exit();
        }

        $this->context->smarty->assign([
            'content' => $this->content,
            'url_post' => self::$currentIndex . '&token=' . $this->token,
        ]);

        parent::initContent();
    }

    public function processUpdateOptions()
    {
        parent::processUpdateOptions();
        if (empty($this->errors)) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminSimpleBlogSettings') . '&conf=6');
        }
    }
}
