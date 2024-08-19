<?php
require_once 'src/Class/Database.php';

$db = new \Entity\Database();
$db->connect();
$cities = $db->readAll("\\Entity\City");
$address = $db->get("\\Entity\Address", $_GET['addressId']);

if(!isset($address)) {
    header('Location: /task-address-book');
}

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
        <div class="toast-container position-fixed top-0 end-0 p-3">
            <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        Address modified successfully!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <h1 class="text-center mt-5">Edit Address Book</h1>
        <div class="col-sm-4 mt-5 offset-4">
            <form id="addressForm" data-edit="<?php echo $address->getId() ?>">
                <div id="invalidFeedback"></div>
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name"
                           value="<?php echo $address->getName() ?>"
                           required>
                </div>
                <div class="mb-3">
                    <label
                            for="firstName" class="form-label">First Name</label>
                    <input type="text" class="form-control "  id="firstName" name="firstName" value="<?php echo $address->getFirstName() ?>" required>
                </div>
                <div class="mb-3">

                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $address->getEmail() ?>" required>
                </div>
                <div
                        class="mb-3">
                    <label for="street" class="form-label">Street</label>
                    <input type="text" class="form-control" id="street" name="street" value="<?php echo $address->getStreet() ?>" required>
                </div>
                <div class="mb-3">

                    <label for="zipCode" class="form-label">Zip Code</label>
                    <input type="text" class="form-control" id="zipCode" name="zipCode"
                           value="<?php echo $address->getZipCode() ?>"
                           required>
                </div>
                <div class="mb-3">
                    <label for="city" class="form-label">City</label>
                    <select class="form-select" id="city" name="city"
                            required>
                        <?php
                        /**
                         * @var $city \Entity\City
                         */
                        foreach ($cities as $city) {
                            $selected = $city->getId() === $address->getCity()->getId() ? ' selected' : '';
                            echo sprintf('<option value="%s"%s>%s</option>', $city->getId(), $selected, $city->getName());
                        }
                        ?>

                    </select>
                </div>
                <button type="submit" class="btn btn-secondary mt-4">Modify</button>
            </form>
        </div>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="assets/js/address.js"></script>
</body>
</html>