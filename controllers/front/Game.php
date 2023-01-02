<?php

class click_for_winGameModuleFrontController extends ModuleFrontController
{
    public $auth = true;
    public $guestAllowed = false;

    public function initContent()
    {
        parent::initContent();
        $this->setTemplate('module:click_for_win/views/templates/front/game.tpl');
    }
}