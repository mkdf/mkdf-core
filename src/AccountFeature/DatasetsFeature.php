<?php


namespace MKDF\Core\AccountFeature;

use MKDF\Core\Service\AccountFeatureInterface;

class DatasetsFeature implements AccountFeatureInterface
{
    private $active = false;

    public function getController() {
        return \MKDF\Core\Controller\MyAccountController::class;
    }
    public function getViewAction(){
        return 'datasets';
    }
    public function getEditAction(){
        return 'datasets';
    }
    public function getViewHref(){
        return '/my-account/datasets';
    }
    public function getEditHref(){
        return '/my-account/datasets';
    }
    public function hasFeature(){
        // They all have this one
        return true;
    }
    public function getLabel(){
        return 'My datasets';
    }
    public function isActive(){
        return $this->active;
    }
    public function setActive($bool){
        $this->active = !!$bool;
    }
}