# payum-payeezy
Payum Payeezy integration.

Setup PayumBuilder:
```php
<?php

use Payum\Core\PayumBuilder;
use Payum\Core\GatewayFactoryInterface;

$payum = (new PayumBuilder)
    ->addGatewayFactory('payeezy', function((array $config, $coreGatewayFactory) {
        return new \Payum\Payeezy\PayeezyGatewayFactory($config, $coreGatewayFactory);
    })

    ->addGateway('payeezy', [
        "apiKey": "",
        "apiSecret": "",
        "merchantToken": "fdoa-xxx",
        "sandbox": true
    ])

    ->getPayum()
;
```


Use Gateway:
```php
<?php

use Payum\Core\Request\Capture;

$payeezy = $payum->getGateway('payeezy');

$model = new \ArrayObject([
  // ...
]);

$payeezy->execute(new Capture($model));
```


# License
The MIT License (MIT)

Copyright (c) 2016 noogen

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
