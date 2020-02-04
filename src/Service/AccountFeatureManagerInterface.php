<?php


namespace MKDF\Core\Service;

interface AccountFeatureManagerInterface
{
    public function registerFeature(AccountFeatureInterface $f);

    public function getFeatures($user_id);
}