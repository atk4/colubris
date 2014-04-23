<?php

class page_quotes_rfq_requirements extends Page_Requirements {
    function init() {
        parent::init();

        // Checking client's read permission to this quote and redirect to denied if required
        if(!$this->app->user_access->canSeeRequirements()){
            throw $this->exception('You cannot see this page','Exception_Denied');
        }

    }
}
