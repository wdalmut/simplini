# Simple INI

A simple INI reader. This project is heavly inspired to
`Zend_Config_Ini`

## Unit Testing Status

[![Build Status](https://secure.travis-ci.org/wdalmut/simplini.png)](http://travis-ci.org/wdalmut/simplini?branch=master)

## Sections

This allow multiple section and section override.

```ini
[prod]
a="ciao"
b=hello

[dev : prod]
a="ecco"
```

Force the dev state:

```php
<?php
$conf = new Config();
$conf->load(__DIR__ . '/my.ini', 'dev');

echo $conf->prod()->a; // will echo "ecco"
```

## Arrays

Array support

```ini
a[] = one
a[] = two
a[] = three
```

```php
<?php
$conf = new Config();
$conf->load(__DIR__ . '/my.ini');

var_dump(conf->a); // array(one,two,three)
```

## Nested objects

Nested Objects

```ini
[production]
a.b.c = "Hello"
```

```php
<?php
$conf = new Config();
$conf->load(__DIR__ . '/my.ini');

echo conf->production()->a->b-c; // will echo "hello"
```

## Multiple sections

```ini
[mysql]
db.host = "localhost"
db.user = "user"
db.password = "password"

[redis]
nosql.a.host = "localhost"
nosql.b.host = "192.168.2.2"
```

```php
<?php
$conf = new Config();
$conf->load(__DIR__ . '/my.ini');

echo $conf->mysql()->db->host; // localhost

echo $conf->redis()->nosql->b->host; // 192.168.2.2
```

## Override strategies

You have three types of overrides.

```ini
[prod]
a = hello

[dev : prod]
a = ciao

[mysql]
host = localhost

[dm : mysql]
host = 192.168.2.2

[redis]
host = localhost

[rd : redis]
host = 192.168.3.3
```

### Override all

```php
<?php
$conf = new Config();
$conf->load(__DIR__ . '/a.ini', true);

echo $conf->prod()->a; // echo hello
echo $conf->mysql()->host; // echo 192.168.2.2
echo $conf->redis()->host; // echo 192.168.3.3
```

### Override only one section

```php
<?php
$conf = new Config();
$conf->load(__DIR__ . '/a.ini', 'dev');

echo $conf->prod()->a; // echo ciao
echo $conf->mysql()->host; // echo localhost
echo $conf->redis()->host; // echo localhost
```

### Override a group of sections

```php
<?php
$conf = new Config();
$conf->load(__DIR__ . '/a.ini', array('dev', 'dm'));

echo $conf->prod()->a; // echo ciao
echo $conf->mysql()->host; // echo 192.168.2.2
echo $conf->redis()->host; // echo localhost
```
