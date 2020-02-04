<?php


namespace MKDF\Core\Service;


use MKDF\Core\Service\AccountFeatureManagerInterface;

class AccountFeatureManager implements AccountFeatureManagerInterface
{
    private $features = [];
    private $active = NULL;
    public function registerFeature(AccountFeatureInterface $f){
        if(!in_array($f, $this->features)){
            $this->features[] = $f;
        }
    }

    public function getFeatures($user_id = NULL){
        $features = [];
        foreach($this->features as $f){
            if($user_id == null || $f->hasFeature($user_id)){
                array_push($features, $f);
            }
        }
        return $features;
    }

    public function setActive(MvcEvent $event){

    }

}