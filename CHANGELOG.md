# Change Log

All notable changes to this project will be documented in this file.

This projects adheres to [Semantic Versioning](http://semver.org/) and [Keep a CHANGELOG](http://keepachangelog.com/).

## [Unreleased][unreleased]
-

## [4.5.6] - 2023-10-13

### Commits

- Removed inline comments that have no added value. ([0a05c2e](https://github.com/pronamic/wp-pronamic-pay-pay-nl/commit/0a05c2e80e68ad2e2cdba875fe731d39bcabc807))
- Removed intro, this applies to all gateways, this intro adds little. ([ab59d75](https://github.com/pronamic/wp-pronamic-pay-pay-nl/commit/ab59d75ace09c0d2fd156eb95a78a1206895bd2f))

Full set of changes: [`4.5.5...4.5.6`][4.5.6]

[4.5.6]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/v4.5.5...v4.5.6

## [4.5.5] - 2023-09-11

### Commits

- Include error message for unknown response. ([00d8ee8](https://github.com/pronamic/wp-pronamic-pay-pay-nl/commit/00d8ee8701aac5d39f998fd8c0fdd774d8933452))

Full set of changes: [`4.5.4...4.5.5`][4.5.5]

[4.5.5]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/v4.5.4...v4.5.5

## [4.5.4] - 2023-07-12

### Commits

- Updated tooltips to new Pay. dashboard, closes #11 . ([1a2d818](https://github.com/pronamic/wp-pronamic-pay-pay-nl/commit/1a2d818605df131d18d0d8d24e115831e3d80905))

Full set of changes: [`4.5.3...4.5.4`][4.5.4]

[4.5.4]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/v4.5.3...v4.5.4

## [4.5.3] - 2023-06-01

### Commits

- Switch from `pronamic/wp-deployer` to `pronamic/pronamic-cli`. ([040e117](https://github.com/pronamic/wp-pronamic-pay-pay-nl/commit/040e117b3d0ad277ac37e000d970e814a5fa2b2c))
- Updated .gitattributes ([aa8365e](https://github.com/pronamic/wp-pronamic-pay-pay-nl/commit/aa8365e454566a3b226023bacdb18484e0d1e066))

Full set of changes: [`4.5.2...4.5.3`][4.5.3]

[4.5.3]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/v4.5.2...v4.5.3

## [4.5.2] - 2023-03-27

### Commits

- Set Composer type to WordPress plugin. ([6d9c8f5](https://github.com/pronamic/wp-pronamic-pay-pay-nl/commit/6d9c8f5e27eb37b73288b2d6865440a9f823e40b))
- Updated .gitattributes ([1d78daa](https://github.com/pronamic/wp-pronamic-pay-pay-nl/commit/1d78daa757b292cf773a5db22998dded469867a2))

Full set of changes: [`4.5.1...4.5.2`][4.5.2]

[4.5.2]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/v4.5.1...v4.5.2

## [4.5.1] - 2023-01-31
### Composer

- Changed `php` from `>=8.0` to `>=7.4`.
Full set of changes: [`4.5.0...4.5.1`][4.5.1]

[4.5.1]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/v4.5.0...v4.5.1

## [4.5.0] - 2022-12-23

### Commits

- Register Riverty payment method. ([0a7c780](https://github.com/pronamic/wp-pronamic-pay-pay-nl/commit/0a7c78006fc119de31dd8b0df97c58417782795f))
- Removed usage of deprecated `\FILTER_SANITIZE_STRING` in gateway settings fields. ([85d9e1c](https://github.com/pronamic/wp-pronamic-pay-pay-nl/commit/85d9e1c8a5dde898939d339b1b71f19352b3d21e))

### Composer

- Changed `php` from `>=5.6.20` to `>=8.0`.
- Changed `wp-pay/core` from `^4.5` to `v4.6.0`.
	Release notes: https://github.com/pronamic/wp-pay-core/releases/tag/v4.4.0
Full set of changes: [`4.4.0...4.5.0`][4.5.0]

[4.5.0]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/v4.4.0...v4.5.0

## [4.4.0] - 2022-12-01
- Updated to REST API version 13: https://rest-api.pay.nl/v13/.
- Added `statsData` to transaction requests. [#18](https://github.com/pronamic/pronamic-pay/issues/18)

## [4.3.0] - 2022-11-07
- Updated dashboard URL to https://my.pay.nl/. [#3](https://github.com/pronamic/wp-pronamic-pay-pay-nl/pull/3)
- Added payment provider URL filter. [#3](https://github.com/pronamic/wp-pronamic-pay-pay-nl/pull/3)
- Update integration name from "Pay.nl" to "Pay.". [#2](https://github.com/pronamic/wp-pronamic-pay-pay-nl/issues/2)

## [4.2.0] - 2022-09-26
- Updated payment methods registration.

## [4.1.0] - 2022-04-11
- Remove gateway error usage, exception should be handled downstream.

## [4.0.0] - 2022-01-11
### Changed
- Updated to https://github.com/pronamic/wp-pay-core/releases/tag/4.0.0.

## [3.0.1] - 2021-08-24
- Fixed "Fatal error: Uncaught Error: Call to undefined method Pronamic\WordPress\Money\Money::get_including_tax()".

## [3.0.0] - 2021-08-05
- Updated to `pronamic/wp-pay-core` version `3.0.0`.
- Updated to `pronamic/wp-money` version `2.0.0`.
- Switched to `pronamic/wp-coding-standards`.
- Added support for SprayPay payment method.

## [2.1.2] - 2021-04-26
- Happy 2021.

## [2.1.1] - 2020-11-09
- Limited first and last name to 32 characters.

## [2.1.0] - 2020-03-19
- Extend from AbstractGatewayIntegration class.

## [2.0.4] - 2019-12-22
- Added URL to manual in gateway settings.
- Improved error handling with exceptions.
- Updated payment status class name.

## [2.0.3] - 2019-08-28
- Updated packages.

## [2.0.2] - 2019-02-04
- Fix error 'invalid paymentProfileId or amount' if no payment method is specified.

## [2.0.1] - 2018-12-12
- Added support for payment lines, shipping, billing, customer data.
- Added support for AfterPay, Focum, In3, Klarna Pay Later.
- Use issuer field from core gateway.

## [2.0.0] - 2018-05-11
- Switched to PHP namespaces.

## [1.1.8] - 2017-12-12
- Set transaction description.

## [1.1.7] - 2016-10-20
- Added `payment_status_request` feature support.
- Fixed "urlencode should only be used when dealing with legacy applications, rawurlencode() should now be used instead.
- Removed schedule status check event, this will be part of the Pronamic iDEAL plugin.
- Added end user name and e-mail address to transaction.
- Added support new Bancontact constant.
- Don't schedule `pronamic_ideal_check_transaction_status` event on transaction error.

## [1.1.6] - 2016-06-08
- Simplified the gateway payment start function.

## [1.1.5] - 2016-05-06
- Improved error handling.

## [1.1.4] - 2016-03-22
- Added scheduled transaction status request.
- Updated gateway settings.
- Added product and dashboard URLs.

## [1.1.3] - 2016-03-02
- Added get settings function.
- Moved get_gateway_class() function to the configuration class.
- Removed get_config_class(), no longer required.

## [1.1.2] - 2016-02-1
- Added an gateway settings class.

## [1.1.1] - 2015-03-26
- Updated WordPress pay core library to version 1.2.0.

## [1.1.0] - 2015-02-27
- Updated WordPress pay core library to version 1.1.0.
- Fixed issues with filter_input INPUT_SERVER (https://bugs.php.net/bug.php?id=49184).

## [1.0.3] - 2015-01-20
- Require WordPress pay core library version 1.0.0.

## [1.0.2] - 2014-12-16
- Fix - fixed issue with payment status update handling.

## [1.0.1] - 2014-12-15
- Fix - Fixed issue with start transaction response handling.

## 1.0.0 - 2014-12-15
- First release.

[unreleased]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/4.4.0...HEAD
[4.4.0]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/4.3.0...4.4.0
[4.3.0]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/4.2.0...4.3.0
[4.2.0]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/4.1.0...4.2.0
[4.1.0]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/4.0.0...4.1.0
[4.0.0]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/3.0.1...4.0.0
[3.0.1]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/2.1.2...3.0.0
[2.1.2]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/2.1.1...2.1.2
[2.1.1]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/2.0.4...2.1.0
[2.0.4]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/2.0.3...2.0.4
[2.0.3]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/2.0.2...2.0.3
[2.0.2]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/1.1.8...2.0.0
[1.1.8]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/1.1.7...1.1.8
[1.1.7]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/1.1.6...1.1.7
[1.1.6]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/1.1.5...1.1.6
[1.1.5]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/1.1.4...1.1.5
[1.1.4]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/1.1.3...1.1.4
[1.1.3]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/1.0.3...1.1.0
[1.0.3]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/pronamic/wp-pronamic-pay-pay-nl/compare/1.0.0...1.0.1
