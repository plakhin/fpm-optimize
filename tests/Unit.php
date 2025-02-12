<?php

use Plakhin\PPSkeleton\MyClass;

it('foo', function (): void {
    expect((new MyClass)->foo())->toBe('bar');
});
