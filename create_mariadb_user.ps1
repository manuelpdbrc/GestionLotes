$ErrorActionPreference = "Stop"

Write-Host "=========================================="
Write-Host " Creando usuario nativo de MariaDB 'laravel'"
Write-Host "=========================================="

Write-Host "1. Deteniendo el servicio..."
Stop-Service MariaDB -Force

Write-Host "2. Creando archivo de inicialización..."
$initFile = "C:\mariadb_reset.txt"
"CREATE DATABASE IF NOT EXISTS gestion_lotes; CREATE USER IF NOT EXISTS 'laravel'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD('secret'); GRANT ALL PRIVILEGES ON gestion_lotes.* TO 'laravel'@'localhost'; FLUSH PRIVILEGES;" | Out-File -FilePath $initFile -Encoding UTF8

Write-Host "3. Arrancando MariaDB temporal..."
$mysqldApp = "C:\Program Files\MariaDB 11.5\bin\mysqld.exe"
$defaultsArgs = "--defaults-file=C:\Program Files\MariaDB 11.5\data\my.ini"

Start-Process -FilePath $mysqldApp -ArgumentList "`"$defaultsArgs`"", "--init-file=$initFile" -WindowStyle Hidden

Write-Host "4. Esperando 10 segundos..."
Start-Sleep -Seconds 10

Write-Host "5. Matando proceso..."
Get-Process mysqld -ErrorAction SilentlyContinue | Stop-Process -Force

Write-Host "6. Limpiando..."
Remove-Item $initFile -Force

Write-Host "7. Reiniciando servicio..."
Start-Service MariaDB

Write-Host "Terminado!"
Start-Sleep -Seconds 2
