<?php
require 'connect.php';

$message = "";
$messageType = "";

// Handle Add Department
if (isset($_POST['add_department'])) {

    if (empty($_POST['dept_name'])) {
        $message = "Department name is required.";
        $messageType = "error";
    } else {

        $stmt = $mysqli->prepare("INSERT INTO departments (name) VALUES (?)");
        $stmt->bind_param("s", $_POST['dept_name']);

        try {
            $stmt->execute();
            $message = "Department added successfully.";
            $messageType = "success";
        } catch (mysqli_sql_exception $e) {

            if ($e->getCode() == 1062) {
                $message = "Department already exists.";
                $messageType = "error";
            } else {
                $message = "Database error: " . $e->getMessage();
                $messageType = "error";
            }
        }

        $stmt->close();
    }
}


// Handle Add Employee
if (isset($_POST['add_employee'])) {

    if (
        empty($_POST['name']) ||
        empty($_POST['email']) ||
        empty($_POST['department_id']) ||
        empty($_POST['position']) ||
        empty($_POST['salary'])
    ) {
        $message = "All employee fields are required.";
        $messageType = "error";
    } else {

        $stmt = $mysqli->prepare(
            "INSERT INTO employees (name, email, department_id, position, salary)
             VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssisd",
            $_POST['name'],
            $_POST['email'],
            $_POST['department_id'],
            $_POST['position'],
            $_POST['salary']
        );

        try {
            $stmt->execute();
            $message = "Employee added successfully.";
            $messageType = "success";

        } catch (mysqli_sql_exception $e) {

            if ($e->getCode() == 1062) {
                $message = "Email already exists. Please use a different email.";
                $messageType = "error";
            } else {
                $message = "Database error: " . $e->getMessage();
                $messageType = "error";
            }
        }

        $stmt->close();
    }
}

//Handle Update employee
if (isset($_POST['update_employee'])) {
    $stmt = $mysqli->prepare("UPDATE employees SET name=?, email=?, department_id=?, position=?, salary=? WHERE id=?");
    $stmt->bind_param("ssisdi", $_POST['name'], $_POST['email'], $_POST['department_id'], $_POST['position'], $_POST['salary'], $_POST['id']);
    $stmt->execute();
    $stmt->close();

}

//Handle Delete employee
if (isset($_GET['delete_employee'])) {
    $stmt = $mysqli->prepare("DELETE FROM employees WHERE id=?");
    $stmt->bind_param("i", $_GET['delete_employee']);
    $stmt->execute();
    $stmt->close();
}


//Fetch departments
$departments = [];
$res = $mysqli->query("SELECT * FROM departments ORDER BY name");
while ($row = $res->fetch_assoc())
    $departments[] = $row;

//Fetch Employees with JOIN
$employees = [];
$sql = "SELECT employees.id, employees.name, employees.email, employees.position, employees.salary, departments.name AS dept_name
        FROM employees
        JOIN departments ON employees.department_id = departments.id
        ORDER BY employees.id ASC";
$res = $mysqli->query($sql);
while ($row = $res->fetch_assoc()) {
    $employees[] = $row;
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
    <link rel="stylesheet" type="text/css" href="<?php echo $css_file ?>">
</head>

<body>
    <h1>Employee Management System</h1>

    <h2>Add department</h2>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?= $messageType ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    <form method="post">
        <input type="text" name="dept_name" placeholder="Department Name" required>
        <button type="submit" name="add_department">Add department</button>
    </form>



    <h2>Add employee</h2>
    
    <form method="post">
        <input type="text" name="name" placeholder="Employee Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <select name="department_id" id="" required>
            <option value="">Select Department</option>
            <?php foreach ($departments as $dept): ?>
                <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
            <?php endforeach ?>
        </select>
        <input type="text" name="position" placeholder="Position">
        <input type="number" step="0.01" name="salary" placeholder="Salary">
        <button type="submit" name="add_employee">Add Employee</button>
    </form>
    <h2>Employees List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Position</th>
            <th>Salary</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($employees as $emp): ?>
            <tr>
                <td><?= $emp["id"] ?></td>
                <td><?= htmlspecialchars($emp['name']) ?></td>
                <td><?= htmlspecialchars($emp['email']) ?></td>
                <td><?= htmlspecialchars($emp['dept_name']) ?></td>
                <td><?= htmlspecialchars($emp['position']) ?></td>
                <td><?= number_format($emp['salary'], 2) ?></td>
                <td>
                    <a href="update.php?id=<?= $emp['id']; ?>" class="edit">Edit</a>

                    <a href="?delete_employee=<?= $emp['id'] ?>" class="delete"
                        onclick="return confirm('Delete this employee?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>