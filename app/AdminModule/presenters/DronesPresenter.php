<?php
/**
 * Class DronesPresenter.php , Last changed 29.1.17 0:36
 * This file is part of the drones-map
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 */

namespace App\AdminModule\Presenters;


use App\Models\Grid;
use Nette\Utils\Finder;
use Symfony\Component\Filesystem\Filesystem;

class DronesPresenter extends BasePresenter
{
    /** @var \App\Managers\DroneManager @inject */
    public $droneManager;

    /** @var \App\Managers\PhotoManager @inject */
    public $photoManager;

    /** @var \App\AdminModule\Forms\DroneForm @inject */
    public $droneForm;

    private $drone;

    public function actionEdit($id)
    {
		$this->drone = $this->droneManager->getDrone($id);

		if(!$this->drone) {
			throw new \Exception($this->getTranslator()->translate('default.messages.itemNotFind'));
		}

        $this->droneForm->setDefaultData($this->drone);

		$this->template->drone = $this->drone;
    }

    public function createComponentDronesGrid($name)
    {
        $grid = new Grid($this, $name);

        $grid->setDataSource($this->droneManager->getUserDrones($this->userEntity));

        $grid->addColumnText('name', 'Název');

        $grid->addColumnText('type', 'Typ')
			->setFitContent()
            ->setRenderer(function ($item) {
                return $this->getTranslator()->translate(\App\Entities\DroneEntity::TYPES[$item->type]);
            });

        $grid->addAction('edit', $this->getTranslator()->translate('default.buttons.edit'))
            ->setIcon('edit');

        $grid->addAction('remove', $this->getTranslator()->translate('default.buttons.remove'), 'remove!')
			->setClass('btn btn-danger btn-xs ajax')
            ->setIcon('remove')
            ->setConfirm(function($item) {
                return 'Opravdu chcete smazat položku ' . $item->name . '?';
            });

        return $grid;
    }

    public function createComponentDroneForm($name)
    {
        return $this->droneForm->create($this, $name);
    }

    public function handleRemove($id)
	{
		try {
			$drone = $this->droneManager->removeUserDrone($this->userEntity, $id);
			$this->flashMessage('Stroj byl smazán.');
		} catch (\Exception $exception) {
			$this->flashMessage($exception->getMessage(), 'danger');
		}

		$this->entityManager->refresh($this->userEntity);

		if ($this->isAjax()) {
			$this->redrawControl('flashes');
			$this['dronesGrid']->reload();
		} else {
			$this->redirect('this');
		}
	}

    public function handleRemoveDronePhoto($photo_id)
	{
		$result = $this->photoManager->removeDronePhoto($photo_id);

		if($result)
			$this->flashMessage($this->getTranslator()->translate('default.messages.fileIsDeleted'));

		$this->redirect('this');
	}
}