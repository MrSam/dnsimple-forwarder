# dnsimple-forwarder

Use this PHP class to talk to the DNSimple API

## Usage

Get an overview of accounts (this token can access)

```
$fw = new DNSSimple_Forwarder('<your token>');
$accounts = $fw->getAccounts();
var_dump($accounts);
```

Get an overview of domains for the specified account_id

```
$fw = new DNSSimple_Forwarder('<your token>');
$domains = $fw->getDomains($account_id);
var_dump($domains);
```

Get an overview of configured forwards for a domain

```
$api_token = "<your token>";
$account_id = 1234;
$domain = "prezlymailtest.com";

$fw = new DNSSimple_Forwarder($api_token;
$domains = $fw->getDomains($account_id);
var_dump($domains);
```

Create a new forward in a domain

```
$api_token = "<your token>";
$account_id = 1234;
$domain = "prezlymailtest.com";
$from = "sam";
$to = "sam@acme.com";

$fw = new DNSSimple_Forwarder($api_token;
$fw->addForward($account_id, $domain, $from, $to);

```

## License

ZeroSlack is licensed under the GPLv3+: http://www.gnu.org/licenses/gpl-3.0.html.

## Legal

This code is in no way affiliated with, authorized, maintained, sponsored or endorsed by dnsimple or any of its affiliates or subsidiaries. This is independent and unofficial code.
