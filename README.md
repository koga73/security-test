# Security-Test
By: AJ Savino

## Install
```
npm install
```

## Build
Modify gulpfile.js to toggle whether the application is secure or vulnerable
```
gulp build
```

## Hosting
Use WAMP / MAMP / LAMP

## Server install
- Install CentOS 7.6
- Install NodeJS
```
yum install -y gcc-c++ make
curl -sL https://rpm.nodesource.com/setup_12.x | bash -
```
- Install PHP 5.6
- [Install MySQL](https://www.linode.com/docs/databases/mysql/how-to-install-mysql-on-centos-7/)
- Allow connection to db from webserver
```
sudo setsebool -P httpd_can_network_connect 1
```
- [Install FTP](https://www.unixmen.com/install-configure-ftp-server-centos-7/)
```
sudo setsebool -P allow_ftpd_full_access 1
```

## WHOIS
Use the WHOIS record to give some hints - this will reward users who perform recon

### Site
https://exploitfox.com

### Users
Bill Jones
User to create: "Bill"
Password: waffles1
Whois: billloveswaffles89@gmail.com
Gmail password: Waffles1

## Vulnerabilities

1. Persistent XSS - Login and go to messages.php and submit this as the message:
```
<script>console.log("your name");</script>
```

2. Reflective XSS (use IE) - Append a query string to 404 page
```
/fakepage?<script>alert('xss');</script>
```

3. CSRF - Login and save messages.php to your computer. While logged in open the saved page and submit a message. Observe that the message is submitted for the user. This illustrates that another site on a different domain can perform user actions

4. Session fixation - Send a user a link with a known session id: http://localhost:8080/security/login.php?sid=abcd - Let them login and then use the same link for yourself in a different browser. Observe that you are logged into their session

5. SQLi obtain password hashes - On search.php try inputing a single quote and observe that a SQL error message is displayed. Try guessingthe number of columns in the user table until you get to 3.
```
' union select 1,2 from users #
```
```
' union select 1,2,3 from users #
```
```
' union select username,password,3 from users #
```

6. Password cracking - Now that you have the password hash observe that it is a SHA-1 hash due to length. Use a password cracking tool and wordlist to crack the hash

7. Homepage defacement - This requires FTP on server. Assume that the password hash you previously cracked is the same as FTP credentials, login and use FTP to update the homepage. Additionally you could use vsftpd 2.3.4 which has an exploit to gain access

8. TLS Private Key obtained - This requires server. Either use FTP directory traversal to download the key, heartbleed to obtain the key from memory, or anther exploit to gain server access