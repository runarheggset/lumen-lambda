# lumen-lambda
 
[![Latest Stable Version](https://img.shields.io/github/release/Runar1/lumen-lambda.svg)](https://packagist.org/packages/runar1/lumen-lambda) [![Total Downloads](https://img.shields.io/packagist/dm/Runar1/lumen-lambda.svg)](https://packagist.org/packages/runar1/lumen-lambda)

A package for running lumen on AWS Lambda.
 
## Installation

Installation using composer:
```
composer require runar1/lumen-lambda
```

And add the service provider in `bootstrap/app.php`:
```php
$app->register(Runar1\Lumen\LumenServiceProvider::class);
```

## Usage

The project comes with a prebuilt php-cgi-7.1.4. Feel free to build your own.

- Create a lambda function on aws.
- Make sure lumen is writing to /tmp or not at all.
- Make sure to chmod 777 php-cgi.
- Point aws API Gateway to your newly generated lambda function.

For more indepth tips, read this excellent blog post: https://cwhite.me/hosting-a-laravel-application-on-aws-lambda/.

## Contributing
 
1. Fork it!
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request :D
 
## History
 
Version 1.0 (2017-05-02) - Initial features
 
## Credits
 
Chris White (https://cwhite.me/)
 
## License
 
The MIT License (MIT)

Copyright (c) 2017 Runar Heggset

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.