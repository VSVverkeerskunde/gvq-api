<?php

namespace VSV\GVQ_API\Common;

interface CsvData
{
    public function rows(): iterable;
}
