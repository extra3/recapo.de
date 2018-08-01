# recapo extended
This is a fork of [https://github.com/lherich/recapo.de](https://github.com/lherich/recapo.de).

## Additional features

* side navigation (aside) on both sides
* blur text in article
* different layout options for article text
* use of bannerfile in header
* use of images in article
  * optional blur/grayscale
* installation instructions for XAMPP (see [./install/howto-install-recapo-with-xampp.md](./install/howto-install-recapo-with-xampp.md)).
* fix: export of informationarchitecture
* several fixes

# recapo.de
A webapplication for usability testing. Recapo offers an abstracted and partial formated test environment for reverse card sorting tests. These tests are also know as:
* Tree tresting
* Card based classification evaluation
* Inverse card sorting
* Reverse card lookup

### System requirements

* web server with php support
* PHP = 5.x (not PHP 7)
* MySQL server >= 5.5.0 for results in seconds
* MySQL server >= 5.6.4 for results in mikroseconds
                  recapo will use fractional seconds, which are available since version 5.6.4, see [mysql.com](http://dev.mysql.com/doc/refman/5.6/en/fractional-seconds.html)
* optional: "ImageMagick" and php extension "imagick" installed

### Installation

Please see [./install/*](install/).

### Changelog
Please see [CHANGELOG.md](CHANGELOG.md).

### Author
Recapo was created in 2014 by Lucas Herich <info@recapo.de>.

### License
Recapo is released under the MIT public license.
Please see [LICENSE](LICENSE).
