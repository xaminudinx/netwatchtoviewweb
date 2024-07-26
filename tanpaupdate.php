<?php

require('routeros_api.class.php');

$API = new RouterosAPI();
$status = '';

if ($API->connect('192.168.2.1', '1234', '4321')) {
    $data = $API->comm('/tool/netwatch/print');
    $API->disconnect();

    // Urutkan data berdasarkan host secara numerik ascending
    usort($data, function($a, $b) {
        $hostA = ip2long($a['host'] ?? '0.0.0.0');
        $hostB = ip2long($b['host'] ?? '0.0.0.0');
        return $hostA <=> $hostB;
    });

    $status = '<table class="table table-bordered" style="border-collapse: collapse; width: 100%;">';
    $status .= '<thead><tr><th>Name</th><th>Address</th><th>Status</th><th>Timeout</th></tr></thead>';
    $status .= '<tbody>';
    
    foreach ($data as $entry) {
        $statusColor = '';
        if (isset($entry['status'])) {
            if ($entry['status'] == 'up') {
                $statusColor = 'table-success';
            } elseif ($entry['status'] == 'down') {
                $statusColor = 'table-danger';
            } elseif ($entry['status'] == 'unknown') {
                $statusColor = 'table-warning';
            }
        }

        $status .= '<tr class="' . $statusColor . '">';
        $status .= '<td>' . htmlspecialchars($entry['comment'] ?? 'N/A') . '</td>';
        $status .= '<td>' . htmlspecialchars($entry['host'] ?? 'N/A') . '</td>';
        $status .= '<td>' . htmlspecialchars($entry['status'] ?? 'N/A') . '</td>';
        $status .= '<td>' . htmlspecialchars($entry['timeout'] ?? 'N/A') . '</td>';
        $status .= '</tr>';
    }
    
    $status .= '</tbody></table>';
} else {
    $status = 'Gagal terhubung ke MikroTik.';
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Netwatch MikroTik</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
        }
        .table-container {
            width: 80%;
            margin: auto;
        }
        table {
            border: 1px solid #dee2e6;
            width: 100%;
        }
        th, td {
            border: 1px solid #dee2e6 !important; /* Tambahkan batas dalam tabel */
        }
        .table-success {
            background-color: #d4edda !important;
        }
        .table-danger {
            background-color: #f8d7da !important;
        }
        .table-warning {
            background-color: #fff3cd !important;
        }
    </style>
</head>
<body>
    <h1>Status Netwatch MikroTik</h1>
    <div class="table-container">
        <?php echo $status; ?>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
