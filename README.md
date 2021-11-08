# GitHelper

## Notes

This bundle is a little tool that sometimes help to see who append and when happened the last commit pushed to the current branch.  
Minimal Symfony compatibility : `5.2`

## Installation

`composer require darkikim/git-helper`

## Add this amazing bundle to bundle.php

If you don't have Symfony Flex :
```php
// config/bundles.php

return [
    //...
    Kikim\GitHelper\GitHelperBundle::class => ['dev' => true],
    //...
];
```

See for yourself in the Symfony Toolbar this amazing bundle !
