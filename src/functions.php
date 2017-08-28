<?php

namespace xterr\ClassNames;

if (!function_exists('xterr\ClassNames\classNames'))
{
    function classNames()
    {
        return (new ClassNames)(func_get_args());
    }
}

if (!function_exists('xterr\ClassNames\classNamesConditions'))
{
    function classNamesConditions()
    {
        return (new ClassNames)->asConditions(func_get_args());
    }
}

