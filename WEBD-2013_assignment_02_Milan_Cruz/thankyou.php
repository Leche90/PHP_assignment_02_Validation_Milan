    <?php

    /*******w******** 
    
    Name: Milan Cruz
    Date: 2024-05-27
    Description:
                Assignment 2 
                Server-Side User Input Validation

     ****************/


    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="rick.css">
    <title>Thanks for shopping at the WebDev Store!</title>
</head>
<body>

    <!-- Remember that alternative syntax is good and html inside php is bad -->

    <?php
    function validate_postal_code($postal_code)
    {
        // Regular expression for Canadian postal code
        $regex = "/^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/";
        return preg_match($regex, $postal_code);
    }

    // Function to get the current year
    $current_year = date("Y");
    $max_year = $current_year + 5;

    // Validation
    $errors = [];

    $qty1 = filter_input(INPUT_POST, 'qty1', FILTER_VALIDATE_INT);
    $qty2 = filter_input(INPUT_POST, 'qty2', FILTER_VALIDATE_INT);
    $qty3 = filter_input(INPUT_POST, 'qty3', FILTER_VALIDATE_INT);
    $qty4 = filter_input(INPUT_POST, 'qty4', FILTER_VALIDATE_INT);
    $qty5 = filter_input(INPUT_POST, 'qty5', FILTER_VALIDATE_INT);

    // Check if at least one item is in the cart
    if ($qty1 <= 0 && $qty2 <= 0 && $qty3 <= 0 && $qty4 <= 0 && $qty5 <= 0) {
        $errors[] = "You must order at least one item.";
    }

    $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_SPECIAL_CHARS);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_SPECIAL_CHARS);
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_SPECIAL_CHARS);
    $province = filter_input(INPUT_POST, 'province', FILTER_SANITIZE_SPECIAL_CHARS);
    $postal = filter_input(INPUT_POST, 'postal', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $cardtype = filter_input(INPUT_POST, 'cardtype', FILTER_SANITIZE_SPECIAL_CHARS);
    $cardname = filter_input(INPUT_POST, 'cardname', FILTER_SANITIZE_SPECIAL_CHARS);
    $month = filter_input(INPUT_POST, 'month', FILTER_VALIDATE_INT);
    $year = filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT);
    $cardnumber = filter_input(INPUT_POST, 'cardnumber', FILTER_VALIDATE_INT);

    // Validate quantities
    $quantities = [$qty1, $qty2, $qty3, $qty4, $qty5];
    $product_names = ['MacBook', 'Razer Mouse', 'WD HDD', 'Nexus 7', 'Drums'];
    $product_prices = [1899.99, 79.99, 179.99, 249.99, 119.99];
    $order_total = 0;

    foreach ($quantities as $index => $qty) {
        if ($qty !== null && $qty !== false) {
            if ($qty < 0) {
                $errors[] = "Quantities must be positive integers or blank.";
            } else {
                $order_total += $qty * $product_prices[$index];
            }
        }
    }

    // Validate required fields
    if (!$fullname) $errors[] = "Full name is required.";
    if (!$address) $errors[] = "Address is required.";
    if (!$city) $errors[] = "City is required.";
    if (!$province) $errors[] = "Province is required.";
    if (!$postal) $errors[] = "Postal code is required.";
    if (!$email) $errors[] = "A valid email address is required.";
    if (!$cardtype) $errors[] = "Credit card type is required.";
    if (!$cardname) $errors[] = "Name on the card is required.";
    if (!$month || $month < 1 || $month > 12) $errors[] = "A valid expiry month is required.";
    if (!$year || $year < $current_year || $year > $max_year) $errors[] = "A valid expiry year is required.";
    if (!$cardnumber || strlen((string)$cardnumber) != 10) $errors[] = "Credit card number must be exactly 10 digits.";
    if (!validate_postal_code($postal)) $errors[] = "Postal code must be a valid Canadian postal code.";

    // Validate province
    $valid_provinces = ['AB', 'BC', 'MB', 'NB', 'NL', 'NS', 'ON', 'PE', 'QC', 'SK', 'NT', 'NU', 'YT'];
    if (!in_array($province, $valid_provinces)) {
        $errors[] = "Province must be a valid Canadian province.";
    }

    // If there are errors, display
    if (!empty($errors)) {
        echo "<h1>Form could not be processed</h1>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
    } else {
        // Process the form and generate the invoice
        echo '<div class="invoice">';
        echo "<h2>Thanks for your order, $fullname.</h2>";
        echo "<h3>Here's a summary of your order:</h3>";
        echo "<table>";
        echo "<tbody>";
        echo "<tr><td colspan='4'><h3>Address Information</h3></td></tr>";
        echo "<tr><td class='alignright'><span class='bold'>Address:</span></td><td>$address</td><td class='alignright'><span class='bold'>City:</span></td><td>$city</td></tr>";
        echo "<tr><td class='alignright'><span class='bold'>Province:</span></td><td>$province</td><td class='alignright'><span class='bold'>Postal Code:</span></td><td>$postal</td></tr>";
        echo "<tr><td colspan='2' class='alignright'><span class='bold'>Email:</span></td><td colspan='2'>$email</td></tr>";
        echo "</tbody>";
        echo "</table>";

        echo "<table>";
        echo "<tbody>";
        echo "<tr><td colspan='3'><h3>Order Information</h3></td></tr>";
        echo "<tr><td><span class='bold'>Quantity</span></td><td><span class='bold'>Description</span></td><td><span class='bold'>Cost</span></td></tr>";

        foreach ($quantities as $index => $qty) {
            if ($qty > 0) {
                $product_total = $qty * $product_prices[$index];
                echo "<tr><td>$qty</td><td>{$product_names[$index]}</td><td class='alignright'>\$" . number_format($product_total, 2) . "</td></tr>";
            }
        }

        echo "<tr><td colspan='2' class='alignright'><span class='bold'>Totals</span></td><td class='alignright'><span class='bold'>\$" . number_format($order_total, 2) . "</span></td></tr>";
        echo "</tbody>";
        echo "</table>";
        echo "</div>";

        if ($order_total > 20000) {
            echo '<div id="rollingrick">';
            echo '<h2>Congrats on the big order. Rick Astley congratulates you.</h2>';
            echo '<iframe width="600" height="475" src="//www.youtube.com/embed/dQw4w9WgXcQ"></iframe>';
            echo '</div>';
        }
    }
    ?>

</body>

</html>