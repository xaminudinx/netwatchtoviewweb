<?php

require('routeros_api.class.php');

$API = new RouterosAPI();
$status = '';

if ($API->connect('MIKROTIKADDRESS', 'MIKROTIKUSERNAME', 'MIKROTIKPASSWORD')) {
    $data = $API->comm('/tool/netwatch/print');
    $API->disconnect();

    // Urutkan data berdasarkan host secara numerik ascending
    usort($data, function($a, $b) {
        $hostA = ip2long($a['host'] ?? '0.0.0.0');
        $hostB = ip2long($b['host'] ?? '0.0.0.0');
        return $hostA <=> $hostB;
    });

    $status = '<table class="table table-bordered" style="border-collapse: collapse; width: 100%;">';
    $status .= '<thead><tr><th>Name</th><th>Address</th><th>Status</th></tr></thead>';
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
        $status .= '</tr>';
    }
    
    $status .= '</tbody></table>';
} else {
    $status = 'Gagal terhubung ke MikroTik.';
}

echo $status;
?>
