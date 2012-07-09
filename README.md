# Simple INI

A simple INI reader. This project is heavly inspired to
`Zend_Config_Ini`


## Sections

This allow multiple section and section override.

```ini
[prod]
a="ciao"
b=hello

[dev : prod]
a="ecco"
```

If you read forcing the dev state:

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