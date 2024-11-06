<?php
function generateKodeAbsen() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $kodeAbsen = '';

    for ($i = 0; $i < 6; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $kodeAbsen .= $characters[$index];
    }

    return $kodeAbsen;
}
?>
