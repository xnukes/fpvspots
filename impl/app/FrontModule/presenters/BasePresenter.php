<?php
/**************************************************************************************************
 * This file is part of the github-fpvspots.                                                      *
 * Licence: GNU General Public License.                                                           *
 * Copyright (c) 2018 Lukáš Vlček (http://www.vlceklukas.cz)                                      *
 **************************************************************************************************/

namespace App\FrontModule\Presenters;

use WebLoader\Nette\CssLoader;
use WebLoader\Nette\JavaScriptLoader;
use WebLoader\Nette\LoaderFactory;

/**
 * Class HomepagePresenter
 * @package App\FrontModule\Presenters
 * @author Lukáš Vlček
 */
class BasePresenter extends \App\Presenters\BasePresenter
{
    /** @var LoaderFactory @inject */
    public $webLoader;

    /** @return CssLoader */
    protected function createComponentCss()
    {
        return $this->webLoader->createCssLoader('front');
    }

    /** @return JavaScriptLoader */
    protected function createComponentJsTop()
    {
        return $this->webLoader->createJavaScriptLoader('top');
    }
    /** @return JavaScriptLoader */
    protected function createComponentJsBottom()
    {
        return $this->webLoader->createJavaScriptLoader('bottom');
    }
}