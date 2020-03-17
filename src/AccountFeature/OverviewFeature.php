<?php


namespace MKDF\Core\AccountFeature;

use MKDF\Core\Service\AccountFeatureInterface;

class OverviewFeature implements AccountFeatureInterface
{
    private $active = false;

    public function getController() {
        return \MKDF\Core\Controller\MyAccountController::class;
    }
    public function getViewAction(){
        return 'overview';
    }
    public function getEditAction(){
        return 'edit';
    }
    public function getViewHref(){
        return '/my-account/overview';
    }
    public function getEditHref(){
        return '/my-account/edit';
    }
    public function hasFeature(){
        // They all have this one
        return true;
    }
    public function getLabel(){
        return '<i class="fas fa-info-circle"></i> Overview';
    }
    public function isActive(){
        return $this->active;
    }
    public function setActive($bool){
        $this->active = !!$bool;
    }
}