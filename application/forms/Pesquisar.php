<?php

class Application_Form_Pesquisar extends Zend_Form
{

    public function init()
    {
        $chave = new Zend_Form_Element_Text('chave');
        $chave->setLabel('Chave:');


        $submit = new Zend_Form_Element_Submit('enviar');
        $submit->setValue('Enviar')
               ->setDecorators(array(
                   array('ViewHelper',
                   array('helper' => 'formSubmit'))
               ));

        $this->addElements(array(
            $chave,
            $submit
        ));
    }



}

