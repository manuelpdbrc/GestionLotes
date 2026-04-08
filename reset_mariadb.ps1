$ErrorActionPreference = "Stop"

Write-Host "=========================================="
Write-Host " Reseteando contraseña de MariaDB a 'root123'"
Write-Host "=========================================="

Write-Host "1. Deteniendo el servicio..."
Stop-Service MariaDB -Force

Write-Host "2. Creando archivo de inicialización..."
$initFile = "C:\mariadb_reset.txt"
"ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD('root123'); FLUSH PRIVILEGES;" | Out-File -FilePath $initFile -Encoding UTF8

Write-Host "3. Arrancando MariaDB en modo seguro temporal..."
$mysqldApp = "C:\Program Files\MariaDB 11.5\bin\mysqld.exe"
$defaultsArgs = "--defaults-file=C:\Program Files\MariaDB 11.5\data\my.ini"

Start-Process -FilePath $mysqldApp -ArgumentList "`"$defaultsArgs`"", "--init-file=$initFile" -WindowStyle Hidden

Write-Host "4. Esperando 10 segundos para que procese el comando..."
Start-Sleep -Seconds 10

Write-Host "5. Matando el proceso temporal..."
Get-Process mysqld -ErrorAction SilentlyContinue | Stop-Process -Force

Write-Host "6. Limpiando el archivo temporal..."
Remove-Item $initFile -Force

Write-Host "7. Reiniciando servicio de MariaDB normal..."
Start-Service MariaDB

Write-Host "=========================================="
Write-Host "  EXTXITO! El password nuevo es: root123 "
Write-Host "=========================================="
Start-Sleep -Seconds 3
