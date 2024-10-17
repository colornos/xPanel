<?php
$servers = [
    ['ip' => '192.168.1.100', 'user' => 'root', 'pass' => 'password'],
    ['ip' => '192.168.1.101', 'user' => 'root', 'pass' => 'password']
];

foreach ($servers as $server) {
    $connection = ssh2_connect($server['ip'], 22);
    ssh2_auth_password($connection, $server['user'], $server['pass']);

    $stream = ssh2_exec($connection, 'uptime');
    stream_set_blocking($stream, true);
    $output = stream_get_contents($stream);
    echo "Server: {$server['ip']} - Uptime: $output<br>";
}
?>
