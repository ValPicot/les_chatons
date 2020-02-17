<?php

namespace App\Form\Handler\Base;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseHandler
{
    /** @var FormInterface */
    protected $form;

    /** @var Request */
    protected $request;

    public function process(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->form = $form;
            $this->request = $request;

            return $this->onSuccess();
        }

        return false;
    }

    abstract public function onSuccess(): bool;
}
