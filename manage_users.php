<?php
include "database.php";
session_start();

if(!isset($_SESSION['role'])){
    echo "session role is not set, need login";
    exit;
} else if(($_SESSION['role'] !== 'admin')){
    header("location: login.php?error=unauthorized access");
    exit;
}

$sql = "SELECT user_id, name, email, role, phone_number, gender, date_of_birth, address, specialization, created_at FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-4">
            <h1 class="mb-4">Manage Users</h1>
            <?php if ($result->num_rows > 0): ?>
            <table class="table table-striped table-bordered">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Phone Number</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th>Address</th>
                    <th>Specialization</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['user_id']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                        <td><?= htmlspecialchars($row['phone_number']) ?></td>
                        <td><?= htmlspecialchars($row['gender']) ?></td>
                        <td><?= htmlspecialchars($row['date_of_birth']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td><?= htmlspecialchars($row['specialization']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <a href="edit_user.php?id=<?= $row['user_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete_user.php?id=<?= $row['user_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <?php else: ?>
                <p>No Users Found</p>
            <?php endif; ?>
        </div>    
    </body>
</html>
<?php $conn->close(); ?>