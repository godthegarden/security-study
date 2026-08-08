# Study Note - APM Environment & Login System

## Goal

Apache, PHP, MySQL(APM) 환경을 직접 구축하고,
회원가입 및 로그인 기능을 구현하면서 웹 애플리케이션의 기본 동작 원리를 이해하는 것을 목표로 한다.

---

# Environment

- Mac Silicon
- Ubuntu 26.04 Server
- Apache2
- PHP
- MySQL

---

# 1. APM이란?

APM은 웹 서비스를 운영하기 위한 기본 환경이다.

- **Apache** : 브라우저의 HTTP 요청을 받아 웹 페이지를 제공하는 웹 서버
- **PHP** : 서버에서 실행되는 프로그래밍 언어
- **MySQL** : 사용자 정보를 저장하는 데이터베이스(DBMS)

### 동작 과정

```

Browser
↓
Apache
↓
PHP
↓
MySQL
↓
PHP
↓
Apache
↓
Browser

```

---

# 2. Apache 설치

## 목적

Apache는 브라우저의 HTTP 요청을 받아 HTML, PHP 등의 파일을 사용자에게 제공하는 웹 서버이다.

## 설치

```bash
sudo apt install apache2
```

## 실행 확인

```bash
sudo systemctl status apache2
```

```
active (running)
```

가 출력되면 정상적으로 실행 중인 상태이다.

---

# 3. PHP 설치

## 목적

PHP는 서버에서 실행되는 언어이며

- 사용자 입력 처리
- 로그인
- 회원가입
- 데이터베이스 연동

등을 수행한다.

PHP는 실행 후 HTML만 브라우저로 전달한다.

## 설치

```bash
sudo apt install php libapache2-mod-php
```

### libapache2-mod-php

Apache가 PHP 파일을 발견하면 PHP에게 실행을 요청할 수 있도록 연결해주는 모듈이다.

## 확인

```bash
php -v
```

---

# 4. MySQL 설치

## 목적

회원가입과 로그인 정보를 저장하기 위해 데이터베이스가 필요하다.

## 설치

```bash
sudo apt install mysql-server
```

---

# 5. PHP 동작 확인

Apache의 기본 웹 루트는

```text
/var/www/html
```

이다.

### 테스트 파일 생성

```bash
sudo nano /var/www/html/info.php
```

내용

```php
<?php
phpinfo();
?>
```

브라우저에서

```
http://IP주소/info.php
```

접속하여 PHP 정보 페이지가 출력되면 Apache와 PHP 연동이 완료된 것이다.

---

# 6. Database 생성

## MySQL 접속

```sql
sudo mysql
```

## Database 생성

```sql
CREATE DATABASE login_db;
```

## Database 선택

```sql
USE login_db;
```

## user 테이블 생성

```sql
CREATE TABLE user(
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255)
);
```

### 컬럼 설명

| Column | Description |
|---------|-------------|
| id | 회원 고유 번호 |
| username | 로그인 ID |
| password | 해시된 비밀번호 |

---

# 7. 최소 권한 계정 생성

root 계정 대신 웹 서버 전용 계정을 생성하였다.

```sql
CREATE USER 'webuser'@'localhost' IDENTIFIED BY '1234';

GRANT SELECT, INSERT, UPDATE
ON login_db.*
TO 'webuser'@'localhost';

FLUSH PRIVILEGES;
```

### Why?

SQL Injection이 발생하더라도
root 권한을 사용하지 않도록 하기 위해 최소 권한 원칙(Principle of Least Privilege)을 적용하였다.

---

# 8. PHP와 MySQL 연동

db_connect.php

```php
$servername="localhost";
$username="webuser";
$password="1234";
$dbname="login_db";

$conn = new mysqli($servername,$username,$password,$dbname);
```

DB 연결 코드를 별도 파일로 분리하여

```php
require "db_connect.php";
```

한 줄로 재사용할 수 있도록 구성하였다.

---

# 9. 회원가입 구현

### 동작 과정

```
Browser

↓

register.php

↓

POST

↓

password_hash()

↓

INSERT

↓

MySQL
```

비밀번호는

```php
password_hash()
```

를 이용하여 저장하였다.

### Why?

평문 비밀번호를 저장하지 않고
해시(Hash) 형태로 저장하기 위함이다.

---

# 10. 로그인 구현

### 동작 과정

```
Browser

↓

POST

↓

SELECT

↓

password_verify()

↓

Login Success
```

회원가입 때 생성한 Hash와

```php
password_verify()
```

를 이용하여 로그인을 검증하였다.

---

# 11. Session & Cookie

로그인 성공 후

```php
session_start();

$_SESSION["username"] = $username;
```

를 수행한다.

### Session

서버에서 로그인 상태를 저장한다.

### Cookie

브라우저에는

```
PHPSESSID
```

(Session ID)만 저장된다.

실제 로그인 정보는 서버(Session)에 저장된다.

### Session 동작

```
Login

↓

Session 생성

↓

Session ID 발급

↓

Cookie 저장

↓

다음 요청

↓

Session 조회

↓

로그인 유지
```

---

# 12. logout

```php
session_start();

session_destroy();

header("Location:login.php");
exit();
```

Session을 제거하여 로그아웃을 수행한다.

---

# 13. Troubleshooting

### mysqli 모듈 미설치

확인

```bash
php -m | grep mysqli
```

설치

```bash
sudo apt install php-mysql
sudo systemctl restart apache2
```

---

### HTTP ERROR 500

문법 검사

```bash
php -l 파일이름.php
```

로그 확인

```bash
sudo tail -f /var/log/apache2/error.log
```

또는

```bash
sudo journalctl -u apache2
```

---

### 서버 IP 확인

```bash
hostname -I
```

---

# What I Learned

- Apache, PHP, MySQL의 역할을 이해하였다.
- APM 환경을 직접 구축하였다.
- PHP와 MySQL을 연동하여 로그인 기능을 구현하였다.
- Session과 Cookie를 이용한 인증 구조를 이해하였다.
- password_hash()와 password_verify()의 동작 원리를 학습하였다.
- 최소 권한 원칙(Least Privilege)의 중요성을 이해하였다.

---

# Future Improvements

현재 구현은 학습 목적의 로그인 시스템이며,
다음과 같은 보안 기능을 추가하여 개선할 예정이다.

- Prepared Statement를 이용한 SQL Injection 방어
- htmlspecialchars()를 이용한 XSS 방어
- CSRF Token 적용
- session_regenerate_id(true)를 이용한 Session Hijacking 방어
- 비밀번호 정책 강화
