# Due date calculator in PHP

This project is a due date calculator library written in PHP.

## Structure

```
bin/   - executable files for Composer
src/   - library source
tests/ - Executable files for Composer
```

## Features

The program reads the currently reported problems (bugs) from an issue tracking system and calculates the due date following the rules below: 
- Working hours can be specified. Defaults are **9AM** to **5PM** every working day (Monday through Friday) 
- The program does not deal with holidays, which means that a holiday on a Thursday is still considered as a working day by the program. Also a working Saturday will still be considered as a nonworking day by the system. 
- The turnaround time is given in working hours, which means for example that 2 days are 16 hours. As an example: if a problem was reported at 2:12PM on Tuesday then it is due by 2:12PM on Thursday. 
- All submitted **date values must be between working hours**.

## Usage

### As a library

```bash
composer require shakahl/due-php
```

```php
use Shakahl\Due\DueDateCalculator;

$calculator = DueDateCalculator::make();

$calculator->setDayStart(9);
$calculator->setDayEnd(9);

$dueDate = $calculator->calculate('2018-05-11 11:23:42', 7);

echo $dueDate->format('c'); // 2018-05-11T13:23:42+00:00

```

### CLI - Command line interface

```bash
composer global require shakahl/due-php
due help
```

## Testing

```bash
$ composer test
```

## Credits

- [Soma Szélpál][link-author]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/shakahl/due-php.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/shakahl/due-php/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/shakahl/due-php.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/shakahl/due-php.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/shakahl/due-php.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/shakahl/due-php
[link-travis]: https://travis-ci.org/shakahl/due-php
[link-scrutinizer]: https://scrutinizer-ci.com/g/shakahl/due-php/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/shakahl/due-php
[link-downloads]: https://packagist.org/packages/shakahl/due-php
[link-author]: https://github.com/shakahl
[link-contributors]: ../../contributors
