# Manual MySQL Configuration Guide

If the automated script doesn't work, you can configure MySQL manually.

## Step 1: Find Your MySQL Configuration File

Run the diagnostic script first:
```bash
sudo bash scripts/diagnose-mysql.sh
```

Or check these common locations:
```bash
# Check common locations
ls -la /etc/mysql/mysql.conf.d/mysqld.cnf
ls -la /etc/mysql/my.cnf
ls -la /etc/my.cnf
ls -la /etc/mysql/mariadb.conf.d/50-server.cnf

# Find all MySQL config files
find /etc -name "*mysql*.cnf" -o -name "*mariadb*.cnf" 2>/dev/null
```

## Step 2: Edit the Configuration File

Once you find your config file, edit it:

```bash
sudo nano /path/to/your/mysql/config/file
```

## Step 3: Add or Modify bind-address

Find the `[mysqld]` section and add/modify:

```ini
[mysqld]
bind-address = 0.0.0.0
```

**Note:** 
- `bind-address = 127.0.0.1` → Only localhost (Docker can't connect)
- `bind-address = 0.0.0.0` → All interfaces (Docker can connect)
- `#bind-address` (commented) → Usually defaults to all interfaces

## Step 4: Restart MySQL

```bash
sudo systemctl restart mysql
# or
sudo systemctl restart mysqld
# or
sudo systemctl restart mariadb
```

## Step 5: Verify Configuration

```bash
# Check if MySQL is listening on all interfaces
sudo netstat -tlnp | grep mysql
# Should show: 0.0.0.0:3306

# Or using ss
sudo ss -tlnp | grep mysql
```

## Common Config File Locations by Distribution

### Ubuntu/Debian (MySQL)
- `/etc/mysql/mysql.conf.d/mysqld.cnf`
- `/etc/mysql/my.cnf`

### Ubuntu/Debian (MariaDB)
- `/etc/mysql/mariadb.conf.d/50-server.cnf`
- `/etc/mysql/my.cnf`

### CentOS/RHEL
- `/etc/my.cnf`
- `/etc/mysql/my.cnf`

### Docker MySQL
- Usually in `/etc/mysql/my.cnf` inside container
- Or environment variables

## Troubleshooting

### MySQL won't restart after change

1. Check MySQL error log:
   ```bash
   sudo tail -f /var/log/mysql/error.log
   ```

2. Verify syntax:
   ```bash
   mysqld --validate-config
   ```

3. Check systemd status:
   ```bash
   sudo systemctl status mysql
   ```

### Still can't find config file

MySQL might be using default settings. Create a config file:

```bash
sudo mkdir -p /etc/mysql/mysql.conf.d
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Add:
```ini
[mysqld]
bind-address = 0.0.0.0
```

Then restart MySQL.

### Security Note

After setting `bind-address = 0.0.0.0`, MySQL will accept connections from any IP. Make sure to:

1. Use strong passwords for database users
2. Configure firewall (UFW/iptables) to restrict access
3. Use MySQL user host restrictions (users can only connect from specific hosts)

Example firewall rule:
```bash
# Allow only Docker network
sudo ufw allow from 172.17.0.0/16 to any port 3306
```
