<?php 
    require 'connect.php';

    if (!isset($_GET['id'])) {
    die("Employee ID not provided.");
}

$id = $_GET['id'];

//Fetch employee data
$stmt = $mysqli->prepare(
    "SELECT * FROM employees WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
    die("Employee not found.");
}

//Fetch departments
$departments = [];
$res = $mysqli->query("SELECT * FROM departments ORDER BY name");
while ($dept = $res->fetch_assoc()) {
    $departments[] = $dept;
}

//Handle update submission
if (isset($_POST['update_employee'])) {

    $stmt = $mysqli->prepare(
        "UPDATE employees 
         SET name=?, email=?, department_id=?, position=?, salary=?
         WHERE id=?"
    );

    $stmt->bind_param(
        "ssisdi",
        $_POST['name'],
        $_POST['email'],
        $_POST['department_id'],
        $_POST['position'],
        $_POST['salary'],
        $_POST['id']
    );

    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee management System</title>
    <?php 
    //Defince CSS filepath in the variable
    $css_file = "style.css"
     ?>
     <!--Output the variable within the link tag-->
     <link rel="stylesheet" type="text/css" href="<?php echo $css_file?>">
</head>
<body>

    <h1>Employee Management System</h1>
    <h2>Update Employee</h2>
    <form method="post">

    <!-- Hidden ID -->
    <input type="hidden" name="id" value="<?= $row['id']; ?>">

    <input type="text" name="name"
           value="<?= htmlspecialchars($row['name']); ?>"
           placeholder="Employee Name" required>

    <input type="email" name="email"
           value="<?= htmlspecialchars($row['email']); ?>"
           placeholder="Email" required>

    <select name="department_id" required>
        <option value="">Select Department</option>
        <?php foreach ($departments as $dept): ?>
            <option value="<?= $dept['id']; ?>"
                <?= $dept['id'] == $row['department_id'] ? 'selected' : ''; ?>>
                <?= htmlspecialchars($dept['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="text" name="position"
           value="<?= htmlspecialchars($row['position']); ?>"
           placeholder="Position">

    <input type="number" step="0.01" name="salary"
           value="<?= htmlspecialchars($row['salary']); ?>"
           placeholder="Salary">

    <button type="submit" name="update_employee">
        Update Employee
    </button>

</form>
</body>
</html>