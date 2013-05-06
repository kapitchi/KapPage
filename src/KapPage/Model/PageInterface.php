<?php

namespace KapPage\Model;

interface PageInterface {
    public function getTitle();
    public function getDescription();
    public function getKeywords();
    public function getParentPageId();
    public function getPageParams();
}