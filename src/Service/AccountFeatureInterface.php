<?php


namespace MKDF\Core\Service;


interface AccountFeatureInterface
{
    public function getController();
    public function getViewAction();
    public function getEditAction();
    public function getViewHref();
    public function getEditHref();
    public function hasFeature();
    public function getLabel();
    public function isActive();
    public function setActive($bool);
}