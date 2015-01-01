HostnameSeek
-------------------------------------
A PHP class for dealing with hostnames

Dependencies
-------------
 - PHP >= 5.4

Examples
--------

Get all hostnames from /etc/hosts
------------------------------------------------------------------
```php
$seek = new HostnameSeek('/etc/hosts');

print_r($seek->GetHosts());

```

Get hostnames matching certain items
------------------------------------------------------------------

```php
$seek = new HostnameSeek('/etc/hosts', ['labdevice', 'labcomputer']);

print_r($seek->GetHosts());

```