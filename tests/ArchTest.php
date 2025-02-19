<?php

use Plakhin\FpmOptimize\Commands\SuggestFpmConfigValues;

arch()->preset()->php();
arch()->preset()->strict()->ignoring(SuggestFpmConfigValues::class);
