<?php
$senha = "@gmail.com"; // Coloque a senha que deseja
$hash = password_hash($senha, PASSWORD_ARGON2ID, [
    'memory_cost' => 1 << 17,
    'time_cost' => 4,
    'threads' => 2
]);
echo "Hash gerado: " . $hash;
?>
