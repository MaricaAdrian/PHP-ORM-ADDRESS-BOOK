<?php

namespace Controller;

use Entity\Address;
use Entity\City;
use Entity\Database;

class AddressController
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
        $this->db->connect();
    }

    public function create(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['street'], $data['city'], $data['name'], $data['zipCode'], $data['firstName'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
            return;
        }

        /**
         * @var $city City
         */
        $city = $this->db->get(City::class, $data['city']);

        if (!$city) {
            http_response_code(400);
            echo json_encode(['error' => 'City not found']);
            return;
        }

        $address = new Address();
        $address->setName($data['name']);
        $address->setFirstName($data['firstName']);
        $address->setEmail($data['email']);
        $address->setStreet($data['street']);
        $address->setCity($city);
        $address->setZipCode($data['zipCode']);

        if ($this->db->insert($address)) {
            http_response_code(201);
            echo json_encode(['message' => 'Address created']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create address']);
        }
    }

    public function read(int $id): void
    {
        /**
         * @var Address $address
         */
        $address = $this->db->get(Address::class, $id);

        if ($address) {
            http_response_code(200);
            echo json_encode([
                'id' => $address->getId(),
                'street' => $address->getStreet(),
                'city' => [
                    'id' => $address->getCity()->getId(),
                    'name' => $address->getCity()->getName(),
                ],
                'zipCode' => $address->getZipCode(),
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Address not found']);
        }
    }

    public function readAll(bool $apiRequest = true): array|null
    {
        $addresses = $this->db->readAll(Address::class);

        if ($addresses) {
            if(!$apiRequest) return $addresses;

            http_response_code(200);
            $result = array_map(fn($address) => [
                'id' => $address->getId(),
                'street' => $address->getStreet(),
                'city' => $address->getCity()->getId(),
                'state' => $address->getState(),
                'zipCode' => $address->getZipCode(),
            ], $addresses);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'No addresses found']);
        }

        return null;
    }

    public function update(int $id): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['street'], $data['city'], $data['name'], $data['zipCode'], $data['firstName'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
            return;
        }

        $address = $this->db->get(Address::class, $id);

        if (!$address) {
            http_response_code(404);
            echo json_encode(['error' => 'Address not found']);
            return;
        }

        /**
         * @var $city City
         */
        $city = $this->db->get(City::class, $data['city']);

        if (!$city) {
            http_response_code(400);
            echo json_encode(['error' => 'City not found']);
            return;
        }

        $address->setName($data['name']);
        $address->setFirstName($data['firstName']);
        $address->setEmail($data['email']);
        $address->setStreet($data['street']);
        $address->setCity($city);
        $address->setZipCode($data['zipCode']);

        if ($this->db->update($address)) {
            http_response_code(200);
            echo json_encode(['message' => 'Address updated']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update address']);
        }
    }

    public function delete(int $id): void
    {
        if ($this->db->delete(Address::class, $id)) {
            http_response_code(200);
            echo json_encode(['message' => 'Address deleted']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Address not found']);
        }
    }

    public function exportXML(): void
    {
        $addresses = $this->db->readAll(Address::class);

        if ($addresses) {
            $writer = new \XMLWriter();
            $writer->openMemory();
            $writer->setIndent(true);
            $writer->setIndentString('  ');
            $writer->startDocument('1.0', 'UTF-8');
            $writer->startElement('addresses');
            foreach ($addresses as $address) {
                $writer->startElement('address');
                $writer->writeElement('id', $address->getId());
                $writer->writeElement('name', $address->getName());
                $writer->writeElement('firstName', $address->getFirstName());
                $writer->writeElement('email', $address->getEmail());
                $writer->writeElement('zipCode', $address->getZipCode());
                $writer->writeElement('street', $address->getStreet());
                $writer->startElement('city');
                    $writer->writeElement('id', $address->getCity()->getId());
                    $writer->writeElement('name', $address->getCity()->getName());
                $writer->endElement();
                $writer->endElement();
            }
            $writer->endElement();
            $xmlString = $writer->outputMemory();

            echo $xmlString;
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'No addresses found']);
        }
    }

    public function exportJSON(): void
    {
        $addresses = $this->db->readAll(Address::class);

        if ($addresses) {
            $result = array_map(fn($address) => [
                'id' => $address->getId(),
                'street' => $address->getStreet(),
                'city' => [
                    'id' => $address->getCity()->getId(),
                    'name' => $address->getCity()->getName()
                ],
                'email' => $address->getEmail(),
                'zipCode' => $address->getZipCode(),
            ], $addresses);
            echo json_encode($result, JSON_PRETTY_PRINT);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'No addresses found']);
        }
    }
}