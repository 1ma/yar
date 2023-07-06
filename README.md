# YAR: Yet Another Relay

## Installation

```shell
$ composer install
$ php main.php
```

Currently, the server listens on `ws://127.0.0.1:1337` as plaintext.

## Limitations

* No persistent event storage.
* Signature verification is very slow. https://github.com/Bit-Wasp/secp256k1-php needs some love to make it work in PHP 8.1+.

## Implemented NIPs

- [NIP-01]: Basic protocol flow description


[NIP-01]: https://github.com/nostr-protocol/nips/blob/master/01.md


