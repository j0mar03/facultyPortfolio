# Security Checklist - Faculty Portfolio System

## Pre-Deployment Security Audit

### ✅ Critical Security Items

#### 1. Environment Configuration
- [ ] APP_DEBUG=false in production
- [ ] APP_ENV=production
- [ ] Strong APP_KEY generated (32 characters)
- [ ] SESSION_SECURE_COOKIE=true
- [ ] SESSION_SAME_SITE=strict
- [ ] FORCE_HTTPS=true

#### 2. Database Security
- [ ] Strong database password (16+ characters, mixed case, numbers, symbols)
- [ ] Database user has minimum required privileges
- [ ] Database accessible only from application server
- [ ] Regular automated backups configured
- [ ] Backup encryption enabled

#### 3. File Security
- [ ] SSL certificates NOT in git repository
- [ ] .env file NOT in git repository
- [ ] storage/ and bootstrap/cache/ writable by web server
- [ ] All uploaded files validated (type, size, content)
- [ ] File upload directory outside public root

#### 4. Authentication & Authorization
- [ ] Password minimum 8 characters enforced
- [ ] Bcrypt rounds set to 12+
- [ ] Email verification enabled
- [ ] Rate limiting on login (5 attempts per minute)
- [ ] Two-factor authentication available
- [ ] Session timeout configured (120 minutes)
- [ ] Role-based access control implemented

#### 5. Web Server Configuration
- [ ] HTTPS enforced (HTTP redirects to HTTPS)
- [ ] Strong SSL/TLS configuration (TLS 1.2+)
- [ ] Security headers configured:
  - [ ] X-Frame-Options: SAMEORIGIN
  - [ ] X-Content-Type-Options: nosniff
  - [ ] X-XSS-Protection: 1; mode=block
  - [ ] Strict-Transport-Security (HSTS)
  - [ ] Content-Security-Policy
  - [ ] Referrer-Policy
- [ ] Server signature hidden
- [ ] Directory listing disabled
- [ ] .git directory blocked

#### 6. Application Security
- [ ] CSRF protection enabled (default in Laravel)
- [ ] SQL injection protection (using Eloquent/Query Builder)
- [ ] XSS protection (Blade auto-escaping)
- [ ] Mass assignment protection (fillable/guarded)
- [ ] File upload validation
- [ ] Rate limiting on API endpoints
- [ ] Input validation on all forms
- [ ] Output escaping in views

#### 7. Dependency Security
- [ ] All Composer packages updated
- [ ] All NPM packages updated
- [ ] No known vulnerabilities (run `composer audit`, `npm audit`)
- [ ] Lock files committed (composer.lock, package-lock.json)

#### 8. Logging & Monitoring
- [ ] Error logging configured
- [ ] Failed login attempts logged
- [ ] Sensitive data NOT logged (passwords, tokens)
- [ ] Log rotation configured
- [ ] Monitoring/alerting system in place

#### 9. Backup & Recovery
- [ ] Automated daily database backups
- [ ] Backup retention policy (30 days)
- [ ] Backup restoration tested
- [ ] Application code backup (git)
- [ ] Off-site backup storage

#### 10. Infrastructure Security
- [ ] Firewall configured (allow only 80, 443, 22)
- [ ] SSH key-based authentication
- [ ] Root SSH login disabled
- [ ] Fail2ban or similar installed
- [ ] Server packages regularly updated
- [ ] Unused services disabled

---

## Security Testing Commands

### 1. Check for Known Vulnerabilities
```bash
# PHP dependencies
composer audit

# NPM dependencies
npm audit

# Fix automatically (if possible)
composer update
npm audit fix
```

### 2. Test SSL Configuration
```bash
# Using OpenSSL
openssl s_client -connect your-domain.com:443 -tls1_2

# Using testssl.sh
docker run --rm -ti drwetter/testssl.sh your-domain.com

# Online tool: https://www.ssllabs.com/ssltest/
```

### 3. Security Headers Check
```bash
# Test security headers
curl -I https://your-domain.com

# Expected headers:
# X-Frame-Options: SAMEORIGIN
# X-Content-Type-Options: nosniff
# Strict-Transport-Security: max-age=31536000
```

### 4. File Permissions Audit
```bash
# Check file permissions
find . -type f -perm 0777
find . -type d -perm 0777

# Correct permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache
```

