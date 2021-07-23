<?php
namespace Thesauri\Controller;

use Thesauri\Form\AddThesaurusForm;
use Omeka\Form\ConfirmForm;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController 
{

    private function getResourceClassId($propertyName) {
        $class = $this->api()->search('resource_classes', [
            "local_name" => $propertyName,
        ])->getContent();
        return $class[0]->id();
    }

    protected function getItemsInItemSet($item_set_id) {
        $response = $this->api()->search('items', [ "item_set_id" => $item_set_id, "sort_by" => "title"]); 
        return $response->getContent();
    }

    public function indexAction() {

        $thesauri = [];

        $response = $this->api()->search('item_sets', ["resource_class_label" => "Collection", "sort_by" => "title"]);
        $thesauriSets = $response->getContent();
        foreach ($thesauriSets as $ts) {
            $items = $this->getItemsInItemSet($ts->id());
            array_push($thesauri, [
                "label" => $ts->displayTitle(),
                "edit_link" => $ts->url('edit'),
                "id" => $ts->id(),
                "count" => count($items),
                "items" => $items,
                "link" => "item?item_set_id=" . $ts->id() . "&sort_by=title&sort_order=asc"
            ]);
        }

        $view = new ViewModel();
        $view->setVariable("thesauri", $thesauri);
        return $view;
    }

    public function addAction() {
        $form = $this->getForm(AddThesaurusForm::class);

        if ($this->getRequest()->isPost()) 
        {
            $form->setData($this->params()->fromPost());
            if ($form->isValid())
            {
                $formData = $form->getData();

                $label = $formData['o:label'];
                $resourceClass = $this->getResourceClassId('Collection');
                $itemSetData = [
                    'o:resource_class' => [ 'o:id' => $resourceClass ],
                    'dcterms:title' => [
                        [
                            'type' => 'literal',
                            'property_id' => 1,
                            '@value' => $label,
                        ]
                    ]
                ];
                $itemSet = $this->api()->create('item_sets', $itemSetData);

                $formData['o:item_set'] = ['o:id' => $itemSet->getContent()->id() ];
                $response = $this->api($form)->create('custom_vocabs', $formData);
                $this->redirect()->toRoute('admin/thesauri');
            }
                        
        }

        $view = new ViewModel();
        $view->setVariable('form', $form);
        return $view;        
    }

    public function createConceptAction() 
    {
        $itemSet = $this->params()->fromRoute('id');

        $item = [];
        $item['o:resource_class'] = [
            'o:id' => $this->getResourceClassId("Concept"),
        ];        
        $item['o:item_set'] = [
            'o:id' => $itemSet,
        ];

        $new = $this->api()->create('items', $item)->getContent();
        return  $this->redirect()->toURL($new->url('edit'));

    }

    public function deleteConfirmAction()
    {
        $form = $this->getForm(ConfirmForm::class);

        $itemSetId = $this->params()->fromRoute('id');
        $itemSet = $this->api()->read('item_sets', $itemSetId)->getContent();

        $view = new ViewModel;
        $view->setTerminal(true);
        $view->setVariable('form', $form);
        $view->setVariable('itemSet', $itemSet);
        return $view;
    }

    public function deleteThesaurusAction()
    {
        $id = $this->params()->fromRoute('id');
        $vocab = $this->api()->search('custom_vocabs', [ 'o:item_set' => $id])->getContent()[0];
        
        foreach ($this->getItemsInItemSet($id) as $concept) {
            $this->api()->delete('items', $concept->id());
        }

        $this->api()->delete('custom_vocabs', $vocab->id());
        $this->api()->delete('item_sets', $id);



        $this->redirect()->toRoute('admin/thesauri');
    }
}