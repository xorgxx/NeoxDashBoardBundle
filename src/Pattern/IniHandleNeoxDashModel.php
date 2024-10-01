<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Pattern;

    use Symfony\Component\Form\FormInterface;

    class IniHandleNeoxDashModel
    {

        public ?string              $new    = null;
        public ?string              $form   = null;
        public ?string              $route  = null;
        public ?array               $params = [];
        public ?object              $entity = null;
        public FormInterface|string $formInterface;
        public ?array               $return = [];
        

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

        public function getEntity(): ?object
        {
            return $this->entity;
        }

        public function setEntity(?object $entity): IniHandleNeoxDashModel
        {
            $this->entity = $entity;
            return $this;
        }

        public function getFormInterface(): FormInterface|string
        {
            return $this->FormInterface;
        }

        public function setFormInterface(FormInterface|string $formInterface): IniHandleNeoxDashModel
        {
            $this->FormInterface = $formInterface;
            return $this;
        }

        public function getReturn(): ?array
        {
            return $this->return;
        }

        public function setReturn(?array $return): IniHandleNeoxDashModel
        {
            $this->return = $return;
            return $this;
        }

        
    }