### 5. Database Security Check
```bash
# Check for default credentials
mysql -e "SELECT User, Host FROM mysql.user WHERE User IN ('root', 'admin', 'test');"

# Check for users without passwords
mysql -e "SELECT User, Host FROM mysql.user WHERE authentication_string='';"
```

---

## Immediate Actions Required

### ⚠️ CRITICAL - Fix Before Deployment

1. **Add SSL certificates to .gitignore**
```bash
echo "docker/nginx/ssl/*.key" >> .gitignore
echo "docker/nginx/ssl/*.crt" >> .gitignore
echo "docker/nginx/ssl/*.pem" >> .gitignore
git rm --cached docker/nginx/ssl/*.key
git commit -m "security: Remove SSL keys from repository"
```

2. **Create production environment template**
```bash
cp .env.example .env.production.example
# Edit and remove sensitive values
# Commit .env.production.example (without actual secrets)
```

3. **Generate strong passwords**
```bash
# Generate APP_KEY
php artisan key:generate

# Generate database password (32 characters)
openssl rand -base64 32

# Generate Redis password
openssl rand -base64 32
```

4. **Update database credentials in production**
```env
DB_PASSWORD=$(openssl rand -base64 32)
```

---

## Security Monitoring

### 1. Failed Login Monitoring
```bash
# Monitor failed logins
tail -f storage/logs/laravel.log | grep "failed login"

# Count failed attempts
grep "failed login" storage/logs/laravel.log | wc -l
```

### 2. Suspicious Activity Detection
```bash
# Check for SQL injection attempts
grep -E "(union|select|insert|delete|drop)" storage/logs/laravel.log

# Check for XSS attempts
grep -E "(<script|javascript:|onerror)" storage/logs/laravel.log

# Check for path traversal attempts
grep -E "(\.\.\/|\.\.\\)" storage/logs/laravel.log
```

### 3. File Integrity Monitoring
```bash
# Install AIDE (Advanced Intrusion Detection Environment)
sudo apt install aide

# Initialize database
sudo aideinit

# Check for changes
sudo aide --check
```

---

## Compliance Checklist

### GDPR Compliance (if applicable)
- [ ] Privacy policy published
- [ ] Cookie consent implemented
- [ ] User data export functionality
- [ ] User data deletion functionality
- [ ] Data breach notification procedure
- [ ] Data processing agreement with third parties

### Data Protection
- [ ] Passwords hashed (not encrypted)
- [ ] Sensitive data encrypted at rest
- [ ] Sensitive data encrypted in transit (HTTPS)
- [ ] API keys stored securely (not in code)
- [ ] Personal data access logged

---

## Incident Response Plan

### If Security Breach Detected:

1. **Immediate Actions**
   - [ ] Take affected systems offline
   - [ ] Preserve logs and evidence
   - [ ] Change all passwords and keys
   - [ ] Notify security team

2. **Investigation**
   - [ ] Identify breach scope
   - [ ] Determine data accessed
   - [ ] Review access logs
   - [ ] Document findings

3. **Remediation**
   - [ ] Patch vulnerabilities
   - [ ] Update security configurations
   - [ ] Deploy fixes
   - [ ] Test thoroughly

4. **Communication**
   - [ ] Notify affected users
   - [ ] Report to authorities (if required)
   - [ ] Public disclosure (if necessary)
   - [ ] Document incident

5. **Post-Incident**
   - [ ] Conduct security audit
   - [ ] Update security policies
   - [ ] Train staff
   - [ ] Implement preventive measures

---

## Security Contact

**Security Issues:** security@pup.edu.ph
**Emergency Contact:** +63 XXX XXX XXXX

**Responsible Disclosure:**
If you discover a security vulnerability, please email us at security@pup.edu.ph with:
- Description of the vulnerability
- Steps to reproduce
- Potential impact
- Suggested fix (optional)

Please allow 48 hours for initial response.

---

## Regular Security Maintenance

### Daily
- [ ] Review error logs
- [ ] Check backup status
- [ ] Monitor disk space

### Weekly
- [ ] Review access logs
- [ ] Check for failed login attempts
- [ ] Verify backup integrity

### Monthly
- [ ] Update dependencies (`composer update`, `npm update`)
- [ ] Run security audits
- [ ] Review user access levels
- [ ] Test backup restoration

### Quarterly
- [ ] Full security audit
- [ ] Penetration testing
- [ ] Review and update security policies
- [ ] Staff security training

---

**Last Updated:** November 13, 2025
**Review Schedule:** Quarterly
