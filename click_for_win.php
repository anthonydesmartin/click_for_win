<?php

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Click_For_Win extends Module implements WidgetInterface
{
    private $templateFile;

    public function __construct()
    {
        $this->name = 'click_for_win';
        $this->author = 'Anthony Desmartin';
        $this->version = '0.1.0';
        $this->need_instance = 1;
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Click For Win', [], 'Modules.ClickForWin.Admin');
        $this->description = $this->trans(
            'Click per second duel game for win some stuff in game!',
            [],
            'Modules.ClickForWin.Admin'
        );

        $this->templateFile = 'module:click_for_win/views/templates/home/click_for_win.tpl';
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayHome')
            && $this->registerHook('header')
            && Configuration::updateValue('CLICKFORWIN_DURATION', 10);
    }

    public function uninstall()
    {
        return parent::uninstall()
            && $this->unregisterHook('displayHome')
            && $this->registerHook('header');
    }

    function hookHeader()
    {
        $this->context->controller->addCSS('modules/'.$this->name.'/views/assets/css/main.css');
        $this->context->controller->addJS('modules/'.$this->name.'/views/assets/js/game.js');
    }

    public function getContent()
    {
        $output = '';
        $errors = [];

        if (Tools::isSubmit('submit')) {
            $duration = Tools::getValue('duration');
            if (!Validate::isInt($duration)) {
                $errors[] = $this->trans(
                    'The duration is invalid. Please enter a correct number.',
                    [],
                    'Modules.Featuredproducts.Admin'
                );
            }
            if (Validate::isInt($duration) && $duration < 10) {
                $errors[] = $this->trans(
                    'The duration is too small. Please enter a number bigger or eqal to 10.',
                    [],
                    'Modules.Featuredproducts.Admin'
                );
            }
            if (Validate::isInt($duration) && $duration > 60) {
                $errors[] = $this->trans(
                    'The duration is too big. Please enter a number lower or eqal to 60.',
                    [],
                    'Modules.Featuredproducts.Admin'
                );
            }
            if (count($errors)) {
                $output = $this->displayError(implode('<br />', $errors));
            } else {
                Configuration::updateValue('CLICKFORWIN_DURATION', (int)$duration);

                $this->_clearCache('*');

                $output = $this->displayConfirmation(
                    $this->trans('The settings have been updated.', [], 'Admin.Notifications.Success')
                );
            }
        }

        return $output.$this->renderForm();
    }

    public function renderForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Settings', [], 'Admin.Global'),
                    'icon' => 'icon-cogs',
                ],

                'description' => $this->trans(
                    'Define the duration of the duel (max 60 seconds).',
                    [],
                    'Modules.ClickForWin.Admin'
                ),
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->trans('Duration of the duel', [], 'Modules.ClickForWin.Admin'),
                        'name' => 'duration',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->trans('Set the duration of duels.', [], 'Modules.ClickForWin.Admin'),
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Admin.Actions'),
                ],
            ],
        ];

        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get(
            'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
        ) : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit';
        $helper->currentIndex = $this->context->link->getAdminLink(
                'AdminModules',
                false
            ).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function renderWidget($hookName, array $configuration)
    {
        $vars = $this->getWidgetVariables($hookName, $configuration);
        $this->smarty->assign($vars);

        return $this->fetch($this->templateFile);
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
        $link = $this->context->link->getModuleLink($this->name, 'Game');

        return [
            'link' => $link,
        ];
    }

    private function getConfigFieldsValues()
    {
        return [
            'duration' => Tools::getValue('duration', (int)Configuration::get('CLICKFORWIN_DURATION')),
        ];
    }
}