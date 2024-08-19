<?php

declare(strict_types=1);
namespace Service;
use Controller\AddressController;
require_once 'src/Service/Url.php';
require_once 'src/Controller/Address.php';
require_once 'src/Class/Database.php';

class AddressService extends UrlService {
    private AddressController $controller;
    public function __construct()
    {
        parent::__construct();
        $this->controller = new AddressController();
    }

    public function initialize(): bool {
        $controller = $this->getController();
        $segments = $this->getSegments();
        $requestMethod = $this->getRequestMethod();


        if (in_array('address', $segments)) {
            $id = isset($segments[array_key_last($segments)]) ? (int)$segments[array_key_last($segments)] : 0;

            switch ($requestMethod) {
                case 'POST':
                    $controller->create();
                    break;

                case 'GET':
                    if(isset($_GET['exportXML'])) {
                        $controller->exportXML();
                        break;
                    }
                    if(isset($_GET['exportJSON'])) {
                        $controller->exportJSON();
                        break;
                    }
                    if ($id) {
                        $controller->read($id);
                    } else {
                        $controller->readAll();
                    }
                    break;

                case 'PUT':
                    if ($id) {
                        $controller->update($id);
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID required for update']);
                    }
                    break;

                case 'DELETE':
                    if ($id) {
                        $controller->delete($id);
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID required for delete']);
                    }
                    break;

                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Method not allowed']);
                    break;
            }

            return true;
        }

        return false;
    }

    public function getController(): AddressController
    {
        return $this->controller;
    }
}
