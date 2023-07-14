# YAR: Yet Another Relay

[![CI](https://github.com/1ma/yar/actions/workflows/ci.yml/badge.svg)](https://github.com/1ma/yar/actions/workflows/ci.yml)

## Installation

YAR needs the [secp256k1_nostr](https://github.com/1ma/secp256k1-nostr-php) PHP extension.

```shell
$ composer install
$ php main.php
```

Currently, the server listens on `ws://127.0.0.1:1337` as plaintext.

## Limitations

* No persistent event storage.

## Implemented NIPs

- [NIP-01]: Basic protocol flow description


[NIP-01]: https://github.com/nostr-protocol/nips/blob/master/01.md


