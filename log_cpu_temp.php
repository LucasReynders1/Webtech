<?php
function get_cpu_temperature() {
    // Voor Windows server
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $output = shell_exec("wmic /namespace:\\\\root\\wmi PATH MSAcpi_ThermalZoneTemperature get CurrentTemperature /format:csv");
        $lines = explode("\n", trim($output));
        if (count($lines) > 1) {
            $temp_data = explode(",", $lines[1]);
            if (isset($temp_data[1])) {
                $kelvin = $temp_data[1];
                $celsius = $kelvin / 10.0 - 273.15;
                return round($celsius, 1);
            }
        }
    } elseif (strtoupper(substr(PHP_OS, 0, 3)) === 'LIN') {
        // Voor Linux server
        $output = shell_exec("sensors | grep 'Core 0' | awk '{print $3}'");
        $temp = floatval(str_replace("+", "", $output));
        return $temp;
    } elseif (strtoupper(substr(PHP_OS, 0, 6)) === 'DARWIN') {
        // Voor macOS server
        $output = shell_exec("osx-cpu-temp");
        $temp = floatval($output);
        return $temp;
    } else {
        return null;
    }
}

function log_cpu_temperature($file_path) {
    $cpu_temp = get_cpu_temperature();
    if ($cpu_temp !== null) {
        $current_time = date('Y-m-d H:i:s');
        $data_entry = array(
            'datetime' => $current_time,
            'cpu_temperature' => $cpu_temp
        );

        // Lees bestaande data
        if (file_exists($file_path)) {
            $json_data = file_get_contents($file_path);
            $data = json_decode($json_data, true);
        } else {
            $data = array();
        }

        // Voeg nieuwe data toe
        $data[] = $data_entry;

        // Schrijf data terug naar JSON bestand
        file_put_contents($file_path, json_encode($data, JSON_PRETTY_PRINT));
    }
}

// Pad naar het JSON bestand (zorg ervoor dat dit pad schrijfbaar is voor de webserver)
$json_file_path = 'cpu_temperatures.json';

// Log de CPU temperatuur
log_cpu_temperature($json_file_path);

echo "CPU temperatuur gelogd.";
?>
