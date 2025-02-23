<?php

declare(strict_types=1);

use Plakhin\FpmOptimize\Commands\SuggestFpmConfigValues;

arch()->preset()->php();
arch()->preset()->security();
arch()->preset()->strict()->ignoring(SuggestFpmConfigValues::class);
