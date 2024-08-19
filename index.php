<?php

use Service\AddressService;

require_once 'src/Service/Address.php';

$addressService = new AddressService();

if (!$addressService->initialize()) {
$currentAddress = $addressService->getController()->readAll(false);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Address Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-dark text-white">


<main class="d-flex flex-nowrap">
    <?php
        include 'navbar.php';
    ?>
    <div class="container">
        <h1 class="text-center mt-5">Address Book</h1>
        <table class="table table-dark table-striped mt-4">
            <thead>
            <tr>
                <th>Name</th>
                <th>First Name</th>
                <th>Email</th>
                <th>Street</th>
                <th>Zip Code</th>
                <th>City</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
                <?php
                    /**
                     * @var \Entity\Address $address
                     */
                    foreach ($currentAddress as $address) {
                        $addressId = $address->getId()
                ?>
                    <tr data-address-view="<?php echo $addressId?>">
                        <td><?php echo $address->getName() ?></td>
                        <td><?php echo $address->getFirstName() ?></td>
                        <td><?php echo $address->getEmail() ?></td>
                        <td><?php echo $address->getStreet() ?></td>
                        <td><?php echo $address->getZipCode() ?></td>
                        <td><?php echo $address->getCity()->getName() ?></td>
                        <td><a href="edit.php?addressId=<?php echo $addressId?>">Edit</a> | <a href="#" class="delete" data-delete-id="<?php echo $addressId?>">Delete</a> </td>
                    </tr>

                <?php } ?>
            </tbody>
        </table>

        <button class="btn btn-success" id="exportAddressesXML">Export as XML</button>
        <button class="btn btn-primary" id="exportAddressesJSON">Export as JSON</button>

        <div class="modal fade" id="deleteAddressModal" tabindex="-1" aria-labelledby="deleteAddressModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content text-black">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteAddressModalLabel">Confirm
                            Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this address?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="assets/js/address.js"></script>
</body>
</html>

<?php } ?>