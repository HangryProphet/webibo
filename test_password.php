<?php
require_once __DIR__ . '/core/db_connect.php';
require_once __DIR__ . '/core/models/UserModel.php';

// Test credentials - CHANGE THESE to your actual username and password
$testUsername = 'jere';
$testPassword = 'pass123';

echo "<h2>Password Debug Test</h2>";

// First, check database connection and show all users
try {
    $stmt = $pdo->query("SELECT id, username, email, is_verified FROM users");
    $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Database Connection: ✅ Connected</h3>";
    echo "<h3>All Users in Database (" . count($allUsers) . " total):</h3>";
    
    if (empty($allUsers)) {
        echo "<p style='color:red;'>⚠️ No users found in database! The users table is empty.</p>";
        echo "<p>This means registration is not working or the table wasn't created.</p>";
    } else {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Verified</th></tr>";
        foreach ($allUsers as $u) {
            echo "<tr>";
            echo "<td>" . $u['id'] . "</td>";
            echo "<td><strong>" . $u['username'] . "</strong></td>";
            echo "<td>" . $u['email'] . "</td>";
            echo "<td>" . ($u['is_verified'] ? '✅' : '❌') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    echo "<hr>";
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Database Error: " . $e->getMessage() . "</p>";
    exit;
}

// Get user from database
echo "<h3>Testing Login for Username: <strong>$testUsername</strong></h3>";

$user = UserModel::getUserByUsernameOrEmail($pdo, $testUsername);

if (!$user) {
    echo "<p style='color:red;'>❌ User not found with getUserByUsernameOrEmail()</p>";
    
    // Try direct query to debug
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$testUsername, $testUsername]);
    $directUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($directUser) {
        echo "<p style='color:orange;'>⚠️ BUT user found with direct query! Issue is in UserModel method.</p>";
        $user = $directUser; // Use it anyway
    } else {
        echo "<p>User truly doesn't exist. Check spelling of username.</p>";
        exit;
    }
}

echo "<p style='color:green;'>✅ User found: " . $user['username'] . "</p>";
echo "<p>Email: " . $user['email'] . "</p>";
echo "<p>Is Verified: " . ($user['is_verified'] ? 'TRUE' : 'FALSE') . "</p>";
echo "<p>Password Hash (first 30 chars): " . substr($user['password_hash'], 0, 30) . "...</p>";
echo "<p>Full Hash Length: " . strlen($user['password_hash']) . " characters</p>";

// Test password verification
echo "<hr>";
echo "<h3>Password Verification Test</h3>";
echo "<p>Testing password: <strong>$testPassword</strong></p>";

$verified = password_verify($testPassword, $user['password_hash']);

if ($verified) {
    echo "<p style='color:green; font-weight:bold;'>✅ PASSWORD VERIFIED SUCCESSFULLY!</p>";
    echo "<p>Login should work. If it doesn't, the issue is elsewhere.</p>";
} else {
    echo "<p style='color:red; font-weight:bold;'>❌ PASSWORD VERIFICATION FAILED!</p>";
    echo "<p>This means:</p>";
    echo "<ul>";
    echo "<li>The password you typed doesn't match what's in the database</li>";
    echo "<li>OR the hash in the database is corrupted</li>";
    echo "</ul>";
    
    // Generate a new hash to compare
    $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
    echo "<h4>Generate New Hash</h4>";
    echo "<p>New hash for '$testPassword': <br><code>$newHash</code></p>";
    echo "<p>You can manually update the database with this hash if needed.</p>";
}
?>
