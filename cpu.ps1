# Your endpoint to make POST request to
$SERVER_URL = 'https://server-of-bjarni.pxl.bjth.xyz/Vieuw Data'

function Get-CpuFreq {
    Write-Output 'Fetching data...'
    
    # Use Get-WmiObject to retrieve CPU frequency
    $cpuFreq = (Get-WmiObject -Query "SELECT CurrentClockSpeed FROM Win32_Processor").CurrentClockSpeed
    # Format the data as JSON
    $dataJson = @{
        cpu_freq = $cpuFreq
    } | ConvertTo-Json

    Write-Output 'POSTing data...'
    
    # Make the actual POST request
    try {
        Invoke-RestMethod -Uri $SERVER_URL -Method Post -Body $dataJson -ContentType 'application/json'
        Write-Output 'POST success!'
    } catch {
        Write-Output 'POST failed!'
    }
}

Write-Output 'Starting loop...'

# Do this one job forever in 5 second intervals
while ($true) {
    Get-CpuFreq
    Start-Sleep -Seconds 5
}
