<?php
class page_projects extends Page {
    use Helper_Url;
    function init() {
        parent::init();
        $this->addNgJs();
    }
    protected function addNgJs() {
        $this->app->jquery->addStaticInclude('ng/vendor/angularjs');
        $this->app->jquery->addStaticInclude('ng/projects/app');
        $this->app->jquery->addStaticInclude('ng/projects/controllers/inlineCrud');
        $this->app->jquery->addStaticInclude('ng/_shared/directives/ngConfirmClick');
        $this->app->jquery->addStaticInclude('ng/projects/directives/inlineCrud');
        $this->app->jquery->addStaticInclude('ng/projects/directives/projectForm');
        $this->app->jquery->addStaticInclude('ng/_shared/services/API');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Project');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Quote');
        $this->app->jquery->addStaticInclude('ng/_shared/services/Client');

        $this->js(true)->colubris()->startProjectsApp(
            $this->app->url('/'),
            $this->app->getConfig('url_prefix'),
            $this->app->getConfig('url_postfix'),
            $this->app->url($this->app->getConfig('api_base_url')),
            $this->app->currentUser()->get('lhash')
        );
    }
    function defaultTemplate() {
        return array('page/projects');
    }
}