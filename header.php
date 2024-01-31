<?php 
?>
<header>
    <?php if (has_role('admin')): ?>
        <a href="users.php">Users</a>
        <a href="?logout=1">Logout</a>
        
    <!-- Search form -->
    <form class="search-form" method="post">
        <label for="search">Search by username or email:</label>
        <input type="text" name="search" id="search" value="<?= $searchTerm ?? ''; ?>">
        <button type="submit">Search</button>
    </form>
    <?php elseif(has_role('regular')): ?>
        <a href="?logout=1">Logout</a>
    <?php else: ?>
        <div>
            <a href="register.php">Register</a>
        </div>
    <?php endif;?>
</header>