<?php

namespace xterr\ClassNames;

if (!function_exists('xterr\ClassNames\classNames'))
{
    function classNames()
    {
        return (new ClassNames)(func_get_args());
    }
}


