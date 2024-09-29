<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Pattern;

    class IniHandleNeoxDashModel
    {

        public ?string $new    = null;
        public ?string $form  = null;
        public ?string $route  = null;
        public ?array  $params = [];

        public function getNew(): ?string
        {
            return $this->new;
        }

        public function setNew(?string $new): IniHandleNeoxDashModel
        {
            $this->new = $new;
            return $this;
        }

        public function getForm(): ?string
        {
            return $this->form;
        }

        public function setForm(?string $form): IniHandleNeoxDashModel
        {
            $this->form = $form;
            return $this;
        }

        public function getRoute(): ?string
        {
            return $this->route;
        }

        public function setRoute(?string $route): IniHandleNeoxDashModel
        {
            $this->route = $route;
            return $this;
        }

        public function getParams(): ?array
        {
            return $this->params;
        }

        public function setParams(?array $params): IniHandleNeoxDashModel
        {
            $this->params = $params;
            return $this;
        }
        
        
    }