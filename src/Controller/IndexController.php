<?php
namespace Thesauri\Controller;

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
        $response = $this->api()->search('items', [ "item_set_id" => $item_set_id]); 
        return $response->getContent();
    }

    public function indexAction() {

        $thesauri = [];

        $response = $this->api()->search('item_sets', ["resource_class_label" => "Collection"]);
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
}