<?php
/**
 * Class TextInput.php , Last changed 10.06.2017
 * This file is part of the fpvspots
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\Models\Form;

use Nette;

/**
 * Class TextInput
 * @package App\Models\Form
 */
class TextInput extends Nette\Forms\Controls\TextInput
{
    private $icon = false;

    private $fancyTooltip = false;

    public function __construct($label = NULL, $maxLength = NULL)
    {
        parent::__construct($label, $maxLength);
    }

    public function setIcon($name)
    {
        $this->icon = $name;
        return $this;
    }

    public function setFancyTooltip($value)
    {
        $this->fancyTooltip = $value;
        return $this;
    }

    public function getControl()
    {
        $control = parent::getControl();

        if ($this->fancyTooltip || $this->icon)
        {
            $fancyControl = Nette\Utils\Html::el('div')
                ->setAttribute('class', 'fancy-form');

            if ($this->icon) {
                $fancyControl->addHtml(Nette\Utils\Html::el('i')->setAttribute('class', 'fa fa-' . $this->icon));
            }

            $fancyControl->addHtml($control);

            if ($this->fancyTooltip) {
                $fancyControl->addHtml(
                    Nette\Utils\Html::el('span')
                        ->setAttribute('class', 'fancy-tooltip top-left')
                        ->addHtml(Nette\Utils\Html::el('em')->setText($this->fancyTooltip))
                );
            }

            return $fancyControl;
        }

        return $control;
    }
}