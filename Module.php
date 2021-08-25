<?php declare(strict_types=1);
namespace Thesauri;

use Laminas\EventManager\Event;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Omeka\Api\Exception\NotFoundException;
use Omeka\Module\AbstractModule;


class Module extends AbstractModule
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    private function getResourceClassId($propertyName) 
    {
        $class = $this->api->read('resource_classes', ["label" => $propertyName])->getContent();
        return $class->id();
    }

    private function getPropertyId($propertyName) 
    {
        $prop = $this->api->read('properties', ["label" => $propertyName])->getContent();
        return $prop->id();
    }    

    public function createResourceTemplate(string $filepath) {
        $data = json_decode(file_get_contents($filepath), true);

        $label = $data['o:label'];


        try 
        {
            $template = $this->api->read('resource_templates', ['label' => $label]);
        } catch( NotFoundException $e) 
        {
            #set resource class
            $data['o:resource_class']['o:id'] = $this->getResourceClassId(
                $data['o:resource_class']['label']
            );

            #set title property
            if (in_array('o:title_property', $data)) {
                $data['o:title_property']['o:id'] = $this->getPropertyId(
                    $data['o:title_property']['label']
                );    
            }

            #set template properties
            foreach($data['o:resource_template_property'] as $key => $prop) {
                $data['o:resource_template_property'][$key]['o:property']['o:id'] = $this->getPropertyId(
                    $prop['label']
                );
            }

            $this->api->create('resource_templates', $data);
        }

    }

    protected function listFilesInDir(string $dirpath): array 
    {
        if (empty($dirpath) || !file_exists($dirpath) || !is_dir($dirpath) || !is_readable($dirpath) )  
        {
            return [];
        }

        $list = array_map(function ($file) use ($dirpath) 
        {
            return $dirpath . DIRECTORY_SEPARATOR . $file;
        }, scandir($dirpath));
        $list = array_filter($list, function ($file) 
        {
            return is_file($file) && is_readable($file) && filesize($file);
        });

        return array_values($list);
    }



    public function install(ServiceLocatorInterface $serviceLocator)
    {
        $this->api = $serviceLocator->get('Omeka\ApiManager');

        $dataFilepath = OMEKA_PATH . '/modules/Thesauri/data/'; 
        foreach($this->listFilesInDir($dataFilepath . 'resource_templates') as $filepath) {  
            $this->createResourceTemplate($filepath);
        }
    }
}