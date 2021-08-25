<?php 
namespace Thesauri\Form;

use Laminas\Form\Form;

class AddThesaurusForm extends Form
{
    public function init()
    {
        $this->add([
            'name' => 'o:label',
            'type' => 'text',
            'options' => [
                'label' => 'Label',
                'info' => 'Name of the Thesaurus',
            ],
            'attributes' => [
                'required' => true,
                'id' => 'o-label',
            ]
        ]);


    }
}