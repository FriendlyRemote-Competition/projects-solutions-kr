<?php
$data = json_decode(file_get_contents('./table.json'), true) ?? [];

if($_SERVER['REQUEST_METHOD'] === "POST") {
    $action = $_POST['action'];
    $keys = $_POST['keys'];

    if($action === 'add') {
        $data[] = array_fill_keys($_POST['keys'], '');
    } else if ($action === 'save') {
        foreach($data as $idx => $d) {
            $data[$idx]['first name'] = $_POST['first_name_'.$idx];
            $data[$idx]['last name'] = $_POST['last_name_'.$idx];
            $data[$idx]['age'] = $_POST['age_'.$idx];
            $data[$idx]['country'] = $_POST['country_'.$idx];
            $data[$idx]['gender'] = $_POST['gender_'.$idx];
        }
    } else {
        unset($data[$action]);
    }

    file_put_contents('./table.json', json_encode(array_values($data), JSON_PRETTY_PRINT));
    header('Location: '. $_SERVER['PHP_SELF']);
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>C3: Local Table Data Control (Level 01)</title>
</head>
<body>
<form action="./index.php" method="post">
    <table border="1">
        <thead>
        <tr>
            <th>
                <input type="text" name="keys[]" value="first name" readonly>
            </th>
            <th>
                <input type="text" name="keys[]" value="last name" readonly>
            </th>
            <th>
                <input type="text" name="keys[]" value="age" readonly>
            </th>
            <th>
                <input type="text" name="keys[]" value="country" readonly>
            </th>
            <th>
                <input type="text" name="keys[]" value="gender" readonly>
            </th>
            <th>
                == Delete ==
            </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $idx => $row) { ?>
            <tr>
                <td><input type="text" name="first_name_<?= $idx ?>" value="<?= $row['first name'] ?>"></td>
                <td><input type="text" name="last_name_<?= $idx ?>" value="<?= $row['last name'] ?>"></td>
                <td><input type="text" name="age_<?= $idx ?>" value="<?= $row['age'] ?>"></td>
                <td><input type="text" name="country_<?= $idx ?>" value="<?= $row['country'] ?>"></td>
                <td><input type="text" name="gender_<?= $idx ?>" value="<?= $row['gender'] ?>"></td>
                <td>
                    <button name="action" value="<?= $idx ?>">Delete</button>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <button name="action" value="add">Add row</button>
    <button name="action" value="save">Save</button>
</form>
</body>
</html>