# Change Log

All notable changes to this project will be documented in this file.

This projects adheres to [Semantic Versioning](http://semver.org/) and [Keep a CHANGELOG](http://keepachangelog.com/).

## [Unreleased][unreleased]
-

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

[unreleased]: https://github.com/wp-pay-gateways/pay-nl/compare/4.1.0...HEAD
[4.1.0]: https://github.com/wp-pay-gateways/pay-nl/compare/4.0.0...4.1.0
[4.0.0]: https://github.com/wp-pay-gateways/pay-nl/compare/3.0.1...4.0.0
[3.0.1]: https://github.com/wp-pay-gateways/pay-nl/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/wp-pay-gateways/pay-nl/compare/2.1.2...3.0.0
[2.1.2]: https://github.com/wp-pay-gateways/pay-nl/compare/2.1.1...2.1.2
[2.1.1]: https://github.com/wp-pay-gateways/pay-nl/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/wp-pay-gateways/pay-nl/compare/2.0.4...2.1.0
[2.0.4]: https://github.com/wp-pay-gateways/pay-nl/compare/2.0.3...2.0.4
[2.0.3]: https://github.com/wp-pay-gateways/pay-nl/compare/2.0.2...2.0.3
[2.0.2]: https://github.com/wp-pay-gateways/pay-nl/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/wp-pay-gateways/pay-nl/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/wp-pay-gateways/pay-nl/compare/1.1.8...2.0.0
[1.1.8]: https://github.com/wp-pay-gateways/pay-nl/compare/1.1.7...1.1.8
[1.1.7]: https://github.com/wp-pay-gateways/pay-nl/compare/1.1.6...1.1.7
[1.1.6]: https://github.com/wp-pay-gateways/pay-nl/compare/1.1.5...1.1.6
[1.1.5]: https://github.com/wp-pay-gateways/pay-nl/compare/1.1.4...1.1.5
[1.1.4]: https://github.com/wp-pay-gateways/pay-nl/compare/1.1.3...1.1.4
[1.1.3]: https://github.com/wp-pay-gateways/pay-nl/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/wp-pay-gateways/pay-nl/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/wp-pay-gateways/pay-nl/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/wp-pay-gateways/pay-nl/compare/1.0.3...1.1.0
[1.0.3]: https://github.com/wp-pay-gateways/pay-nl/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/wp-pay-gateways/pay-nl/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/wp-pay-gateways/pay-nl/compare/1.0.0...1.0.1
